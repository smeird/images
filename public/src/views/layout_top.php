<?php
$canonicalUrl = isset($canonical_url) ? (string) $canonical_url : absolute_url((string) ($_SERVER['REQUEST_URI'] ?? '/'));
$ogTitle = isset($meta_title) ? (string) $meta_title : (string) ($title ?? $config['site_name']);
$ogDescription = isset($meta_description) ? (string) $meta_description : 'Explore astrophotography captures on ' . $config['site_name'] . '.';
$ogImage = isset($meta_image) ? (string) $meta_image : '';
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
  <?php if ($ogImage !== ''): ?>
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
  <?php endif; ?>
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($ogTitle) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($ogDescription) ?>">
  <?php if ($ogImage !== ''): ?>
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
  <?php endif; ?>
  <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="site-header">
  <a class="brand" href="/"><?= htmlspecialchars($config['site_name']) ?></a>
  <nav>
    <a href="/">Gallery</a>
    <a href="/about">About</a>
    <a href="<?= htmlspecialchars($config['admin_route']) ?>/login">Admin</a>
  </nav>
</header>
<main>
