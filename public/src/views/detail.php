<?php
$wikiSource = null;
if (!empty($wikipedia_data) && is_array($wikipedia_data)) {
    $wikiSource = [
        'title' => (string) ($wikipedia_data['title'] ?? ''),
        'extract' => (string) ($wikipedia_data['extract'] ?? ''),
        'thumbnail' => (string) ($wikipedia_data['thumbnail'] ?? ''),
        'facts' => is_array($wikipedia_data['key_facts'] ?? null) ? $wikipedia_data['key_facts'] : [],
        'url' => (string) ($wikipedia_data['canonical_url'] ?? ($image['wikipediaUrl'] ?? '')),
    ];
} elseif (!empty($image['wikipediaUrl'])) {
    $wikiSource = [
        'title' => (string) ($image['wikiTitle'] ?? ''),
        'extract' => (string) ($image['wikiExtract'] ?? ''),
        'thumbnail' => (string) ($image['wikiThumbnail'] ?? ''),
        'facts' => is_array($image['wikiFacts'] ?? null) ? $image['wikiFacts'] : [],
        'url' => (string) ($image['wikipediaUrl'] ?? ''),
    ];
}

$detailObject = trim((string) ($image['object_name'] ?? ''));
$detailTitle = trim((string) ($image['title'] ?? 'Untitled capture'));
$detailAlt = $detailObject !== ''
    ? 'Astrophotograph of ' . $detailObject . ': ' . $detailTitle
    : $detailTitle;
$detailThumbLarge = (string) ($image['thumb'] ?? '');
$detailThumbSmall = (string) ($image['thumb_small'] ?? $detailThumbLarge);

$factGroups = [
    'Capture' => [
        'Object' => $detailObject,
        'Object type' => (string) ($image['object_type'] ?? ''),
        'Captured' => (string) ($image['captured_at'] ?? ''),
        'Exposure' => (string) ($image['exposure'] ?? ''),
    ],
    'Imaging train' => [
        'Equipment' => (string) ($image['equipment'] ?? ''),
        'Telescope' => (string) ($image['telescope'] ?? ''),
        'Mount' => (string) ($image['mount'] ?? ''),
        'Camera' => (string) ($image['camera'] ?? ''),
        'Filter wheel' => (string) ($image['filter_wheel'] ?? ''),
        'Filters' => (string) ($image['filters'] ?? ''),
        'Filter set' => (string) ($image['filter_set'] ?? ''),
        'Scope type' => (string) ($image['scope_type'] ?? ''),
    ],
    'Processing' => [
        'Workflow' => (string) ($image['processing'] ?? ''),
    ],
];
?>

<a class="detail-back" href="/#gallery-start"><span aria-hidden="true">←</span> Back to archive</a>
<header class="detail-titlebar">
  <p class="detail-eyebrow"><?= htmlspecialchars((string) ($image['object_type'] ?? 'Deep-sky observation')) ?></p>
  <h1><?= htmlspecialchars($detailTitle) ?></h1>
  <p><?= htmlspecialchars($detailObject) ?><?php if (!empty($image['captured_at'])): ?> <span aria-hidden="true">/</span> <?= htmlspecialchars((string) $image['captured_at']) ?><?php endif; ?></p>
