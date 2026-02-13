<article class="detail">
  <figure class="detail-media" data-detail-media>
    <img class="detail-image" src="/media.php?type=original&file=<?= urlencode($image['original']) ?>" alt="<?= htmlspecialchars($image['title']) ?>" data-detail-image>
    <button type="button" class="fullscreen-toggle" data-fullscreen-toggle aria-label="View image in fullscreen">View fullscreen</button>
  </figure>
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

<script>
  (function () {
    const media = document.querySelector('[data-detail-media]');
    const image = document.querySelector('[data-detail-image]');
    const button = document.querySelector('[data-fullscreen-toggle]');

    if (!media || !image || !button || !document.fullscreenEnabled) {
      if (button) {
        button.hidden = true;
      }
      return;
    }

    const setButtonLabel = () => {
      const active = document.fullscreenElement === media;
      button.textContent = active ? 'Exit fullscreen' : 'View fullscreen';
      button.setAttribute('aria-label', active ? 'Exit fullscreen view' : 'View image in fullscreen');
      media.classList.toggle('is-fullscreen', active);
    };

    button.addEventListener('click', () => {
      if (document.fullscreenElement === media) {
        document.exitFullscreen();
        return;
      }

      media.requestFullscreen();
    });

    document.addEventListener('fullscreenchange', setButtonLabel);
    setButtonLabel();
  })();
</script>
