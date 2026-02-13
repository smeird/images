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

    render('detail', [
        'title' => $image['title'],
        'image' => $image,
        'wikipedia_data' => $wikipediaData,
        'wikipedia_error' => $wikipediaError,
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
        } elseif (authenticate((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''))) {
            clear_failed_attempts($ip);
            header('Location: ' . $adminBase . '/upload');
            exit;
        } else {
            register_failed_attempt($ip);
            $error = 'Invalid credentials.';
        }
    }

    render('login', ['title' => 'Admin Login', 'error' => $error]);
    exit;
}

if ($path === $adminBase . '/logout') {
    session_destroy();
    header('Location: /');
    exit;
}

if ($path === $adminBase . '/upload') {
    require_admin();

    $error = null;
    $success = null;
    $passwordError = null;
    $passwordSuccess = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf()) {
            $error = 'Invalid CSRF token.';
        } elseif ((string) ($_POST['form_action'] ?? '') === 'change_password') {
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
        } elseif (empty($_FILES['image']) || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
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
                    'captured_at' => trim((string) ($_POST['captured_at'] ?? '')),
                    'description' => trim((string) ($_POST['description'] ?? '')),
                    'equipment' => trim((string) ($_POST['equipment'] ?? '')),
                    'exposure' => trim((string) ($_POST['exposure'] ?? '')),
                    'processing' => trim((string) ($_POST['processing'] ?? '')),
                    'wikipedia_url' => normalize_wikipedia_url($wikipediaUrlInput),
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
                $preview = null;
            } catch (Throwable $throwable) {
                $error = $throwable->getMessage();
            }
        }
    }

    render('upload', [
        'title' => 'Admin Upload',
        'error' => $error,
        'success' => $success,
        'password_error' => $passwordError,
        'password_success' => $passwordSuccess,
    ]);
    exit;
}

http_response_code(404);
echo 'Page not found';
