<?php

declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$config = load_config();
$adminBase = $config['admin_route'];

if ($path === '/') {
    render('home', ['title' => $config['site_name'], 'images' => image_records()]);
    exit;
}

if ($path === '/about') {
    render('about', ['title' => 'About']);
    exit;
}

if ($path === '/image.php') {
    $image = isset($_GET['id']) ? find_image((string) $_GET['id']) : null;
    if ($image === null) {
        http_response_code(404);
        echo 'Image not found';
        exit;
    }

    $wikipediaData = null;
    $wikipediaError = null;
    if (!empty($image['wikipedia_url'])) {
        try {
            $wikipediaData = wikipedia_summary_from_url((string) $image['wikipedia_url']);
        } catch (Throwable $throwable) {
            $wikipediaError = $throwable->getMessage();
        }
    }

    $detailPath = '/image.php?id=' . rawurlencode((string) $image['id']);
    $detailUrl = absolute_url($detailPath);
    $ogDescription = trim((string) ($image['description'] ?? ''));
    if ($ogDescription === '') {
        $ogDescription = 'Astrophotography capture: ' . (string) ($image['title'] ?? 'Untitled image');
    }

    render('detail', [
        'title' => $image['title'],
        'image' => $image,
        'wikipedia_data' => $wikipediaData,
        'wikipedia_error' => $wikipediaError,
        'canonical_url' => $detailUrl,
        'meta_title' => $image['title'] . ' · ' . $config['site_name'],
        'meta_description' => $ogDescription,
        'meta_image' => absolute_url('/media.php?type=thumb&file=' . rawurlencode((string) $image['thumb'])),
        'meta_image_type' => 'image/jpeg',
        'meta_image_width' => 800,
        'meta_image_height' => 500,
    ]);
    exit;
}

if ($path === '/media.php') {
    $file = basename((string) ($_GET['file'] ?? ''));
    $type = (string) ($_GET['type'] ?? 'thumb');
    $base = $type === 'original' ? ORIGINALS_PATH : THUMBS_PATH;
    $target = $base . '/' . $file;
    if (!is_file($target)) {
        http_response_code(404);
        exit;
    }

    $mime = mime_content_type($target) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=604800');
    readfile($target);
    exit;
}

if ($path === $adminBase . '/login') {
    $error = null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf()) {
            $error = 'Invalid CSRF token.';
        } elseif (too_many_attempts($ip)) {
            $error = 'Too many attempts. Please wait and retry.';
        } else {
            $rememberMe = !empty($_POST['remember_me']);
            if (authenticate((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''), $rememberMe)) {
                clear_failed_attempts($ip);
                header('Location: ' . $adminBase . '/upload');
                exit;
            }

            register_failed_attempt($ip);
            $error = 'Invalid credentials.';
        }
    }

    render('login', ['title' => 'Admin Login', 'error' => $error]);
    exit;
}

if ($path === $adminBase . '/logout') {
    logout_admin();
    header('Location: /');
    exit;
}

$adminSections = [
    $adminBase . '/upload' => 'upload',
    $adminBase . '/setup-presets' => 'setup_presets',
    $adminBase . '/manage-images' => 'manage_images',
    $adminBase . '/security' => 'security',
];

