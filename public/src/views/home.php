<?php $featured = $images[0] ?? null; ?>
<section class="hero">
  <div class="hero-copy">
    <h1>Night sky captures with full capture transparency</h1>
    <p>Explore deep-sky imaging sessions with complete equipment notes, exposure breakdowns, and post-processing context so every frame tells the full story.</p>
    <div class="statline">
      <span class="pill"><?= count($images) ?> published captures</span>
      <span class="pill">Curated astrophotography workflow</span>
      <span class="pill">Metadata-first presentation</span>
    </div>
  </div>
  <aside class="hero-feature">
    <h2>Spotlight capture</h2>
    <?php if ($featured): ?>
      <a href="/image.php?id=<?= urlencode($featured['id']) ?>">
        <img loading="lazy" src="/media.php?type=thumb&file=<?= urlencode($featured['thumb']) ?>" alt="<?= htmlspecialchars($featured['title']) ?>">
      </a>
      <p><strong><?= htmlspecialchars($featured['title']) ?></strong><br><?= htmlspecialchars($featured['object_name']) ?> · <?= htmlspecialchars($featured['captured_at']) ?></p>
    <?php else: ?>
      <p>No spotlight yet. Upload your first image from the secure admin route to light up the gallery.</p>
    <?php endif; ?>
  </aside>
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
