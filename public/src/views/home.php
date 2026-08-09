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
        'thumb_small' => (string) ($image['thumb_small'] ?? ($image['thumb'] ?? '')),
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
<section class="landing-shell hero--immersive<?= $featured ? ' has-feature' : ' is-empty' ?>" id="immersive-hero" aria-labelledby="home-title">
  <div class="hero-spectral hero-spectral--ha" aria-hidden="true"></div>
  <div class="hero-spectral hero-spectral--oiii" aria-hidden="true"></div>
  <?php if ($featured): ?>
    <?php
      $featuredThumbLarge = (string) ($featured['thumb'] ?? '');
      $featuredThumbSmall = (string) ($featured['thumb_small'] ?? $featuredThumbLarge);
      $featuredObject = trim((string) ($featured['object_name'] ?? ''));
      $featuredAlt = $featuredObject !== ''
        ? 'Astrophotograph of ' . $featuredObject . ': ' . (string) ($featured['title'] ?? '')
        : (string) ($featured['title'] ?? 'Featured astrophotograph');
    ?>
    <a class="hero-frame__media" href="/image.php?id=<?= urlencode($featured['id']) ?>" aria-label="View featured capture: <?= htmlspecialchars($featured['title']) ?>">
      <img
        src="/media.php?type=thumb&amp;file=<?= urlencode($featuredThumbLarge) ?>"
        srcset="/media.php?type=thumb&amp;file=<?= urlencode($featuredThumbSmall) ?> 400w, /media.php?type=thumb&amp;file=<?= urlencode($featuredThumbLarge) ?> 800w"
        sizes="100vw"
        alt="<?= htmlspecialchars($featuredAlt) ?>"
        fetchpriority="high"
        decoding="async"
      >
    </a>
  <?php endif; ?>
  <div class="landing-shell__lead">
    <p class="landing-kicker"><span>Night Sky Atlas</span><span>Independent astrophotography archive</span></p>
    <h1 id="home-title"><span>Light from</span><em>the deep.</em></h1>
    <p class="landing-intro">Nebulae, galaxies and lunar landscapes—photographed from Earth, shaped over long nights, and presented at the scale they deserve.</p>
    <div class="landing-stats" role="list" aria-label="Gallery snapshot">
      <span role="listitem"><strong><?= str_pad((string) count($images), 2, '0', STR_PAD_LEFT) ?></strong> captured frames</span>
      <span role="listitem"><strong><?= str_pad((string) count($objectTypeOptions), 2, '0', STR_PAD_LEFT) ?></strong> celestial families</span>
    </div>
    <div class="landing-actions">
      <a class="button-link" href="#gallery-start">Explore the archive <span aria-hidden="true">↓</span></a>
      <a class="landing-text-link" href="/about">Read the field notes <span aria-hidden="true">↗</span></a>
    </div>
  </div>
  <?php if ($featured): ?>
    <?php
      $spotlightRule = $featured['_spotlight_rule'] ?? 'latest';
      $spotlightLabel = $spotlightRule === 'featured'
        ? 'Curator selected'
        : ($spotlightRule === 'daily' ? 'Today’s observation' : 'Latest observation');
    ?>
    <aside class="landing-shell__spotlight" aria-label="Featured observation">
      <p class="highlight-kicker"><?= htmlspecialchars($spotlightLabel) ?></p>
      <h2><?= htmlspecialchars($featured['title']) ?></h2>
      <p class="landing-spotlight-meta"><?= htmlspecialchars($featured['object_name']) ?><?php if (!empty($featured['captured_at'])): ?> <span aria-hidden="true">/</span> <?= htmlspecialchars($featured['captured_at']) ?><?php endif; ?></p>
      <a class="landing-spotlight-link" href="/image.php?id=<?= urlencode($featured['id']) ?>">Enter this frame <span aria-hidden="true">↗</span></a>
    </aside>
  <?php endif; ?>
</section>
<section class="archive-heading" id="gallery-start" aria-labelledby="gallery-heading">
  <p class="archive-heading__index">Archive / <?= str_pad((string) count($images), 2, '0', STR_PAD_LEFT) ?></p>
  <div>
    <h2 id="gallery-heading">Collected light</h2>
    <p>Long exposures, dark skies, and distant structures arranged as a living visual journal.</p>
  </div>
