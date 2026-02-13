<section class="hero">
  <h1>Astronomy gallery</h1>
  <p>Browse curated captures with full details on equipment, exposures, and processing workflow.</p>
</section>
<section class="grid">
  <?php if (empty($images)): ?>
    <p>No images yet. Admins can upload from the secure route.</p>
  <?php else: ?>
    <?php foreach ($images as $image): ?>
      <article class="card">
        <a href="/image.php?id=<?= urlencode($image['id']) ?>">
          <img loading="lazy" src="/media.php?type=thumb&file=<?= urlencode($image['thumb']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
          <h3><?= htmlspecialchars($image['title']) ?></h3>
          <p><?= htmlspecialchars($image['object_name']) ?> · <?= htmlspecialchars($image['captured_at']) ?></p>
        </a>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
</section>
