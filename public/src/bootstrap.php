<?php

declare(strict_types=1);

session_start();

define('ROOT_PATH', dirname(__DIR__, 1));
define('PROJECT_PATH', dirname(ROOT_PATH));
define('STORAGE_PATH', PROJECT_PATH . '/storage');
define('DATA_PATH', STORAGE_PATH . '/data');
define('ORIGINALS_PATH', STORAGE_PATH . '/images/original');
define('THUMBS_PATH', STORAGE_PATH . '/images/thumbs');
define('WIKI_CACHE_TTL_SECONDS', 7 * 24 * 60 * 60);

require_once ROOT_PATH . '/src/services/wikipedia.php';

function load_config(): array
{
    return [
        'site_name' => getenv('SITE_NAME') ?: 'Night Sky Atlas',
        'admin_route' => getenv('ADMIN_ROUTE') ?: '/hidden-admin',
        'max_upload_bytes' => (int) (getenv('MAX_UPLOAD_BYTES') ?: 10 * 1024 * 1024),
        'allowed_mime' => ['image/jpeg', 'image/png', 'image/webp'],
    ];
}

function read_json(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    return is_array($decoded) ? $decoded : [];
}

function write_json(string $path, array $data): void
{
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function image_records(): array
{
    $records = read_json(DATA_PATH . '/images.json');
    $records = array_map('normalize_image_record', $records);
    usort($records, fn(array $a, array $b): int => strcmp($b['captured_at'] ?? '', $a['captured_at'] ?? ''));
    return $records;
}

function normalize_image_record(array $record): array
{
    $record['wikipediaUrl'] = trim((string) ($record['wikipediaUrl'] ?? ''));
    $record['wikiTitle'] = trim((string) ($record['wikiTitle'] ?? ''));
    $record['wikiExtract'] = trim((string) ($record['wikiExtract'] ?? ''));
    $record['wikiThumbnail'] = trim((string) ($record['wikiThumbnail'] ?? ''));
    $record['wikiFetchedAt'] = trim((string) ($record['wikiFetchedAt'] ?? ''));
    $record['wikiStatus'] = trim((string) ($record['wikiStatus'] ?? 'not_requested'));

    return $record;
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

function render(string $view, array $vars = []): void
{
    $config = load_config();
    extract($vars);
    require ROOT_PATH . '/src/views/layout_top.php';
    require ROOT_PATH . '/src/views/' . $view . '.php';
    require ROOT_PATH . '/src/views/layout_bottom.php';
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
    return !empty($_SESSION['admin_authenticated']);
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

function authenticate(string $username, string $password): bool
{
    foreach (users() as $user) {
        if (($user['username'] ?? '') === $username && password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_user'] = $username;
            return true;
        }
    }

    return false;
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

function normalize_wikipedia_url(string $url): ?string
{
    $trimmed = trim($url);
    if ($trimmed === '') {
        return null;
    }

    if (!filter_var($trimmed, FILTER_VALIDATE_URL)) {
        throw new RuntimeException('Wikipedia URL must be a valid URL.');
    }

    $parts = parse_url($trimmed);
    $host = strtolower((string) ($parts['host'] ?? ''));
    $path = (string) ($parts['path'] ?? '');

    if ($host === '' || !preg_match('/(^|\.)wikipedia\.org$/', $host)) {
        throw new RuntimeException('Wikipedia URL must point to a wikipedia.org domain.');
    }

    if (strpos($path, '/wiki/') !== 0 || strlen($path) <= 6) {
        throw new RuntimeException('Wikipedia URL must point to a specific Wikipedia article.');
    }

    return $trimmed;
}

function wikipedia_summary_from_url(string $url): array
{
    $validated = normalize_wikipedia_url($url);
    if ($validated === null) {
        throw new RuntimeException('Wikipedia URL is required for preview.');
    }

    $parts = parse_url($validated);
    $scheme = (($parts['scheme'] ?? 'https') === 'http') ? 'http' : 'https';
    $host = (string) ($parts['host'] ?? 'en.wikipedia.org');
    $path = (string) ($parts['path'] ?? '');
    $title = rawurldecode(substr($path, 6));

    if ($title === '' || strpos($title, ':') !== false) {
        throw new RuntimeException('Wikipedia URL must reference a standard article page.');
    }

    $apiUrl = sprintf('%s://%s/api/rest_v1/page/summary/%s', $scheme, $host, rawurlencode($title));
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 5,
            'header' => "User-Agent: NightSkyAtlas/1.0 (+https://localhost)\r\n",
        ],
    ]);

    $response = @file_get_contents($apiUrl, false, $context);
    if ($response === false) {
        throw new RuntimeException('Could not fetch data from Wikipedia.');
    }

    $payload = json_decode($response, true);
    if (!is_array($payload) || isset($payload['type']) && $payload['type'] === 'https://mediawiki.org/wiki/HyperSwitch/errors/not_found') {
        throw new RuntimeException('Wikipedia article not found.');
    }

    $extract = trim((string) ($payload['extract'] ?? ''));
    $firstParagraph = $extract;
    if (strpos($extract, "\n\n") !== false) {
        $firstParagraph = trim((string) explode("\n\n", $extract)[0]);
    }

    return [
        'title' => (string) ($payload['title'] ?? $title),
        'extract' => $firstParagraph,
        'thumbnail' => (string) (($payload['thumbnail']['source'] ?? '')),
        'canonical_url' => (string) (($payload['content_urls']['desktop']['page'] ?? $validated)),
    ];
}
