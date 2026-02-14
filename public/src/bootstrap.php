<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__, 1));
define('PROJECT_PATH', dirname(ROOT_PATH));
define('STORAGE_PATH', PROJECT_PATH . '/storage');
define('DATA_PATH', STORAGE_PATH . '/data');
define('ORIGINALS_PATH', STORAGE_PATH . '/images/original');
define('THUMBS_PATH', STORAGE_PATH . '/images/thumbs');
define('WIKI_CACHE_TTL_SECONDS', 7 * 24 * 60 * 60);

$sessionPath = STORAGE_PATH . '/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0775, true);
}

if (is_dir($sessionPath) && is_writable($sessionPath)) {
    session_save_path($sessionPath);
}

session_start();

require_once ROOT_PATH . '/src/services/wikipedia.php';

const ADMIN_REMEMBER_COOKIE = 'admin_remember';
const ADMIN_REMEMBER_DAYS = 30;

function load_config(): array
{
    return [
        'site_name' => getenv('SITE_NAME') ?: 'Night Sky Atlas',
        'admin_route' => getenv('ADMIN_ROUTE') ?: '/hidden-admin',
        'max_upload_bytes' => (int) (getenv('MAX_UPLOAD_BYTES') ?: 150 * 1024 * 1024),
        'allowed_mime' => ['image/jpeg', 'image/png', 'image/webp'],
    ];
}

function php_ini_bytes(string $value): int
{
    $value = trim($value);
    if ($value == '') {
        return 0;
    }

    $lastChar = strtolower(substr($value, -1));
    $number = (float) $value;

    switch ($lastChar) {
        case 'g':
            $number *= 1024;
            // no break
        case 'm':
            $number *= 1024;
            // no break
        case 'k':
            $number *= 1024;
    }

    return (int) $number;
}

function effective_upload_limit_bytes(): int
{
    $uploadMax = php_ini_bytes((string) ini_get('upload_max_filesize'));
    $postMax = php_ini_bytes((string) ini_get('post_max_size'));
    $appMax = load_config()['max_upload_bytes'];

    $limits = array_filter([$uploadMax, $postMax, $appMax], static fn(int $bytes): bool => $bytes > 0);
    if (empty($limits)) {
        return $appMax;
    }

    return min($limits);
}

function format_bytes_human(int $bytes): string
{
    if ($bytes <= 0) {
        return '0 B';
    }

    $units = ['B', 'KB', 'MB', 'GB'];
    $power = (int) floor(log($bytes, 1024));
    $power = min($power, count($units) - 1);
    $value = $bytes / (1024 ** $power);

    return ($power === 0 ? (string) $bytes : number_format($value, 1)) . ' ' . $units[$power];
}

function read_json(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    return is_array($decoded) ? $decoded : [];
}

function write_json(string $path, array $data): bool
{
    $directory = dirname($path);
    if (!is_dir($directory) || !is_writable($directory)) {
        return false;
    }

    $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (!is_string($encoded)) {
        return false;
    }

    return @file_put_contents($path, $encoded, LOCK_EX) !== false;
}

function image_records(): array
{
    $records = read_json(DATA_PATH . '/images.json');
    $records = array_map('normalize_image_record', $records);
    usort($records, fn(array $a, array $b): int => strcmp($b['captured_at'] ?? '', $a['captured_at'] ?? ''));
    return $records;
}

function featured_image(array $records): ?array
{
    foreach ($records as $record) {
        if (!empty($record['is_spotlight'])) {
            return $record;
        }
    }

    return $records[0] ?? null;
}

function setup_preset_categories(): array
{
    return [
        'scope_type' => 'Scope type',
        'object_type' => 'Object type',
        'telescope' => 'Telescope / tube',
        'mount' => 'Mount',
        'camera' => 'Camera',
        'filter_wheel' => 'Filter wheel',
        'filters' => 'Filters',
        'filter_set' => 'Filter set',
    ];
}

