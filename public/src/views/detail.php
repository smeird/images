<article class="detail">
  <img class="detail-image" src="/media.php?type=original&file=<?= urlencode($image['original']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
  <div class="panel">
    <h1><?= htmlspecialchars($image['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($image['description'])) ?></p>
    <ul class="metadata-list">
      <li><strong>Object:</strong> <?= htmlspecialchars($image['object_name']) ?></li>
      <li><strong>Captured:</strong> <?= htmlspecialchars($image['captured_at']) ?></li>
      <li><strong>Equipment:</strong> <?= htmlspecialchars($image['equipment']) ?></li>
      <li><strong>Exposure:</strong> <?= htmlspecialchars($image['exposure']) ?></li>
      <li><strong>Processing:</strong> <?= htmlspecialchars($image['processing']) ?></li>
    </ul>
    <?php if (!empty($image['tags'])): ?>
      <div class="tag-list">
        <?php foreach ($image['tags'] as $tag): ?>
          <span class="tag"><?= htmlspecialchars($tag) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($wikipedia_data)): ?>
      <section class="wiki-panel">
        <h2>Wikipedia reference</h2>
        <?php if (!empty($wikipedia_data['thumbnail'])): ?>
          <img class="wiki-thumb" src="<?= htmlspecialchars((string) $wikipedia_data['thumbnail']) ?>" alt="Wikipedia thumbnail for <?= htmlspecialchars((string) ($wikipedia_data['title'] ?? $image['object_name'])) ?>">
        <?php endif; ?>
        <?php if (!empty($wikipedia_data['extract'])): ?>
          <p><?= htmlspecialchars((string) $wikipedia_data['extract']) ?></p>
        <?php endif; ?>
        <?php if (!empty($wikipedia_data['canonical_url'])): ?>
          <p>
            <a href="<?= htmlspecialchars((string) $wikipedia_data['canonical_url']) ?>" target="_blank" rel="noopener noreferrer">
              Read more on Wikipedia
            </a>
          </p>
        <?php endif; ?>
        <p class="attribution-note">
          Text and media excerpted from Wikipedia/Wikimedia under their applicable licenses; see the linked article for full attribution history and license details.
        </p>
      </section>
    <?php elseif (!empty($image['wikipedia_url']) || !empty($wikipedia_error)): ?>
      <section class="wiki-panel">
        <h2>Wikipedia reference</h2>
        <p class="muted">No external reference yet.</p>
      </section>
    <?php endif; ?>
  </div>
</article>
