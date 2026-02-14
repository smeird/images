<?php $featured = $featured ?? ($images[0] ?? null); ?>
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
      <a href="/image.php?id=<?= urlencode($featured['id']) ?>" class="skeleton-card" data-skeleton-card>
        <div class="skeleton-media-wrap">
          <div class="skeleton-shimmer skeleton-media-block" data-skeleton-placeholder aria-hidden="true"></div>
          <img class="fade-asset" loading="lazy" src="/media.php?type=thumb&file=<?= urlencode($featured['thumb']) ?>" alt="<?= htmlspecialchars($featured['title']) ?>" data-skeleton-image>
        </div>
        <div class="skeleton-meta-wrap">
          <div class="skeleton-meta-lines" data-skeleton-placeholder aria-hidden="true">
            <span class="skeleton-shimmer skeleton-line skeleton-line-title"></span>
            <span class="skeleton-shimmer skeleton-line skeleton-line-copy"></span>
          </div>
          <p><strong><?= htmlspecialchars($featured['title']) ?></strong><br><?= htmlspecialchars($featured['object_name']) ?> · <?= htmlspecialchars($featured['captured_at']) ?></p>
        </div>
      </a>
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
      <article class="card skeleton-card" data-skeleton-card>
        <a href="/image.php?id=<?= urlencode($image['id']) ?>">
          <div class="skeleton-media-wrap">
            <div class="skeleton-shimmer skeleton-media-block" data-skeleton-placeholder aria-hidden="true"></div>
            <img class="fade-asset" loading="lazy" src="/media.php?type=thumb&file=<?= urlencode($image['thumb']) ?>" alt="<?= htmlspecialchars($image['title']) ?>" data-skeleton-image>
          </div>
          <div class="skeleton-meta-wrap">
            <div class="skeleton-meta-lines" data-skeleton-placeholder aria-hidden="true">
              <span class="skeleton-shimmer skeleton-line skeleton-line-title"></span>
              <span class="skeleton-shimmer skeleton-line skeleton-line-copy"></span>
            </div>
            <h3><?= htmlspecialchars($image['title']) ?></h3>
            <p><?= htmlspecialchars($image['object_name']) ?> · <?= htmlspecialchars($image['captured_at']) ?></p>
          </div>
        </a>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<script>
  (function () {
    const cards = document.querySelectorAll('[data-skeleton-card]');

    cards.forEach((card) => {
      const image = card.querySelector('[data-skeleton-image]');
      if (!image) {
        return;
      }

      card.classList.add('is-loading');

      const revealAsset = () => {
        card.classList.remove('is-loading');
        card.classList.add('is-loaded');
      };

      if (image.complete && image.naturalWidth > 0) {
        revealAsset();
        return;
      }

      image.addEventListener('load', revealAsset, { once: true });
      image.addEventListener('error', revealAsset, { once: true });
    });
  })();
</script>
