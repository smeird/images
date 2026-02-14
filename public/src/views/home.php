<?php
$featured = $featured ?? ($images[0] ?? null);
$selectedObjectType = trim((string) ($_GET['object_type'] ?? ''));
$selectedTag = trim((string) ($_GET['tag'] ?? ''));
$selectedDateFrom = trim((string) ($_GET['date_from'] ?? ''));
$selectedDateTo = trim((string) ($_GET['date_to'] ?? ''));
$selectedSearch = trim((string) ($_GET['search'] ?? ''));
$selectedSort = trim((string) ($_GET['sort'] ?? 'newest'));

$objectTypes = [];
$tags = [];
$imagePayload = [];
foreach ($images as $image) {
    $objectType = trim((string) ($image['object_type'] ?? ''));
    if ($objectType !== '') {
        $objectTypes[$objectType] = true;
    }

    $imageTags = array_values(array_filter(array_map(static function ($tag): string {
        return trim((string) $tag);
    }, (array) ($image['tags'] ?? []))));

    foreach ($imageTags as $tag) {
        $tags[$tag] = true;
    }

    $imagePayload[] = [
        'id' => (string) ($image['id'] ?? ''),
        'title' => (string) ($image['title'] ?? ''),
        'object_name' => (string) ($image['object_name'] ?? ''),
        'object_type' => $objectType,
        'captured_at' => (string) ($image['captured_at'] ?? ''),
        'thumb' => (string) ($image['thumb'] ?? ''),
        'exposure' => (string) ($image['exposure'] ?? ''),
        'equipment' => (string) ($image['equipment'] ?? (($image['telescope'] ?? '') . ' · ' . ($image['camera'] ?? ''))),
        'tags' => $imageTags,
    ];
}

$objectTypeOptions = array_keys($objectTypes);
$tagOptions = array_keys($tags);
sort($objectTypeOptions, SORT_NATURAL | SORT_FLAG_CASE);
sort($tagOptions, SORT_NATURAL | SORT_FLAG_CASE);
?>
<section class="hero hero--immersive">
  <div class="hero-copy">
    <h1>A cinematic wall of the night sky</h1>
    <p>Start with the images first. Use filters only when you want to narrow the field.</p>
    <div class="statline">
      <span class="pill"><?= count($images) ?> published captures</span>
      <span class="pill">Large-format gallery wall</span>
      <span class="pill">Capture context on every detail page</span>
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
<section class="filter-toolbar" aria-label="Gallery filters">
  <p class="filter-toolbar__lead">Filters are available, but the gallery stays center stage.</p>
  <div class="filter-toolbar__grid">
    <label>
      Object type
      <select id="filter-object-type" name="object_type">
        <option value="">All object types</option>
        <?php foreach ($objectTypeOptions as $objectType): ?>
          <option value="<?= htmlspecialchars($objectType) ?>" <?= $selectedObjectType === $objectType ? 'selected' : '' ?>><?= htmlspecialchars($objectType) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>
      Tag
      <select id="filter-tag" name="tag">
        <option value="">All tags</option>
        <?php foreach ($tagOptions as $tag): ?>
          <option value="<?= htmlspecialchars($tag) ?>" <?= $selectedTag === $tag ? 'selected' : '' ?>><?= htmlspecialchars($tag) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>
      Capture from
      <input id="filter-date-from" type="date" name="date_from" value="<?= htmlspecialchars($selectedDateFrom) ?>">
    </label>
    <label>
      Capture to
      <input id="filter-date-to" type="date" name="date_to" value="<?= htmlspecialchars($selectedDateTo) ?>">
    </label>
    <label>
      Search
      <input id="filter-search" type="search" name="search" value="<?= htmlspecialchars($selectedSearch) ?>" placeholder="Title, object, tags…">
    </label>
    <label>
      Sort
      <select id="filter-sort" name="sort">
        <option value="newest" <?= $selectedSort === 'newest' ? 'selected' : '' ?>>Newest</option>
        <option value="oldest" <?= $selectedSort === 'oldest' ? 'selected' : '' ?>>Oldest</option>
        <option value="exposure" <?= $selectedSort === 'exposure' ? 'selected' : '' ?>>Exposure length</option>
        <option value="title" <?= $selectedSort === 'title' ? 'selected' : '' ?>>A–Z title</option>
      </select>
    </label>
  </div>
  <div class="filter-toolbar__actions">
    <p id="filter-results" class="muted" aria-live="polite"></p>
    <button id="filter-reset" type="button" class="secondary">Clear filters</button>
  </div>
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
            <?php
              $overlayExposure = trim((string) ($image['exposure'] ?? ''));
              $overlayEquipment = trim((string) ($image['equipment'] ?? ''));
              if ($overlayEquipment === '') {
                  $overlayEquipment = trim((string) (($image['telescope'] ?? '') . ' · ' . ($image['camera'] ?? '')), ' ·');
              }
            ?>
            <div class="card-overlay" aria-hidden="true">
              <?php if ($overlayExposure !== ''): ?><span>Exposure: <?= htmlspecialchars($overlayExposure) ?></span><?php endif; ?>
              <?php if ($overlayEquipment !== ''): ?><span>Gear: <?= htmlspecialchars($overlayEquipment) ?></span><?php endif; ?>
            </div>
          </div>
        </a>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
