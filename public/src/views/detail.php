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
  </div>
</article>
