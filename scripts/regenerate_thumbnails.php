<?php

declare(strict_types=1);

require __DIR__ . '/../public/src/bootstrap.php';

$images = read_json(DATA_PATH . '/images.json');
$updated = false;

foreach ($images as $index => $record) {
    $record = normalize_image_record((array) $record);
    $original = basename((string) ($record['original'] ?? ''));
    if ($original === '') {
        continue;
    }

    $sourcePath = ORIGINALS_PATH . '/' . $original;
    if (!is_file($sourcePath)) {
        continue;
    }

    $thumb = basename((string) ($record['thumb'] ?? ''));
    if ($thumb === '') {
        $thumb = (string) ($record['id'] ?? bin2hex(random_bytes(4))) . '.jpg';
    }

    $thumbSmall = basename((string) ($record['thumb_small'] ?? ''));
    if ($thumbSmall === '') {
        $thumbSmall = preg_replace('/\.jpg$/', '', $thumb) . '-sm.jpg';
    }

    generate_thumbnail($sourcePath, THUMBS_PATH . '/' . $thumb, 800, 500);
    generate_thumbnail($sourcePath, THUMBS_PATH . '/' . $thumbSmall, 400, 250);

    $images[$index]['thumb'] = $thumb;
    $images[$index]['thumb_small'] = $thumbSmall;
    $updated = true;
}

if ($updated) {
    write_json(DATA_PATH . '/images.json', $images);
}

echo "Regenerated thumbnails for " . count($images) . " records.\n";