if (isset($adminSections[$path])) {
    require_admin();

    $adminSection = $adminSections[$path];
    $error = null;
    $limitError = null;
    $effectiveUploadLimit = effective_upload_limit_bytes();
    $success = null;
    $passwordError = null;
    $passwordSuccess = null;
    $wikipediaPreview = null;
    $wikipediaUrlInput = (string) ($_POST['wikipedia_url'] ?? '');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requestLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($requestLength > 0 && $effectiveUploadLimit > 0 && $requestLength > $effectiveUploadLimit && empty($_POST) && empty($_FILES)) {
            $limitError = sprintf('Upload request is too large (%s). Current server/app limit is %s. Increase post_max_size/upload_max_filesize and MAX_UPLOAD_BYTES to allow larger uploads.', format_bytes_human($requestLength), format_bytes_human($effectiveUploadLimit));
        } elseif (!verify_csrf()) {
            $error = 'Invalid CSRF token.';
        } else {
            $formAction = (string) ($_POST['form_action'] ?? '');

            if ($formAction === 'change_password') {
                $currentPassword = (string) ($_POST['current_password'] ?? '');
                $newPassword = (string) ($_POST['new_password'] ?? '');
                $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

                if ($newPassword !== $confirmPassword) {
                    $passwordError = 'New password and confirmation do not match.';
                } else {
                    $username = (string) ($_SESSION['admin_user'] ?? '');
                    $updateError = update_user_password($username, $currentPassword, $newPassword);
                    if ($updateError === null) {
                        $passwordSuccess = 'Password updated successfully.';
                    } else {
                        $passwordError = $updateError;
                    }
                }
            } elseif ($formAction === 'delete_image') {
                $imageId = (string) ($_POST['image_id'] ?? '');
                if (delete_image_by_id($imageId)) {
                    $success = 'Image deleted successfully.';
                } else {
                    $error = 'Image could not be deleted (record not found).';
                }
            } elseif ($formAction === 'add_setup_preset') {
                $presetCategory = (string) ($_POST['preset_category'] ?? '');
                $presetValue = (string) ($_POST['preset_value'] ?? '');
                if (add_setup_preset($presetCategory, $presetValue)) {
                    $success = 'Preset saved.';
                } else {
                    $error = 'Please choose a category and enter a valid preset value.';
                }
            } elseif ($formAction === 'delete_setup_preset') {
                $presetCategory = (string) ($_POST['preset_category'] ?? '');
                $presetValue = (string) ($_POST['preset_value'] ?? '');
                if (delete_setup_preset($presetCategory, $presetValue)) {
                    $success = 'Preset removed.';
                } else {
                    $error = 'Preset was not found.';
                }
            } elseif (($formAction === 'upload_image_preview') && trim($wikipediaUrlInput) !== '') {
                try {
                    $wikipediaPreview = wikipedia_summary_from_url($wikipediaUrlInput);
                } catch (Throwable $throwable) {
                    $error = $throwable->getMessage();
                }
            } elseif ($formAction === 'upload_image') {
                if (empty($_FILES['image']) || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                    $error = 'Image upload failed. Please try again.';
                } else {
                    try {
                        $media = save_uploaded_image($_FILES['image']);

                        $images = image_records();
                        $images[] = [
                            'id' => $media['id'],
                            'original' => $media['original'],
                            'thumb' => $media['thumb'],
                            'title' => trim((string) ($_POST['title'] ?? '')),
                            'object_name' => trim((string) ($_POST['object_name'] ?? '')),
                            'object_type' => trim((string) ($_POST['object_type'] ?? '')),
                            'captured_at' => trim((string) ($_POST['captured_at'] ?? '')),
                            'description' => trim((string) ($_POST['description'] ?? '')),
                            'scope_type' => trim((string) ($_POST['scope_type'] ?? '')),
                            'telescope' => trim((string) ($_POST['telescope'] ?? '')),
                            'mount' => trim((string) ($_POST['mount'] ?? '')),
                            'camera' => trim((string) ($_POST['camera'] ?? '')),
                            'filter_wheel' => trim((string) ($_POST['filter_wheel'] ?? '')),
                            'filters' => trim((string) ($_POST['filters'] ?? '')),
                            'filter_set' => trim((string) ($_POST['filter_set'] ?? '')),
                            'equipment' => compose_equipment_summary([
                                'telescope' => (string) ($_POST['telescope'] ?? ''),
                                'mount' => (string) ($_POST['mount'] ?? ''),
                                'camera' => (string) ($_POST['camera'] ?? ''),
                                'filter_wheel' => (string) ($_POST['filter_wheel'] ?? ''),
                                'filters' => (string) ($_POST['filters'] ?? ''),
                                'filter_set' => (string) ($_POST['filter_set'] ?? ''),
                            ]),
                            'exposure' => trim((string) ($_POST['exposure'] ?? '')),
                            'processing' => trim((string) ($_POST['processing'] ?? '')),
                            'wikipedia_url' => normalize_wikipedia_url_for_storage($wikipediaUrlInput),
                            'tags' => array_values(array_filter(array_map('trim', explode(',', (string) ($_POST['tags'] ?? ''))))),
                            'wikipediaUrl' => trim((string) ($_POST['wikipedia_url'] ?? '')),
                            'wikiTitle' => '',
                            'wikiExtract' => '',
                            'wikiThumbnail' => '',
                            'wikiFetchedAt' => '',
                            'wikiStatus' => 'not_requested',
                        ];

                        write_json(DATA_PATH . '/images.json', $images);
                        $success = 'Image uploaded successfully.';
                        $wikipediaUrlInput = '';
                    } catch (Throwable $throwable) {
                        $error = $throwable->getMessage();
                    }
                }
            }
        }
    }

    render('upload', [
        'title' => 'Admin Portal',
        'admin_section' => $adminSection,
        'error' => $error,
        'limit_error' => $limitError,
        'success' => $success,
        'effective_upload_limit_human' => format_bytes_human($effectiveUploadLimit),
        'password_error' => $passwordError,
        'password_success' => $passwordSuccess,
        'wikipedia_url' => $wikipediaUrlInput,
        'wikipedia_preview' => $wikipediaPreview,
        'images' => image_records(),
        'setup_preset_categories' => setup_preset_categories(),
        'setup_presets' => setup_presets(),
        'storage_summary' => storage_space_summary(),
    ]);
    exit;
}

http_response_code(404);
echo 'Page not found';
