<?php
$adminSection = (string) ($admin_section ?? 'upload');
$adminBase = (string) ($config['admin_route'] ?? '/hidden-admin');
$presetCategories = is_array($setup_preset_categories ?? null) ? $setup_preset_categories : [];
$setupPresetValues = is_array($setup_presets ?? null) ? $setup_presets : [];
?>
<section class="panel">
  <h1>Admin portal</h1>
  <p class="muted">Use task pages to upload images, manage reusable setup presets, curate media, and update security settings.</p>
  <nav class="admin-nav" aria-label="Admin tasks">
    <a href="<?= htmlspecialchars($adminBase) ?>/upload" class="<?= $adminSection === 'upload' ? 'is-active' : '' ?>">Upload image</a>
    <a href="<?= htmlspecialchars($adminBase) ?>/setup-presets" class="<?= $adminSection === 'setup_presets' ? 'is-active' : '' ?>">Setup presets</a>
    <a href="<?= htmlspecialchars($adminBase) ?>/manage-images" class="<?= $adminSection === 'manage_images' ? 'is-active' : '' ?>">Manage images</a>
    <a href="<?= htmlspecialchars($adminBase) ?>/security" class="<?= $adminSection === 'security' ? 'is-active' : '' ?>">Security</a>
  </nav>

  <?php if (!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <?php if (!empty($limit_error)): ?><p class="error"><?= htmlspecialchars($limit_error) ?></p><?php endif; ?>
  <?php if (!empty($success)): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
</section>

<?php if ($adminSection === 'upload'): ?>
  <section class="panel">
    <h2>Upload image</h2>
    <p>Maximum accepted upload size: <strong><?= htmlspecialchars((string) ($effective_upload_limit_human ?? '10.0 MB')) ?></strong></p>

    <?php if (!empty($storage_summary) && is_array($storage_summary)): ?>
      <section class="storage-summary">
        <h3>System storage</h3>
        <p class="muted">Available space: <strong><?= htmlspecialchars((string) ($storage_summary['free_human'] ?? 'Unknown')) ?></strong></p>
        <ul class="storage-list">
          <li><strong>Used:</strong> <?= htmlspecialchars((string) ($storage_summary['used_human'] ?? 'Unknown')) ?></li>
          <li><strong>Total:</strong> <?= htmlspecialchars((string) ($storage_summary['total_human'] ?? 'Unknown')) ?></li>
        </ul>
      </section>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="form_action" value="upload_image">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <label>Image <input required type="file" accept="image/*" name="image"></label>
      <label>Title <input required name="title" value="<?= htmlspecialchars((string) ($_POST['title'] ?? '')) ?>"></label>
      <label>Object name <input required name="object_name" value="<?= htmlspecialchars((string) ($_POST['object_name'] ?? '')) ?>"></label>

      <label>Object type
        <input id="object-type-input" name="object_type" placeholder="e.g. Nebula, Galaxy, Cluster" value="<?= htmlspecialchars((string) ($_POST['object_type'] ?? '')) ?>">
      </label>
      <?php if (!empty($setupPresetValues['object_type'])): ?>
        <div class="preset-pill-wrap" data-preset-group="object_type">
          <?php foreach ($setupPresetValues['object_type'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="object_type" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label>Captured at <input required type="date" name="captured_at" value="<?= htmlspecialchars((string) ($_POST['captured_at'] ?? '')) ?>"></label>
      <label>Description <textarea name="description"><?= htmlspecialchars((string) ($_POST['description'] ?? '')) ?></textarea></label>

      <h3>Capture setup</h3>
      <label>Scope type
        <input id="scope-type-input" name="scope_type" placeholder="e.g. APO refractor, Newtonian reflector" value="<?= htmlspecialchars((string) ($_POST['scope_type'] ?? '')) ?>">
      </label>
      <?php if (!empty($setupPresetValues['scope_type'])): ?>
        <div class="preset-pill-wrap" data-preset-group="scope_type">
          <?php foreach ($setupPresetValues['scope_type'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="scope_type" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label>Telescope / tube
        <input id="telescope-input" name="telescope" placeholder="e.g. Esprit 120" value="<?= htmlspecialchars((string) ($_POST['telescope'] ?? '')) ?>">
      </label>
      <?php if (!empty($setupPresetValues['telescope'])): ?>
        <div class="preset-pill-wrap" data-preset-group="telescope">
          <?php foreach ($setupPresetValues['telescope'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="telescope" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label>Mount
        <input id="mount-input" name="mount" placeholder="e.g. EQ6-R Pro" value="<?= htmlspecialchars((string) ($_POST['mount'] ?? '')) ?>">
      </label>
      <?php if (!empty($setupPresetValues['mount'])): ?>
        <div class="preset-pill-wrap" data-preset-group="mount">
          <?php foreach ($setupPresetValues['mount'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="mount" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label>Camera
        <input id="camera-input" name="camera" placeholder="e.g. ASI2600MM" value="<?= htmlspecialchars((string) ($_POST['camera'] ?? '')) ?>">
      </label>
      <?php if (!empty($setupPresetValues['camera'])): ?>
        <div class="preset-pill-wrap" data-preset-group="camera">
          <?php foreach ($setupPresetValues['camera'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="camera" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label>Filter wheel
        <input id="filter-wheel-input" name="filter_wheel" placeholder="e.g. ZWO 7x2\" EFW" value="<?= htmlspecialchars((string) ($_POST['filter_wheel'] ?? '')) ?>">
      </label>
      <?php if (!empty($setupPresetValues['filter_wheel'])): ?>
        <div class="preset-pill-wrap" data-preset-group="filter_wheel">
          <?php foreach ($setupPresetValues['filter_wheel'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="filter_wheel" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label>Filters
        <input id="filters-input" name="filters" placeholder="e.g. Ha, OIII, SII" value="<?= htmlspecialchars((string) ($_POST['filters'] ?? '')) ?>">
      </label>
      <?php if (!empty($setupPresetValues['filters'])): ?>
        <div class="preset-pill-wrap" data-preset-group="filters">
          <?php foreach ($setupPresetValues['filters'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="filters" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label>Filter set
        <input id="filter-set-input" name="filter_set" placeholder="e.g. SHO narrowband" value="<?= htmlspecialchars((string) ($_POST['filter_set'] ?? '')) ?>">
      </label>
      <?php if (!empty($setupPresetValues['filter_set'])): ?>
        <div class="preset-pill-wrap" data-preset-group="filter_set">
          <?php foreach ($setupPresetValues['filter_set'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="filter_set" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label>Exposure <input required name="exposure" placeholder="30x180s @ ISO 800" value="<?= htmlspecialchars((string) ($_POST['exposure'] ?? '')) ?>"></label>
      <label>Processing <input required name="processing" placeholder="Siril + PixInsight" value="<?= htmlspecialchars((string) ($_POST['processing'] ?? '')) ?>"></label>
      <label>Wikipedia URL <input name="wikipedia_url" type="url" placeholder="https://en.wikipedia.org/wiki/Orion_Nebula" value="<?= htmlspecialchars((string) ($wikipedia_url ?? '')) ?>"></label>
      <label>Tags (comma-separated) <input name="tags" placeholder="nebula, narrowband" value="<?= htmlspecialchars((string) ($_POST['tags'] ?? '')) ?>"></label>
      <div class="button-row">
        <button type="submit">Upload</button>
      </div>
    </form>
  </section>
<?php endif; ?>

<?php if ($adminSection === 'setup_presets'): ?>
  <section class="panel">
    <h2>Setup presets</h2>
    <p class="muted">Store reusable observatory items so uploads can be completed by clicking pills.</p>

    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="form_action" value="add_setup_preset">
      <label>Preset category
        <select required name="preset_category">
          <?php foreach ($presetCategories as $key => $label): ?>
            <option value="<?= htmlspecialchars((string) $key) ?>"><?= htmlspecialchars((string) $label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Preset value <input required name="preset_value" placeholder="e.g. EQ6-R Pro"></label>
      <button type="submit">Save preset</button>
    </form>

    <h3>Saved presets</h3>
    <?php foreach ($presetCategories as $key => $label): ?>
      <section class="preset-category-block">
        <h4><?= htmlspecialchars((string) $label) ?></h4>
        <?php $items = $setupPresetValues[$key] ?? []; ?>
        <?php if (empty($items)): ?>
          <p class="muted">No saved presets.</p>
        <?php else: ?>
          <ul class="admin-image-list">
            <?php foreach ($items as $preset): ?>
              <li>
                <div><strong><?= htmlspecialchars((string) $preset) ?></strong></div>
                <form method="post" class="inline-form" onsubmit="return confirm('Delete this preset?');">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="form_action" value="delete_setup_preset">
                  <input type="hidden" name="preset_category" value="<?= htmlspecialchars((string) $key) ?>">
                  <input type="hidden" name="preset_value" value="<?= htmlspecialchars((string) $preset) ?>">
                  <button type="submit" class="danger">Delete</button>
                </form>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>
    <?php endforeach; ?>
  </section>
<?php endif; ?>

<?php if ($adminSection === 'manage_images'): ?>
  <section class="panel">
    <h2>Manage uploaded images</h2>
    <?php if (empty($images)): ?>
      <p class="muted">No uploaded images yet.</p>
    <?php else: ?>
      <ul class="admin-image-list">
        <?php foreach ($images as $image): ?>
          <li>
            <div>
              <strong><?= htmlspecialchars((string) ($image['title'] ?? 'Untitled')) ?></strong>
              <p class="muted"><?= htmlspecialchars((string) ($image['captured_at'] ?? 'Unknown date')) ?> · ID: <?= htmlspecialchars((string) ($image['id'] ?? '')) ?></p>
            </div>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this image permanently?');">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="form_action" value="delete_image">
              <input type="hidden" name="image_id" value="<?= htmlspecialchars((string) ($image['id'] ?? '')) ?>">
              <button type="submit" class="danger">Delete</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
<?php endif; ?>

<?php if ($adminSection === 'security'): ?>
  <section class="panel">
    <h2>Change admin password</h2>
    <?php if (!empty($password_error)): ?><p class="error"><?= htmlspecialchars($password_error) ?></p><?php endif; ?>
    <?php if (!empty($password_success)): ?><p class="success"><?= htmlspecialchars($password_success) ?></p><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="form_action" value="change_password">
      <label>Current password <input required type="password" name="current_password" autocomplete="current-password"></label>
      <label>New password <input required type="password" name="new_password" minlength="12" autocomplete="new-password"></label>
      <label>Confirm new password <input required type="password" name="confirm_password" minlength="12" autocomplete="new-password"></label>
      <button type="submit">Update password</button>
    </form>
  </section>
<?php endif; ?>

<section class="panel">
  <p><a href="<?= htmlspecialchars($config['admin_route']) ?>/logout">Sign out</a></p>
</section>

<script>
  (function () {
    const fieldIds = {
      object_type: 'object-type-input',
      scope_type: 'scope-type-input',
      telescope: 'telescope-input',
      mount: 'mount-input',
      camera: 'camera-input',
      filter_wheel: 'filter-wheel-input',
      filters: 'filters-input',
      filter_set: 'filter-set-input'
    };

    document.querySelectorAll('[data-preset-pill]').forEach((button) => {
      button.addEventListener('click', () => {
        const key = button.getAttribute('data-preset-pill') || '';
        const value = button.getAttribute('data-preset-value') || '';
        const inputId = fieldIds[key];
        if (!inputId) {
          return;
        }

        const input = document.getElementById(inputId);
        if (!input) {
          return;
        }

        input.value = value;
        input.focus();
      });
    });
  })();
</script>
