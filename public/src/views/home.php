<?php $featured = $featured ?? null; ?>
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
      <?php
        $spotlightRule = $featured['_spotlight_rule'] ?? 'latest';
        $spotlightLabel = $spotlightRule === 'featured'
          ? 'Featured curator pick'
          : ($spotlightRule === 'daily' ? 'Daily deterministic pick' : 'Latest published capture');

        $highlightFacts = [];
        if (!empty($featured['object_name'])) {
            $highlightFacts[] = 'Target: ' . (string) $featured['object_name'];
        }
        if (!empty($featured['captured_at'])) {
            $highlightFacts[] = 'Captured: ' . (string) $featured['captured_at'];
        }
        if (!empty($featured['telescope'])) {
            $highlightFacts[] = 'Telescope: ' . (string) $featured['telescope'];
        }
        if (!empty($featured['camera'])) {
            $highlightFacts[] = 'Camera: ' . (string) $featured['camera'];
        }

        $highlightFacts = array_slice($highlightFacts, 0, 2);
      ?>
      <a href="/image.php?id=<?= urlencode($featured['id']) ?>">
        <img loading="lazy" src="/media.php?type=thumb&file=<?= urlencode($featured['thumb']) ?>" alt="<?= htmlspecialchars($featured['title']) ?>">
      </a>
      <p><strong><?= htmlspecialchars($featured['title']) ?></strong><br><?= htmlspecialchars($featured['object_name']) ?> · <?= htmlspecialchars($featured['captured_at']) ?></p>
      <div class="highlight-caption" aria-label="Tonight's Highlight">
        <p class="highlight-kicker">Tonight's Highlight · <?= htmlspecialchars($spotlightLabel) ?></p>
        <?php if (!empty($highlightFacts)): ?>
          <ul>
            <?php foreach ($highlightFacts as $fact): ?>
              <li><?= htmlspecialchars($fact) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <a class="button-link secondary highlight-cta" href="/image.php?id=<?= urlencode($featured['id']) ?>">View full capture details</a>
      </div>
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