function setup_presets(): array
{
    $categories = setup_preset_categories();
    $stored = read_json(DATA_PATH . '/setup_presets.json');

    if (!isset($stored['scope_type']) || !is_array($stored['scope_type']) || empty($stored['scope_type'])) {
        $legacyScopeTypes = read_json(DATA_PATH . '/scope_types.json');
        if (is_array($legacyScopeTypes) && !empty($legacyScopeTypes)) {
            $stored['scope_type'] = $legacyScopeTypes;
        }
    }

    $normalized = [];

    foreach ($categories as $key => $_label) {
        $values = $stored[$key] ?? [];
        if (!is_array($values)) {
            $values = [];
        }

        $clean = [];
        foreach ($values as $value) {
            $text = trim((string) $value);
            if ($text === '') {
                continue;
            }

            $clean[] = $text;
        }

        $clean = array_values(array_unique($clean));
        natcasesort($clean);
        $normalized[$key] = array_values($clean);
    }

    return $normalized;
}

function add_setup_preset(string $category, string $value): bool
{
    $category = trim($category);
    $value = trim($value);
    if ($value === '' || !array_key_exists($category, setup_preset_categories())) {
        return false;
    }

    $presets = setup_presets();
    if (!in_array($value, $presets[$category], true)) {
        $presets[$category][] = $value;
        natcasesort($presets[$category]);
        $presets[$category] = array_values($presets[$category]);
    }

    return write_json(DATA_PATH . '/setup_presets.json', $presets);
}

function delete_setup_preset(string $category, string $value): bool
{
    $category = trim($category);
    $value = trim($value);
    if ($value === '' || !array_key_exists($category, setup_preset_categories())) {
        return false;
    }

    $presets = setup_presets();
    $remaining = array_values(array_filter($presets[$category], static fn(string $preset): bool => $preset !== $value));
    if (count($remaining) === count($presets[$category])) {
        return false;
    }

    $presets[$category] = $remaining;

    return write_json(DATA_PATH . '/setup_presets.json', $presets);
}

function scope_type_presets(): array
{
    $presets = setup_presets();
    return $presets['scope_type'] ?? [];
}

function add_scope_type_preset(string $value): bool
{
    return add_setup_preset('scope_type', $value);
}

function delete_scope_type_preset(string $value): bool
{
    return delete_setup_preset('scope_type', $value);
}

function normalize_wiki_facts($facts): array
{
    if (!is_array($facts)) {
        return [];
    }

    $normalized = [];
    foreach ($facts as $fact) {
        if (!is_array($fact)) {
            continue;
        }

        $label = trim((string) ($fact['label'] ?? ''));
        $value = trim((string) ($fact['value'] ?? ''));
        if ($label === '' || $value === '') {
            continue;
        }

        $normalized[] = ['label' => $label, 'value' => $value];
    }

    return $normalized;
}

function compose_equipment_summary(array $record): string
{
    $parts = [];
    foreach (['telescope', 'mount', 'camera', 'filter_wheel', 'filters', 'filter_set'] as $field) {
        $value = trim((string) ($record[$field] ?? ''));
        if ($value !== '') {
            $parts[] = $value;
        }
    }

    return implode(' · ', $parts);
}

function normalize_image_record(array $record): array
{
    $record['scope_type'] = trim((string) ($record['scope_type'] ?? ''));
    $record['object_type'] = trim((string) ($record['object_type'] ?? ''));
    $record['telescope'] = trim((string) ($record['telescope'] ?? ''));
    $record['mount'] = trim((string) ($record['mount'] ?? ''));
    $record['camera'] = trim((string) ($record['camera'] ?? ''));
    $record['filter_wheel'] = trim((string) ($record['filter_wheel'] ?? ''));
    $record['filters'] = trim((string) ($record['filters'] ?? ''));
    $record['filter_set'] = trim((string) ($record['filter_set'] ?? ''));
    $record['wikipediaUrl'] = trim((string) ($record['wikipediaUrl'] ?? ''));
    $record['wikiTitle'] = trim((string) ($record['wikiTitle'] ?? ''));
    $record['wikiExtract'] = trim((string) ($record['wikiExtract'] ?? ''));
    $record['wikiThumbnail'] = trim((string) ($record['wikiThumbnail'] ?? ''));
    $record['wikiFetchedAt'] = trim((string) ($record['wikiFetchedAt'] ?? ''));
    $record['wikiStatus'] = trim((string) ($record['wikiStatus'] ?? 'not_requested'));
    $record['wikiFacts'] = normalize_wiki_facts($record['wikiFacts'] ?? []);
    $record['meta_title'] = trim((string) ($record['meta_title'] ?? ''));
    $record['meta_description'] = trim((string) ($record['meta_description'] ?? ''));
    $record['meta_keywords'] = trim((string) ($record['meta_keywords'] ?? ''));
    $record['is_spotlight'] = !empty($record['is_spotlight']);
    $record['equipment'] = trim((string) ($record['equipment'] ?? ''));

    if ($record['equipment'] === '') {
        $record['equipment'] = compose_equipment_summary($record);
    }

    return $record;
}

