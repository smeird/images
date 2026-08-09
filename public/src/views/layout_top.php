<?php
$canonicalUrl = isset($canonical_url) ? (string) $canonical_url : absolute_url((string) ($_SERVER['REQUEST_URI'] ?? '/'));
$ogTitle = isset($meta_title) ? (string) $meta_title : (string) ($title ?? $config['site_name']);
$ogDescription = isset($meta_description) ? (string) $meta_description : 'Explore astrophotography captures on ' . $config['site_name'] . '.';
$ogImage = isset($meta_image) ? (string) $meta_image : '';
$ogImageType = isset($meta_image_type) ? trim((string) $meta_image_type) : '';
$ogImageWidth = isset($meta_image_width) ? (int) $meta_image_width : 0;
$ogImageHeight = isset($meta_image_height) ? (int) $meta_image_height : 0;
$metaKeywords = isset($meta_keywords) ? trim((string) $meta_keywords) : '';
$currentPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
$pageClass = 'page-' . str_replace('_', '-', (string) ($view ?? 'site'));
$galleryIsCurrent = $currentPath === '/' || $currentPath === '/image.php';
$adminIsCurrent = strpos($currentPath, (string) $config['admin_route']) === 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? $config['site_name']) ?></title>
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?= htmlspecialchars($config['site_name']) ?>">
  <meta property="og:title" content="<?= htmlspecialchars($ogTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($ogDescription) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
  <?php if ($metaKeywords !== ''): ?>
    <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
  <?php endif; ?>
  <?php if ($ogImage !== ''): ?>
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
    <?php if ($ogImageType !== ''): ?>
      <meta property="og:image:type" content="<?= htmlspecialchars($ogImageType) ?>">
    <?php endif; ?>
    <?php if ($ogImageWidth > 0): ?>
      <meta property="og:image:width" content="<?= $ogImageWidth ?>">
    <?php endif; ?>
    <?php if ($ogImageHeight > 0): ?>
      <meta property="og:image:height" content="<?= $ogImageHeight ?>">
    <?php endif; ?>
  <?php endif; ?>
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($ogTitle) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($ogDescription) ?>">
  <?php if ($ogImage !== ''): ?>
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
  <?php endif; ?>
  <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
  <link rel="stylesheet" href="/assets/style.css">
  <?php if (isset($image) && is_array($image)): ?>
    <?php
      $schema = [
          '@context' => 'https://schema.org',
          '@type' => 'ImageObject',
          'name' => (string) ($image['title'] ?? ''),
          'description' => (string) ($image['description'] ?? ''),
          'contentUrl' => absolute_url('/media.php?type=original&file=' . rawurlencode((string) ($image['original'] ?? ''))),
          'thumbnailUrl' => absolute_url('/media.php?type=thumb&file=' . rawurlencode((string) ($image['thumb'] ?? ''))),
          'dateCreated' => (string) ($image['captured_at'] ?? ''),
          'keywords' => implode(', ', (array) ($image['tags'] ?? [])),
          'license' => 'https://creativecommons.org/licenses/',
      ];
    ?>
    <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <?php endif; ?>
</head>
<body class="<?= htmlspecialchars($pageClass) ?>">
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="site-header">
  <a class="brand" href="/" aria-label="<?= htmlspecialchars($config['site_name']) ?> home">
    <span class="brand-mark" aria-hidden="true"><span></span></span>
    <span class="brand-copy">
      <strong><?= htmlspecialchars($config['site_name']) ?></strong>
      <small>Deep-sky photography</small>
    </span>
  </a>
  <nav aria-label="Primary navigation">
    <a href="/"<?= $galleryIsCurrent ? ' aria-current="page"' : '' ?>>Archive</a>
    <a href="/about"<?= $currentPath === '/about' ? ' aria-current="page"' : '' ?>>Field notes</a>
    <a href="/contact"<?= $currentPath === '/contact' ? ' aria-current="page"' : '' ?>>Contact</a>
    <?php if ($adminIsCurrent): ?>
      <a href="<?= htmlspecialchars($config['admin_route']) ?>/upload" aria-current="page">Studio</a>
    <?php endif; ?>
  </nav>
</header>
<section class="license-banner" aria-label="Image licensing notice">
  <p>
    <span class="license-pulse" aria-hidden="true"></span>
    An independent open image archive
    <span aria-hidden="true">/</span>
    <a href="https://creativecommons.org/licenses/" target="_blank" rel="license noopener noreferrer">Creative Commons licensed</a>
  </p>
</section>
<main id="main-content">