</section>
<script id="home-image-data" type="application/json"><?= json_encode($imagePayload, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<script>
(() => {
  const payloadEl = document.getElementById('home-image-data');
  const gridEl = document.querySelector('.grid');
  if (!payloadEl || !gridEl) return;

  const allImages = JSON.parse(payloadEl.textContent || '[]');
  const controls = {
    objectType: document.getElementById('filter-object-type'),
    tag: document.getElementById('filter-tag'),
    dateFrom: document.getElementById('filter-date-from'),
    dateTo: document.getElementById('filter-date-to'),
    search: document.getElementById('filter-search'),
    sort: document.getElementById('filter-sort'),
    reset: document.getElementById('filter-reset'),
    results: document.getElementById('filter-results')
  };

  const toUnixMs = (value) => {
    const parsed = Date.parse(value || '');
    return Number.isFinite(parsed) ? parsed : null;
  };

  const parseExposureSeconds = (value) => {
    const text = String(value || '').toLowerCase();
    if (!text) return 0;

    const totalPattern = text.match(/(\d+(?:\.\d+)?)\s*(s|sec|secs|second|seconds|m|min|mins|minute|minutes|h|hr|hrs|hour|hours)\b/);
    if (totalPattern) {
      const count = parseFloat(totalPattern[1]);
      const unit = totalPattern[2];
      if (unit.startsWith('h')) return count * 3600;
      if (unit.startsWith('m')) return count * 60;
      return count;
    }

    const stackPattern = text.match(/(\d+)\s*[x×]\s*(\d+(?:\.\d+)?)/);
    if (stackPattern) {
      return parseFloat(stackPattern[1]) * parseFloat(stackPattern[2]);
    }

    const numeric = parseFloat(text);
    return Number.isFinite(numeric) ? numeric : 0;
  };

  const getState = () => ({
    object_type: controls.objectType.value.trim(),
    tag: controls.tag.value.trim(),
    date_from: controls.dateFrom.value.trim(),
    date_to: controls.dateTo.value.trim(),
    search: controls.search.value.trim().toLowerCase(),
    sort: controls.sort.value.trim() || 'newest'
  });

  const escapeHtml = (value) => String(value || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

  const buildOverlayMarkup = (image) => {
    const exposure = String(image.exposure || '').trim();
    const equipment = String(image.equipment || '').trim();
    const lines = [];
    if (exposure) lines.push('<span>Exposure: ' + escapeHtml(exposure) + '</span>');
    if (equipment) lines.push('<span>Gear: ' + escapeHtml(equipment) + '</span>');

    return '<div class="card-overlay" aria-hidden="true">' + lines.join('') + '</div>';
  };

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let advancedMotionEnabled = !prefersReducedMotion.matches;

  const resetCardMotion = (card) => {
    card.style.removeProperty('--tilt-x');
    card.style.removeProperty('--tilt-y');
    card.style.removeProperty('--pointer-y');
    card.style.removeProperty('--shadow-scale');
  };

  const bindCardMotion = (card) => {
    if (!card || card.dataset.motionBound === 'true') return;
    card.dataset.motionBound = 'true';

    const handleMove = (event) => {
      if (!advancedMotionEnabled) return;
      const rect = card.getBoundingClientRect();
      const px = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
      const py = Math.max(0, Math.min(1, (event.clientY - rect.top) / rect.height));
      const tiltX = (0.5 - py) * 7;
      const tiltY = (px - 0.5) * 9;
      card.style.setProperty('--tilt-x', tiltX.toFixed(2) + 'deg');
      card.style.setProperty('--tilt-y', tiltY.toFixed(2) + 'deg');
      card.style.setProperty('--pointer-y', py.toFixed(3));
      card.style.setProperty('--shadow-scale', (1 + ((1 - py) * 0.08)).toFixed(3));
    };

    card.addEventListener('pointermove', handleMove);
    card.addEventListener('pointerleave', () => resetCardMotion(card));
    card.addEventListener('focusin', () => {
      card.classList.add('is-focus-visible');
    });
    card.addEventListener('focusout', () => {
      card.classList.remove('is-focus-visible');
      resetCardMotion(card);
    });
  };

  const initCardMotion = () => {
    gridEl.querySelectorAll('.card').forEach((card) => {
      bindCardMotion(card);
      if (!advancedMotionEnabled) resetCardMotion(card);
    });
  };

  if (typeof prefersReducedMotion.addEventListener === 'function') {
    prefersReducedMotion.addEventListener('change', (event) => {
      advancedMotionEnabled = !event.matches;
      initCardMotion();
    });
  }

  const renderCards = (records) => {
    if (!records.length) {
      gridEl.innerHTML = '<p>No images match the current filters.</p>';
      return;
    }

    const cards = records.map((image) => {
      const detailUrl = '/image.php?id=' + encodeURIComponent(image.id || '');
      const thumbUrl = '/media.php?type=thumb&file=' + encodeURIComponent(image.thumb || '');
      const title = image.title || 'Untitled';
      const subtitle = [image.object_name || 'Unknown object', image.captured_at || 'Unknown date'].join(' · ');

      return '<article class="card">'
        + '<a href="' + detailUrl + '">'
        + '<img loading="lazy" src="' + thumbUrl + '" alt="' + escapeHtml(title) + '">'
        + '<h3>' + escapeHtml(title) + '</h3>'
        + '<p>' + escapeHtml(subtitle) + '</p>'
        + buildOverlayMarkup(image)
        + '</a>'
        + '</article>';
    });

    gridEl.innerHTML = cards.join('');
    initCardMotion();
  };

  const syncQueryParams = (state) => {
    const params = new URLSearchParams();
    Object.entries(state).forEach(([key, value]) => {
      if (!value || (key === 'sort' && value === 'newest')) return;
      params.set(key, value);
    });
    const query = params.toString();
    const nextUrl = query ? ('/?' + query) : '/';
    window.history.replaceState({}, '', nextUrl);
  };

  const run = () => {
    const state = getState();
    const fromMs = toUnixMs(state.date_from);
    const toMs = toUnixMs(state.date_to);

    const filtered = allImages.filter((image) => {
      if (state.object_type && image.object_type !== state.object_type) return false;
      if (state.tag && !(image.tags || []).includes(state.tag)) return false;

      const capturedMs = toUnixMs(image.captured_at);
      if (fromMs !== null && (capturedMs === null || capturedMs < fromMs)) return false;
      if (toMs !== null && (capturedMs === null || capturedMs > toMs)) return false;

      if (state.search) {
        const haystack = [image.title, image.object_name, image.object_type, image.captured_at, image.exposure, (image.tags || []).join(' ')].join(' ').toLowerCase();
        if (!haystack.includes(state.search)) return false;
      }
      return true;
    });

    filtered.sort((a, b) => {
      if (state.sort === 'oldest') {
        return (toUnixMs(a.captured_at) || 0) - (toUnixMs(b.captured_at) || 0);
      }
      if (state.sort === 'exposure') {
        return parseExposureSeconds(b.exposure) - parseExposureSeconds(a.exposure);
      }
      if (state.sort === 'title') {
        return String(a.title || '').localeCompare(String(b.title || ''), undefined, { sensitivity: 'base' });
      }
      return (toUnixMs(b.captured_at) || 0) - (toUnixMs(a.captured_at) || 0);
    });

    renderCards(filtered);
    if (controls.results) {
      controls.results.textContent = filtered.length + ' of ' + allImages.length + ' captures shown';
    }
    syncQueryParams(state);
  };

  [controls.objectType, controls.tag, controls.dateFrom, controls.dateTo, controls.search, controls.sort]
    .forEach((input) => {
      if (!input) return;
      input.addEventListener('input', run);
      input.addEventListener('change', run);
    });

  if (controls.reset) {
    controls.reset.addEventListener('click', () => {
      controls.objectType.value = '';
      controls.tag.value = '';
      controls.dateFrom.value = '';
      controls.dateTo.value = '';
      controls.search.value = '';
      controls.sort.value = 'newest';
      run();
    });
  }

  initCardMotion();
  run();
})();
</script>
