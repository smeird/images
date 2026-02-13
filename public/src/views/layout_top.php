<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? $config['site_name']) ?></title>
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