function set_spotlight_image(string $id): bool
{
    $id = trim($id);
    if ($id === '') {
        return false;
    }

    $images = read_json(DATA_PATH . '/images.json');
    $found = false;

    foreach ($images as $index => $record) {
        $isTarget = (($record['id'] ?? '') === $id);
        $images[$index]['is_spotlight'] = $isTarget;
        if ($isTarget) {
            $found = true;
        }
    }

    if (!$found) {
        return false;
    }

    return write_json(DATA_PATH . '/images.json', $images);
}

function update_image_metadata(string $id, array $input): ?string
{
    $id = trim($id);
    if ($id === '') {
        return 'Image ID is required.';
    }

    $images = read_json(DATA_PATH . '/images.json');
    $targetIndex = null;

    foreach ($images as $index => $record) {
        if (($record['id'] ?? '') === $id) {
            $targetIndex = $index;
            $images[$index] = normalize_image_record($record);
            break;
        }
    }

    if ($targetIndex === null) {
        return 'Image not found.';
    }

    $title = trim((string) ($input['title'] ?? ''));
    $objectName = trim((string) ($input['object_name'] ?? ''));
    $capturedAt = trim((string) ($input['captured_at'] ?? ''));
    if ($title === '' || $objectName === '' || $capturedAt === '') {
        return 'Title, object name, and capture date are required.';
    }

    $tagsInput = trim((string) ($input['tags'] ?? ''));
    $tags = array_values(array_filter(array_map('trim', explode(',', $tagsInput)), static fn(string $tag): bool => $tag !== ''));

    $wikipediaUrl = normalize_wikipedia_url_for_storage((string) ($input['wikipedia_url'] ?? ''));
    if (trim((string) ($input['wikipedia_url'] ?? '')) !== '' && $wikipediaUrl === '') {
        return 'Wikipedia URL must be a valid wikipedia.org/wiki/... article link.';
    }

    $existingWikipediaUrl = trim((string) ($images[$targetIndex]['wikipediaUrl'] ?? ''));
    $wikiUrlChanged = $existingWikipediaUrl !== $wikipediaUrl;

    $images[$targetIndex]['title'] = $title;
    $images[$targetIndex]['object_name'] = $objectName;
    $images[$targetIndex]['object_type'] = trim((string) ($input['object_type'] ?? ''));
    $images[$targetIndex]['captured_at'] = $capturedAt;
    $images[$targetIndex]['description'] = trim((string) ($input['description'] ?? ''));
    $images[$targetIndex]['scope_type'] = trim((string) ($input['scope_type'] ?? ''));
    $images[$targetIndex]['telescope'] = trim((string) ($input['telescope'] ?? ''));
    $images[$targetIndex]['mount'] = trim((string) ($input['mount'] ?? ''));
    $images[$targetIndex]['camera'] = trim((string) ($input['camera'] ?? ''));
    $images[$targetIndex]['filter_wheel'] = trim((string) ($input['filter_wheel'] ?? ''));
    $images[$targetIndex]['filters'] = trim((string) ($input['filters'] ?? ''));
    $images[$targetIndex]['filter_set'] = trim((string) ($input['filter_set'] ?? ''));
    $images[$targetIndex]['equipment'] = compose_equipment_summary($images[$targetIndex]);
    $images[$targetIndex]['exposure'] = trim((string) ($input['exposure'] ?? ''));
    $images[$targetIndex]['processing'] = trim((string) ($input['processing'] ?? ''));
    $images[$targetIndex]['tags'] = $tags;
    $images[$targetIndex]['wikipedia_url'] = $wikipediaUrl;
    $images[$targetIndex]['wikipediaUrl'] = $wikipediaUrl;

    if ($wikiUrlChanged) {
        $images[$targetIndex]['wikiTitle'] = '';
        $images[$targetIndex]['wikiExtract'] = '';
        $images[$targetIndex]['wikiThumbnail'] = '';
        $images[$targetIndex]['wikiFetchedAt'] = '';
        $images[$targetIndex]['wikiStatus'] = $wikipediaUrl === '' ? 'not_requested' : 'pending_refresh';
        $images[$targetIndex]['wikiFacts'] = [];

        if ($wikipediaUrl !== '') {
            try {
                $wikiData = fetch_wikipedia_summary($wikipediaUrl);
                $images[$targetIndex] = array_merge($images[$targetIndex], $wikiData);
            } catch (Throwable $throwable) {
                $images[$targetIndex]['wikiStatus'] = 'error';
                log_event('Wikipedia refresh failed during metadata update for image ' . $id . ': ' . $throwable->getMessage());
            }
        }
    }

    $images[$targetIndex]['meta_title'] = trim((string) ($input['meta_title'] ?? ''));
    $images[$targetIndex]['meta_description'] = trim((string) ($input['meta_description'] ?? ''));
    $images[$targetIndex]['meta_keywords'] = trim((string) ($input['meta_keywords'] ?? ''));

    if (!write_json(DATA_PATH . '/images.json', $images)) {
        return 'Image metadata could not be saved because storage is not writable.';
    }

    return null;
}