</header>
<article class="detail detail-viewer-layout">
  <figure class="detail-media skeleton-card" data-detail-media data-skeleton-card>
    <div class="skeleton-shimmer skeleton-media-block detail-media-skeleton" data-skeleton-placeholder aria-hidden="true"></div>
    <img
      class="detail-image fade-asset"
      src="/media.php?type=thumb&amp;file=<?= urlencode($detailThumbLarge) ?>"
      srcset="/media.php?type=thumb&amp;file=<?= urlencode($detailThumbSmall) ?> 400w, /media.php?type=thumb&amp;file=<?= urlencode($detailThumbLarge) ?> 800w"
      sizes="(max-width: 760px) 96vw, 92vw"
      data-full-src="/media.php?type=original&amp;file=<?= urlencode((string) $image['original']) ?>"
      alt="<?= htmlspecialchars($detailAlt) ?>"
      decoding="async"
      fetchpriority="high"
      data-detail-image
      data-skeleton-image
    >
    <button type="button" class="fullscreen-toggle" data-fullscreen-toggle aria-label="View image in fullscreen">View fullscreen</button>
    <figcaption><span><?= htmlspecialchars($detailTitle) ?></span><span><?= htmlspecialchars($detailObject) ?></span></figcaption>
  </figure>
  <div class="panel detail-panel skeleton-card" data-skeleton-card>
    <div class="skeleton-meta-lines detail-meta-skeleton" data-skeleton-placeholder aria-hidden="true">
      <span class="skeleton-shimmer skeleton-line skeleton-line-title"></span>
      <span class="skeleton-shimmer skeleton-line skeleton-line-copy"></span>
      <span class="skeleton-shimmer skeleton-line skeleton-line-copy"></span>
      <span class="skeleton-shimmer skeleton-line skeleton-line-copy skeleton-line-short"></span>
    </div>

    <div class="detail-info-layout">
      <section class="detail-primary-copy">
        <?php if (trim((string) ($image['description'] ?? '')) !== ''): ?>
          <p class="detail-story"><?= nl2br(htmlspecialchars((string) $image['description'])) ?></p>
        <?php endif; ?>

        <div class="detail-fact-groups">
          <?php $factGroupIndex = 0; ?>
          <?php foreach ($factGroups as $groupLabel => $facts): ?>
            <?php
              $visibleFacts = array_filter($facts, static function ($value): bool {
                  return trim((string) $value) !== '';
              });
              if (empty($visibleFacts)) {
                  continue;
              }
              $factGroupIndex++;
            ?>
            <section class="detail-fact-group">
              <p class="detail-fact-kicker"><?= str_pad((string) $factGroupIndex, 2, '0', STR_PAD_LEFT) ?> / <?= htmlspecialchars($groupLabel) ?></p>
              <dl>
                <?php foreach ($visibleFacts as $label => $value): ?>
                  <div><dt><?= htmlspecialchars((string) $label) ?></dt><dd><?= htmlspecialchars((string) $value) ?></dd></div>
                <?php endforeach; ?>
              </dl>
            </section>
          <?php endforeach; ?>
        </div>

        <div class="share-link">
          <label for="image-share-url"><strong>Share preview link</strong></label>
          <div class="share-link-row">
            <input id="image-share-url" type="text" readonly value="<?= htmlspecialchars(absolute_url('/image.php?id=' . rawurlencode((string) $image['id']))) ?>">
            <button type="button" class="secondary share-link-button" data-copy-share-link>Copy link</button>
          </div>
          <p class="muted" data-copy-share-status role="status" aria-live="polite">Paste this URL in Facebook, WhatsApp, or iMessage to show this image preview.</p>
        </div>

        <p class="attribution-note">Image license: This gallery image is published under a Creative Commons license.</p>

        <?php if (!empty($image['tags'])): ?>
          <div class="tag-list">
            <?php foreach ($image['tags'] as $tag): ?>
              <span class="tag"><?= htmlspecialchars($tag) ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <?php if ($wikiSource !== null): ?>
        <section class="wiki-panel detail-wiki-column">
          <p class="detail-fact-kicker">Object notes / Wikipedia</p>
          <?php if ($wikiSource['thumbnail'] !== ''): ?>
            <img class="wiki-thumb" loading="lazy" decoding="async" src="<?= htmlspecialchars($wikiSource['thumbnail']) ?>" alt="Wikipedia thumbnail for <?= htmlspecialchars($wikiSource['title'] !== '' ? $wikiSource['title'] : $image['object_name']) ?>">
          <?php endif; ?>
          <?php if ($wikiSource['title'] !== ''): ?>
            <h3><?= htmlspecialchars($wikiSource['title']) ?></h3>
          <?php endif; ?>
          <?php if ($wikiSource['extract'] !== ''): ?>
            <p><?= nl2br(htmlspecialchars($wikiSource['extract'])) ?></p>
          <?php else: ?>
            <p class="muted">Wikipedia summary is not cached yet.</p>
          <?php endif; ?>
          <?php if (!empty($wikiSource['facts'])): ?>
            <ul class="metadata-list wiki-facts-list">
              <?php foreach ($wikiSource['facts'] as $fact): ?>
                <?php if (!empty($fact['label']) && !empty($fact['value'])): ?>
                  <li><strong><?= htmlspecialchars((string) $fact['label']) ?>:</strong> <?= htmlspecialchars((string) $fact['value']) ?></li>
                <?php endif; ?>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <?php if ($wikiSource['url'] !== ''): ?>
            <p>
              <a href="<?= htmlspecialchars($wikiSource['url']) ?>" target="_blank" rel="noopener noreferrer">
                Read more on Wikipedia
              </a>
            </p>
          <?php endif; ?>
          <p class="attribution-note">
            Text and media excerpted from Wikipedia/Wikimedia under their applicable licenses; see the linked article for full attribution history and license details.
          </p>
        </section>
      <?php elseif (!empty($wikipedia_error)): ?>
        <section class="wiki-panel detail-wiki-column">
          <p class="detail-fact-kicker">Object notes / Wikipedia</p>
          <p class="muted">No external reference yet.</p>
        </section>
      <?php endif; ?>
    </div>
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

        const fullSource = image.dataset.fullSrc;
        if (fullSource && image.dataset.highResolution !== 'true') {
          image.src = fullSource;
          image.removeAttribute('srcset');
          image.removeAttribute('sizes');
          image.dataset.highResolution = 'true';
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
