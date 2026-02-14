<article class="detail">
  <figure class="detail-media skeleton-card" data-detail-media data-skeleton-card>
    <div class="skeleton-shimmer skeleton-media-block detail-media-skeleton" data-skeleton-placeholder aria-hidden="true"></div>
    <img class="detail-image fade-asset" src="/media.php?type=original&file=<?= urlencode($image['original']) ?>" alt="<?= htmlspecialchars($image['title']) ?>" data-detail-image data-skeleton-image>
    <button type="button" class="fullscreen-toggle" data-fullscreen-toggle aria-label="View image in fullscreen">View fullscreen</button>
  </figure>
  <div class="panel skeleton-card" data-skeleton-card>
    <div class="skeleton-meta-lines detail-meta-skeleton" data-skeleton-placeholder aria-hidden="true">
      <span class="skeleton-shimmer skeleton-line skeleton-line-title"></span>
      <span class="skeleton-shimmer skeleton-line skeleton-line-copy"></span>
      <span class="skeleton-shimmer skeleton-line skeleton-line-copy"></span>
      <span class="skeleton-shimmer skeleton-line skeleton-line-copy skeleton-line-short"></span>
    </div>

    <h1><?= htmlspecialchars($image['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($image['description'])) ?></p>
    <ul class="metadata-list">
      <li><strong>Object:</strong> <?= htmlspecialchars($image['object_name']) ?></li>
      <?php if (!empty($image['object_type'])): ?>
        <li><strong>Object type:</strong> <?= htmlspecialchars((string) $image['object_type']) ?></li>
      <?php endif; ?>
      <li><strong>Captured:</strong> <?= htmlspecialchars($image['captured_at']) ?></li>
      <li><strong>Equipment:</strong> <?= htmlspecialchars($image['equipment']) ?></li>
      <?php if (!empty($image['telescope'])): ?><li><strong>Telescope:</strong> <?= htmlspecialchars((string) $image['telescope']) ?></li><?php endif; ?>
      <?php if (!empty($image['mount'])): ?><li><strong>Mount:</strong> <?= htmlspecialchars((string) $image['mount']) ?></li><?php endif; ?>
      <?php if (!empty($image['camera'])): ?><li><strong>Camera:</strong> <?= htmlspecialchars((string) $image['camera']) ?></li><?php endif; ?>
      <?php if (!empty($image['filter_wheel'])): ?><li><strong>Filter wheel:</strong> <?= htmlspecialchars((string) $image['filter_wheel']) ?></li><?php endif; ?>
      <?php if (!empty($image['filters'])): ?><li><strong>Filters:</strong> <?= htmlspecialchars((string) $image['filters']) ?></li><?php endif; ?>
      <?php if (!empty($image['filter_set'])): ?><li><strong>Filter set:</strong> <?= htmlspecialchars((string) $image['filter_set']) ?></li><?php endif; ?>
      <?php if (!empty($image['scope_type'])): ?>
        <li><strong>Scope type:</strong> <?= htmlspecialchars((string) $image['scope_type']) ?></li>
      <?php endif; ?>
      <li><strong>Exposure:</strong> <?= htmlspecialchars($image['exposure']) ?></li>
      <li><strong>Processing:</strong> <?= htmlspecialchars($image['processing']) ?></li>
    </ul>

    <?php if (!empty($image['wikipediaUrl'])): ?>
      <section>
        <h2>Wikipedia</h2>
        <?php if (!empty($image['wikiThumbnail'])): ?>
          <img class="detail-image" src="<?= htmlspecialchars($image['wikiThumbnail']) ?>" alt="<?= htmlspecialchars($image['wikiTitle'] ?: $image['object_name']) ?>">
        <?php endif; ?>
        <?php if (!empty($image['wikiTitle'])): ?>
          <h3><?= htmlspecialchars($image['wikiTitle']) ?></h3>
        <?php endif; ?>
        <?php if (!empty($image['wikiExtract'])): ?>
          <p><?= nl2br(htmlspecialchars($image['wikiExtract'])) ?></p>
        <?php else: ?>
          <p>Wikipedia summary is not cached yet.</p>
        <?php endif; ?>
        <?php if (!empty($image['wikiFacts']) && is_array($image['wikiFacts'])): ?>
          <ul class="metadata-list wiki-facts-list">
            <?php foreach ($image['wikiFacts'] as $fact): ?>
              <?php if (!empty($fact['label']) && !empty($fact['value'])): ?>
                <li><strong><?= htmlspecialchars((string) $fact['label']) ?>:</strong> <?= htmlspecialchars((string) $fact['value']) ?></li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <p>
          <a href="<?= htmlspecialchars($image['wikipediaUrl']) ?>" target="_blank" rel="noopener noreferrer">Read on Wikipedia</a>
        </p>
      </section>
    <?php endif; ?>

    <div class="share-link">
      <label for="image-share-url"><strong>Share preview link</strong></label>
      <div class="share-link-row">
        <input id="image-share-url" type="text" readonly value="<?= htmlspecialchars(absolute_url('/image.php?id=' . rawurlencode((string) $image['id']))) ?>">
        <button type="button" class="secondary share-link-button" data-copy-share-link>Copy link</button>
      </div>
      <p class="muted" data-copy-share-status>Paste this URL in Facebook, WhatsApp, or iMessage to show this image preview.</p>
    </div>

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
        <?php if (!empty($wikipedia_data['key_facts']) && is_array($wikipedia_data['key_facts'])): ?>
          <ul class="metadata-list wiki-facts-list">
            <?php foreach ($wikipedia_data['key_facts'] as $fact): ?>
              <?php if (!empty($fact['label']) && !empty($fact['value'])): ?>
                <li><strong><?= htmlspecialchars((string) $fact['label']) ?>:</strong> <?= htmlspecialchars((string) $fact['value']) ?></li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ul>
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

<script>
  (function () {
    const media = document.querySelector('[data-detail-media]');
    const image = document.querySelector('[data-detail-image]');
    const button = document.querySelector('[data-fullscreen-toggle]');
    const shareInput = document.getElementById('image-share-url');
    const shareButton = document.querySelector('[data-copy-share-link]');
    const shareStatus = document.querySelector('[data-copy-share-status]');
    const skeletonCards = document.querySelectorAll('[data-skeleton-card]');

    skeletonCards.forEach((card) => {
      const targetImage = card.querySelector('[data-skeleton-image]');
      if (!targetImage) {
        return;
      }

      card.classList.add('is-loading');

      const reveal = () => {
        card.classList.remove('is-loading');
        card.classList.add('is-loaded');
      };

      if (targetImage.complete && targetImage.naturalWidth > 0) {
        reveal();
      } else {
        targetImage.addEventListener('load', reveal, { once: true });
        targetImage.addEventListener('error', reveal, { once: true });
      }
    });

    if (!media || !image || !button || !document.fullscreenEnabled) {
      if (button) {
        button.hidden = true;
      }
    } else {
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
    }

    if (shareInput && shareButton) {
      shareButton.addEventListener('click', async () => {
        const link = shareInput.value;
        try {
          if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(link);
          } else {
            shareInput.focus();
            shareInput.select();
            document.execCommand('copy');
          }

          if (shareStatus) {
            shareStatus.textContent = 'Link copied. You can now paste it into Facebook, WhatsApp, or iMessage.';
          }
        } catch (error) {
          if (shareStatus) {
            shareStatus.textContent = 'Could not copy automatically. Copy the URL manually.';
          }
        }
      });
    }

  })();
</script>