function log_event(string $message): void
{
    $line = sprintf("[%s] %s\n", date('c'), $message);
    file_put_contents(STORAGE_PATH . '/logs/app.log', $line, FILE_APPEND);
}

function wiki_refresh_needed(array $record): bool
{
    if (empty($record['wikipediaUrl'])) {
        return false;
    }

    if (empty($record['wikiFetchedAt'])) {
        return true;
    }

    $fetchedAt = strtotime((string) $record['wikiFetchedAt']);
    if ($fetchedAt === false) {
        return true;
    }

    return (time() - $fetchedAt) > WIKI_CACHE_TTL_SECONDS;
}

function wiki_summary_title(string $wikipediaUrl): ?string
{
    $parts = parse_url($wikipediaUrl);
    if (!is_array($parts) || empty($parts['host']) || stripos((string) $parts['host'], 'wikipedia.org') === false) {
        return null;
    }

    $path = (string) ($parts['path'] ?? '');
    if (strpos($path, '/wiki/') !== 0) {
        return null;
    }

    $title = substr($path, strlen('/wiki/'));
    return $title !== '' ? $title : null;
}

function fetch_wikipedia_summary(string $wikipediaUrl): array
{
    $title = wiki_summary_title($wikipediaUrl);
    if ($title === null) {
        throw new RuntimeException('Unsupported Wikipedia URL format.');
    }

    $apiUrl = 'https://en.wikipedia.org/api/rest_v1/page/summary/' . rawurlencode(rawurldecode($title));
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Accept: application/json\r\nUser-Agent: NightSkyAtlas/1.0 (wiki cache refresh)\r\n",
            'timeout' => 5,
        ],
    ]);

    $json = @file_get_contents($apiUrl, false, $context);
    if ($json === false) {
        throw new RuntimeException('Wikipedia summary request failed.');
    }

    $decoded = json_decode($json, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('Invalid Wikipedia summary payload.');
    }

    return [
        'wikiTitle' => trim((string) ($decoded['title'] ?? '')),
        'wikiExtract' => trim((string) ($decoded['extract'] ?? '')),
        'wikiThumbnail' => trim((string) (($decoded['thumbnail']['source'] ?? ''))),
        'wikiFetchedAt' => gmdate('c'),
        'wikiStatus' => 'ok',
        'wikiFacts' => normalize_wiki_facts($decoded['keyFacts'] ?? []),
    ];
}

function refresh_wiki_cache_for_image(string $imageId): void
{
    $images = read_json(DATA_PATH . '/images.json');
    $targetIndex = null;

    foreach ($images as $index => $record) {
        if (($record['id'] ?? '') === $imageId) {
            $targetIndex = $index;
            $images[$index] = normalize_image_record($record);
            break;
        }
    }

    if ($targetIndex === null || !wiki_refresh_needed($images[$targetIndex])) {
        return;
    }

    try {
        $wikiData = fetch_wikipedia_summary((string) $images[$targetIndex]['wikipediaUrl']);
        $images[$targetIndex] = array_merge($images[$targetIndex], $wikiData);
        write_json(DATA_PATH . '/images.json', $images);
    } catch (Throwable $throwable) {
        $images[$targetIndex]['wikiStatus'] = 'error';
        write_json(DATA_PATH . '/images.json', $images);
        log_event('Wikipedia refresh failed for image ' . $imageId . ': ' . $throwable->getMessage());
    }
}