</section>
<section class="filter-toolbar" aria-label="Gallery filters">
  <div class="filter-toolbar__summary">
    <p class="filter-toolbar__lead">Explore by target</p>
    <div id="filter-chip-summary" class="filter-chip-summary" aria-live="polite"></div>
    <button id="filter-refine-toggle" type="button" class="filter-refine-toggle" aria-expanded="false" aria-controls="filter-refine-panel">Refine collection <span aria-hidden="true">+</span></button>
  </div>
  <div id="filter-refine-panel" class="filter-refine-panel" hidden>
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
  </div>
  <div class="filter-toolbar__actions">
    <p id="filter-results" class="muted" aria-live="polite"></p>
    <button id="filter-reset" type="button" class="secondary">Clear filters</button>
  </div>
</section>
<section class="grid gallery-mosaic" id="gallery" aria-labelledby="gallery-heading">
  <?php if (empty($images)): ?>
    <p>No images yet. Admins can upload from the secure route.</p>
  <?php else: ?>
    <?php foreach ($images as $imageIndex => $image): ?>
      <article class="card" data-image-id="<?= htmlspecialchars((string) $image['id']) ?>">
        <a href="/image.php?id=<?= urlencode($image['id']) ?>" aria-label="View capture: <?= htmlspecialchars($image['title']) ?>">
          <div class="skeleton-media-wrap">
            <?php
              $thumbLarge = (string) ($image['thumb'] ?? '');
              $thumbSmall = (string) ($image['thumb_small'] ?? $thumbLarge);
              $overlayExposure = trim((string) ($image['exposure'] ?? ''));
              $overlayEquipment = trim((string) ($image['equipment'] ?? ''));
              if ($overlayEquipment === '') {
                  $overlayEquipment = trim((string) (($image['telescope'] ?? '') . ' · ' . ($image['camera'] ?? '')), ' ·');
              }
            ?>
            <?php
              $imageObject = trim((string) ($image['object_name'] ?? ''));
              $imageAlt = $imageObject !== ''
                ? 'Astrophotograph of ' . $imageObject . ': ' . (string) ($image['title'] ?? '')
                : (string) ($image['title'] ?? 'Astrophotograph');
            ?>
            <img loading="lazy" decoding="async" src="/media.php?type=thumb&amp;file=<?= urlencode($thumbLarge) ?>" srcset="/media.php?type=thumb&amp;file=<?= urlencode($thumbSmall) ?> 400w, /media.php?type=thumb&amp;file=<?= urlencode($thumbLarge) ?> 800w" sizes="(max-width: 680px) 94vw, (max-width: 900px) 48vw, 58vw" alt="<?= htmlspecialchars($imageAlt) ?>">
            <div class="card-overlay">
              <?php if ($overlayExposure !== ''): ?><span>Exposure: <?= htmlspecialchars($overlayExposure) ?></span><?php endif; ?>
              <?php if ($overlayEquipment !== ''): ?><span>Gear: <?= htmlspecialchars($overlayEquipment) ?></span><?php endif; ?>
            </div>
            <div class="card-caption">
              <span class="card-caption__index" aria-hidden="true"><?= str_pad((string) ($imageIndex + 1), 2, '0', STR_PAD_LEFT) ?></span>
              <div>
                <h3><?= htmlspecialchars($image['title']) ?></h3>
                <p><?= htmlspecialchars($image['object_name']) ?><?php if (!empty($image['captured_at'])): ?> <span aria-hidden="true">/</span> <?= htmlspecialchars($image['captured_at']) ?><?php endif; ?></p>
              </div>
              <span class="card-caption__arrow" aria-hidden="true">↗</span>
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
  const gridEl = document.getElementById('gallery');
  if (!payloadEl || !gridEl) return;

  const allImages = JSON.parse(payloadEl.textContent || '[]');
  const cardNodes = new Map(Array.from(gridEl.querySelectorAll('[data-image-id]')).map((card) => [card.dataset.imageId, card]));
  const controls = {
    hero: document.getElementById('immersive-hero'),
    chipSummary: document.getElementById('filter-chip-summary'),
    refineToggle: document.getElementById('filter-refine-toggle'),
    refinePanel: document.getElementById('filter-refine-panel'),
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

  const updateRefinePanelVisibility = (isOpen) => {
    if (!controls.refinePanel || !controls.refineToggle) return;
    controls.refinePanel.hidden = !isOpen;
    controls.refineToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    controls.refineToggle.innerHTML = isOpen ? 'Close filters <span aria-hidden="true">−</span>' : 'Refine collection <span aria-hidden="true">+</span>';
  };

  if (controls.refineToggle) {
    controls.refineToggle.addEventListener('click', () => {
      const isOpen = controls.refinePanel ? controls.refinePanel.hidden : true;
      updateRefinePanelVisibility(isOpen);
    });
  }

  const setHeroParallax = () => {
    if (!controls.hero || !advancedMotionEnabled) return;
    const maxDrift = 56;
    const scrollFactor = Math.min(window.scrollY / 900, 1);
    controls.hero.style.setProperty('--spectral-ha-shift', (scrollFactor * -maxDrift).toFixed(1) + 'px');
    controls.hero.style.setProperty('--spectral-oiii-shift', (scrollFactor * (maxDrift * 0.72)).toFixed(1) + 'px');
  };

  const renderChipSummary = (state) => {
    if (!controls.chipSummary) return;

    const chips = [];
    if (state.object_type) chips.push({ label: 'Object', value: state.object_type, key: 'objectType' });
    if (state.tag) chips.push({ label: 'Tag', value: state.tag, key: 'tag' });
    if (state.date_from) chips.push({ label: 'From', value: state.date_from, key: 'dateFrom' });
    if (state.date_to) chips.push({ label: 'To', value: state.date_to, key: 'dateTo' });
    if (state.search) chips.push({ label: 'Search', value: state.search, key: 'search' });
    if (state.sort && state.sort !== 'newest') chips.push({ label: 'Sort', value: state.sort, key: 'sort' });

    if (!chips.length) {
      controls.chipSummary.innerHTML = '<span class="filter-chip-summary__empty">No active filters · showing full gallery</span>';
      return;
    }

    controls.chipSummary.innerHTML = chips.map((chip) => (
      '<button type="button" class="filter-chip" data-filter-clear="' + chip.key + '">' + escapeHtml(chip.label + ': ' + chip.value) + ' ×</button>'
    )).join('');
  };

  if (controls.chipSummary) {
    controls.chipSummary.addEventListener('click', (event) => {
      const button = event.target.closest('[data-filter-clear]');
      if (!button) return;
      const controlKey = button.getAttribute('data-filter-clear');
      const control = controls[controlKey];
      if (!control) return;
      control.value = controlKey === 'sort' ? 'newest' : '';
      run();
    });
  }

  const escapeHtml = (value) => String(value || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let advancedMotionEnabled = !prefersReducedMotion.matches;

  if (typeof prefersReducedMotion.addEventListener === 'function') {
    prefersReducedMotion.addEventListener('change', (event) => {
      advancedMotionEnabled = !event.matches;
      if (!advancedMotionEnabled && controls.hero) {
        controls.hero.style.setProperty('--spectral-ha-shift', '0px');
        controls.hero.style.setProperty('--spectral-oiii-shift', '0px');
      } else {
        setHeroParallax();
      }
    });
  }

  const renderCards = (records) => {
    if (!records.length) {
      const emptyState = document.createElement('p');
      emptyState.className = 'gallery-empty';
      emptyState.textContent = allImages.length
        ? 'No images match the current filters.'
        : 'The archive is awaiting its first published frame.';
      gridEl.replaceChildren(emptyState);
      return;
    }

    const fragment = document.createDocumentFragment();
    records.forEach((image) => {
      const card = cardNodes.get(String(image.id || ''));
      if (card) fragment.appendChild(card);
    });
    gridEl.replaceChildren(fragment);
  };

  const syncQueryParams = (state) => {
    const params = new URLSearchParams();
    Object.entries(state).forEach(([key, value]) => {
      if (!value || (key === 'sort' && value === 'newest')) return;
      params.set(key, value);
    });
    const query = params.toString();
    const nextUrl = (query ? ('/?' + query) : '/') + window.location.hash;
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
    renderChipSummary(state);
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

  if (typeof window.addEventListener === 'function') {
    window.addEventListener('scroll', setHeroParallax, { passive: true });
  }

  setHeroParallax();
  run();
})();
</script>