function queue_wiki_refresh(string $imageId): void
{
    static $queued = [];
    if (isset($queued[$imageId])) {
        return;
    }

    $queued[$imageId] = true;
    register_shutdown_function(static function () use ($imageId): void {
        refresh_wiki_cache_for_image($imageId);
    });
}

function find_image(string $id): ?array
{
    foreach (image_records() as $record) {
        if (($record['id'] ?? '') === $id) {
            if (wiki_refresh_needed($record)) {
                queue_wiki_refresh($id);
            }
            return $record;
        }
    }

    return null;
}

function delete_image_by_id(string $id): bool
{
    $id = trim($id);
    if ($id === '') {
        return false;
    }

    $images = read_json(DATA_PATH . '/images.json');
    $remaining = [];
    $deleted = null;

    foreach ($images as $record) {
        if (($record['id'] ?? '') === $id) {
            $deleted = normalize_image_record($record);
            continue;
        }

        $remaining[] = $record;
    }

    if ($deleted === null) {
        return false;
    }

    if (!write_json(DATA_PATH . '/images.json', $remaining)) {
        return false;
    }

    foreach (['original' => ORIGINALS_PATH, 'thumb' => THUMBS_PATH] as $field => $basePath) {
        $filename = basename((string) ($deleted[$field] ?? ''));
        if ($filename === '') {
            continue;
        }

        $targetPath = $basePath . '/' . $filename;
        if (is_file($targetPath)) {
            @unlink($targetPath);
        }
    }

    return true;
}

function storage_space_summary(): ?array
{
    $total = @disk_total_space(PROJECT_PATH);
    $free = @disk_free_space(PROJECT_PATH);

    if (!is_float($total) && !is_int($total)) {
        return null;
    }

    if (!is_float($free) && !is_int($free)) {
        return null;
    }

    $totalBytes = (int) $total;
    $freeBytes = (int) $free;
    $usedBytes = max(0, $totalBytes - $freeBytes);

    return [
        'total_bytes' => $totalBytes,
        'free_bytes' => $freeBytes,
        'used_bytes' => $usedBytes,
        'total_human' => format_bytes_human($totalBytes),
        'free_human' => format_bytes_human($freeBytes),
        'used_human' => format_bytes_human($usedBytes),
    ];
}

function render(string $view, array $vars = []): void
{
    $config = load_config();
    extract($vars);
    require ROOT_PATH . '/src/views/layout_top.php';
    require ROOT_PATH . '/src/views/' . $view . '.php';
    require ROOT_PATH . '/src/views/layout_bottom.php';
}

function request_origin(): string
{
    $forwardedProto = trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    if ($forwardedProto !== '') {
        $scheme = strtolower(explode(',', $forwardedProto)[0]);
    } else {
        $https = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
        $scheme = ($https !== '' && $https !== 'off') ? 'https' : 'http';
    }

    $host = trim((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    return $scheme . '://' . $host;
}

function absolute_url(string $path): string
{
    return request_origin() . $path;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['csrf'];
}

function verify_csrf(): bool
{
    return isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf'] ?? '', (string) $_POST['csrf_token']);
}

function login_attempts_path(): string
{
    return STORAGE_PATH . '/cache/login_attempts.json';
}

function too_many_attempts(string $ip): bool
{
    $attempts = read_json(login_attempts_path());
    $window = 15 * 60;
    $maxAttempts = 5;
    $attempts[$ip] = array_values(array_filter($attempts[$ip] ?? [], fn(int $stamp): bool => time() - $stamp < $window));
    write_json(login_attempts_path(), $attempts);

    return count($attempts[$ip]) >= $maxAttempts;
}

function register_failed_attempt(string $ip): void
{
    $attempts = read_json(login_attempts_path());
    $attempts[$ip] ??= [];
    $attempts[$ip][] = time();
    write_json(login_attempts_path(), $attempts);
}

function clear_failed_attempts(string $ip): void
{
    $attempts = read_json(login_attempts_path());
    unset($attempts[$ip]);
    write_json(login_attempts_path(), $attempts);
}

function is_admin(): bool
{
    restore_admin_from_remember_cookie();
    return !empty($_SESSION['admin_authenticated']);
}

function set_admin_session(string $username): void
{
    $_SESSION['admin_authenticated'] = true;
    $_SESSION['admin_user'] = $username;
}

function forget_admin_session(): void
{
    unset($_SESSION['admin_authenticated'], $_SESSION['admin_user'], $_SESSION['csrf']);
}

function request_is_https(): bool
{
    $forwardedProto = trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    if ($forwardedProto !== '') {
        $proto = strtolower(explode(',', $forwardedProto)[0]);
        return $proto === 'https';
    }

    $https = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
    return $https !== '' && $https !== 'off';
}

function write_remember_cookie(string $username, string $token): void
{
    setcookie(ADMIN_REMEMBER_COOKIE, $username . ':' . $token, [
        'expires' => time() + (ADMIN_REMEMBER_DAYS * 24 * 60 * 60),
        'path' => '/',
        'secure' => request_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clear_remember_cookie(): void
{
    setcookie(ADMIN_REMEMBER_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => request_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function rotate_remember_token(string $username): void
{
    $token = bin2hex(random_bytes(32));
    $expiresAt = gmdate('c', time() + (ADMIN_REMEMBER_DAYS * 24 * 60 * 60));

    $users = users();
    foreach ($users as $index => $user) {
        if (($user['username'] ?? '') !== $username) {
            continue;
        }

        $users[$index]['remember_token_hash'] = hash('sha256', $token);
        $users[$index]['remember_token_expires_at'] = $expiresAt;
        write_json(DATA_PATH . '/users.json', $users);
        write_remember_cookie($username, $token);
        return;
    }
}

function clear_remember_token_for_user(string $username): void
{
    if ($username === '') {
        return;
    }

    $users = users();
    $changed = false;

    foreach ($users as $index => $user) {
        if (($user['username'] ?? '') !== $username) {
            continue;
        }

        if (!empty($users[$index]['remember_token_hash']) || !empty($users[$index]['remember_token_expires_at'])) {
            unset($users[$index]['remember_token_hash'], $users[$index]['remember_token_expires_at']);
            $changed = true;
        }
    }

    if ($changed) {
        write_json(DATA_PATH . '/users.json', $users);
    }
}

function restore_admin_from_remember_cookie(): void
{
    if (!empty($_SESSION['admin_authenticated'])) {
        return;
    }

    $cookie = (string) ($_COOKIE[ADMIN_REMEMBER_COOKIE] ?? '');
    if ($cookie === '' || strpos($cookie, ':') === false) {
        return;
    }

    [$username, $token] = explode(':', $cookie, 2);
    $username = trim($username);
    if ($username === '' || $token === '') {
        clear_remember_cookie();
        return;
    }

    foreach (users() as $user) {
        if (($user['username'] ?? '') !== $username) {
            continue;
        }

        $storedHash = (string) ($user['remember_token_hash'] ?? '');
        $expiresAt = (string) ($user['remember_token_expires_at'] ?? '');
        $expiryTs = strtotime($expiresAt);

        if ($storedHash === '' || $expiryTs === false || $expiryTs < time()) {
            clear_remember_cookie();
            clear_remember_token_for_user($username);
            return;
        }

        if (!hash_equals($storedHash, hash('sha256', $token))) {
            clear_remember_cookie();
            clear_remember_token_for_user($username);
            return;
        }

        set_admin_session($username);
        rotate_remember_token($username);
        return;
    }

    clear_remember_cookie();
}

function logout_admin(): void
{
    $sessionUsername = (string) ($_SESSION['admin_user'] ?? '');
    $cookie = (string) ($_COOKIE[ADMIN_REMEMBER_COOKIE] ?? '');
    $cookieUsername = '';
    if ($cookie !== '' && strpos($cookie, ':') !== false) {
        [$cookieUsername] = explode(':', $cookie, 2);
    }

    clear_remember_cookie();
    clear_remember_token_for_user(trim($sessionUsername) !== '' ? trim($sessionUsername) : trim($cookieUsername));

    forget_admin_session();
    session_regenerate_id(true);
}

function require_admin(): void
{
    if (!is_admin()) {
        header('Location: ' . load_config()['admin_route'] . '/login');
        exit;
    }
}

function users(): array
{
    return read_json(DATA_PATH . '/users.json');
}

function authenticate(string $username, string $password, bool $rememberMe = false): bool
{
    foreach (users() as $user) {
        if (($user['username'] ?? '') === $username && password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            set_admin_session($username);
            if ($rememberMe) {
                rotate_remember_token($username);
            } else {
                clear_remember_cookie();
                clear_remember_token_for_user($username);
            }
            return true;
        }
    }

    return false;
}

function update_user_password(string $username, string $currentPassword, string $newPassword): ?string
{
    $newPassword = trim($newPassword);
    if (strlen($newPassword) < 12) {
        return 'New password must be at least 12 characters.';
    }

    $users = users();
    foreach ($users as $index => $user) {
        $storedUsername = (string) ($user['username'] ?? '');
        $storedHash = (string) ($user['password_hash'] ?? '');
        if ($storedUsername !== $username) {
            continue;
        }

        if (!password_verify($currentPassword, $storedHash)) {
            return 'Current password is incorrect.';
        }

        $users[$index]['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        unset($users[$index]['remember_token_hash'], $users[$index]['remember_token_expires_at']);
        write_json(DATA_PATH . '/users.json', $users);
        clear_remember_cookie();
        return null;
    }

    return 'Admin user account not found.';
}

function save_uploaded_image(array $file): array
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $config = load_config();
    if (!in_array($mime, $config['allowed_mime'], true)) {
        throw new RuntimeException('Unsupported image type.');
    }

    if ($file['size'] > $config['max_upload_bytes']) {
        throw new RuntimeException('Image exceeds upload size limit.');
    }

    switch ($mime) {
        case 'image/jpeg':
            $extension = 'jpg';
            break;
        case 'image/png':
            $extension = 'png';
            break;
        case 'image/webp':
            $extension = 'webp';
            break;
        default:
            throw new RuntimeException('Unsupported image extension.');
    }

    $id = bin2hex(random_bytes(8));
    $originalFilename = $id . '.' . $extension;
    $thumbFilename = $id . '.jpg';

    $destination = ORIGINALS_PATH . '/' . $originalFilename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Failed to store upload.');
    }

    generate_thumbnail($destination, THUMBS_PATH . '/' . $thumbFilename, 800, 500);

    return [
        'id' => $id,
        'original' => $originalFilename,
        'thumb' => $thumbFilename,
        'mime' => $mime,
    ];
}

function generate_thumbnail(string $source, string $target, int $maxWidth, int $maxHeight): void
{
    [$width, $height, $type] = getimagesize($source);
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = (int) max(1, floor($width * $ratio));
    $newHeight = (int) max(1, floor($height * $ratio));

    switch ($type) {
        case IMAGETYPE_JPEG:
            $srcImg = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $srcImg = imagecreatefrompng($source);
            break;
        case IMAGETYPE_WEBP:
            $srcImg = imagecreatefromwebp($source);
            break;
        default:
            throw new RuntimeException('Unsupported source for thumbnail generation.');
    }

    $dstImg = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagejpeg($dstImg, $target, 85);

    imagedestroy($srcImg);
    imagedestroy($dstImg);
}

function normalize_wikipedia_url_for_storage(string $url): ?string
{
    $trimmed = trim($url);
    if ($trimmed === '') {
        return null;
    }

    $normalized = normalize_wikipedia_url($trimmed);
    if (!($normalized['ok'] ?? false)) {
        throw new RuntimeException((string) ($normalized['error']['message'] ?? 'Invalid Wikipedia URL.'));
    }

    return (string) ($normalized['url'] ?? $trimmed);
}

function wikipedia_summary_from_url(string $url): array
{
    $result = fetch_wikipedia_metadata($url);
    if (!($result['ok'] ?? false)) {
        throw new RuntimeException((string) ($result['error']['message'] ?? 'Could not fetch data from Wikipedia.'));
    }

    $data = $result['data'] ?? [];

    return [
        'title' => (string) ($data['title'] ?? ''),
        'extract' => (string) ($data['summary'] ?? ''),
        'thumbnail' => (string) ($data['thumbnail'] ?? ''),
        'canonical_url' => (string) ($data['canonicalUrl'] ?? ''),
        'key_facts' => normalize_wiki_facts($data['keyFacts'] ?? []),
    ];
}
