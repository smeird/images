<?php
$adminSection = (string) ($admin_section ?? 'upload');
$adminBase = (string) ($config['admin_route'] ?? '/hidden-admin');
$presetCategories = is_array($setup_preset_categories ?? null) ? $setup_preset_categories : [];
$setupPresetValues = is_array($setup_presets ?? null) ? $setup_presets : [];
$images = is_array($images ?? null) ? $images : [];
$currentEditId = (string) ($_GET['id'] ?? '');
$editingImage = null;

foreach ($images as $imageRecord) {
    if (($imageRecord['id'] ?? '') === $currentEditId) {
        $editingImage = $imageRecord;
        break;
    }
}

function field_value(string $field, $editingImage): string
{
    if (array_key_exists($field, $_POST)) {
        return trim((string) $_POST[$field]);
    }

    if (is_array($editingImage) && array_key_exists($field, $editingImage)) {
        $value = $editingImage[$field];
        if (is_array($value)) {
            return implode(', ', $value);
        }

        return trim((string) $value);
    }

    return '';
}
?>
<div class="admin-layout">
  <aside class="panel admin-sidebar">
    <div class="admin-shell-header">
      <h1>Admin control center</h1>
      <p class="muted">A guided workspace for uploads, setup presets, homepage spotlight control, and metadata editing.</p>
    </div>
    <nav class="admin-nav" aria-label="Admin tasks">
      <a href="<?= htmlspecialchars($adminBase) ?>/upload" class="<?= $adminSection === 'upload' ? 'is-active' : '' ?>">1) Upload</a>
      <a href="<?= htmlspecialchars($adminBase) ?>/setup-presets" class="<?= $adminSection === 'setup_presets' ? 'is-active' : '' ?>">2) Presets</a>
      <a href="<?= htmlspecialchars($adminBase) ?>/manage-images" class="<?= $adminSection === 'manage_images' ? 'is-active' : '' ?>">3) Media library</a>
      <a href="<?= htmlspecialchars($adminBase) ?>/edit-image<?= $currentEditId !== '' ? '?id=' . urlencode($currentEditId) : '' ?>" class="<?= $adminSection === 'edit_image' ? 'is-active' : '' ?>">4) Edit page</a>
      <a href="<?= htmlspecialchars($adminBase) ?>/security" class="<?= $adminSection === 'security' ? 'is-active' : '' ?>">5) Security</a>
    </nav>

    <div class="admin-help-grid">
      <article class="admin-help-card">
        <h3>Fast upload workflow</h3>
        <p class="muted">Use preset pills under each equipment field so repeat gear entries are one click away.</p>
      </article>
      <article class="admin-help-card">
        <h3>Spotlight curation</h3>
        <p class="muted">On <strong>Media library</strong>, set one capture as the homepage spotlight card.</p>
      </article>
      <article class="admin-help-card">
        <h3>SEO + metadata edits</h3>
        <p class="muted">Use the <strong>Edit page</strong> task to update all fields and meta tags for any previous upload.</p>
      </article>
    </div>

    <section class="panel admin-signout-panel">
      <p><a href="<?= htmlspecialchars($config['admin_route']) ?>/logout">Sign out</a></p>
    </section>
  </aside>

  <div class="admin-main">
    <section class="panel admin-status-panel">
      <?php if (!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
      <?php if (!empty($limit_error)): ?><p class="error"><?= htmlspecialchars($limit_error) ?></p><?php endif; ?>
      <?php if (!empty($success)): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    </section>

<?php if ($adminSection === 'upload'): ?>
  <section class="panel">
    <h2>Upload image</h2>
    <p class="muted">Tip: required fields are focused on capture identity + technical context. Everything else can be refined later from the Media library editor.</p>
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
      <?php
        $setupFields = [
            'scope_type' => ['Scope type', 'e.g. APO refractor, Newtonian reflector'],
            'telescope' => ['Telescope / tube', 'e.g. Esprit 120'],
            'mount' => ['Mount', 'e.g. EQ6-R Pro'],
            'camera' => ['Camera', 'e.g. ASI2600MM'],
            'filter_wheel' => ['Filter wheel', 'e.g. ZWO 7x2" EFW'],
            'filters' => ['Filters', 'e.g. Ha, OIII, SII'],
            'filter_set' => ['Filter set', 'e.g. SHO narrowband'],
        ];
      ?>
      <?php foreach ($setupFields as $fieldKey => [$fieldLabel, $placeholder]): ?>
        <label><?= htmlspecialchars($fieldLabel) ?>
          <input id="<?= htmlspecialchars(str_replace('_', '-', $fieldKey)) ?>-input" name="<?= htmlspecialchars($fieldKey) ?>" placeholder="<?= htmlspecialchars($placeholder) ?>" value="<?= htmlspecialchars((string) ($_POST[$fieldKey] ?? '')) ?>">
        </label>
        <?php if (!empty($setupPresetValues[$fieldKey])): ?>
          <div class="preset-pill-wrap" data-preset-group="<?= htmlspecialchars($fieldKey) ?>">
            <?php foreach ($setupPresetValues[$fieldKey] as $preset): ?>
              <button type="button" class="secondary preset-pill" data-preset-pill="<?= htmlspecialchars($fieldKey) ?>" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>

      <label>Exposure <input required name="exposure" placeholder="30x180s @ ISO 800" value="<?= htmlspecialchars((string) ($_POST['exposure'] ?? '')) ?>"></label>
      <label>Processing <input required id="processing-input" name="processing" placeholder="Siril + PixInsight" value="<?= htmlspecialchars((string) ($_POST['processing'] ?? '')) ?>"></label>
      <?php if (!empty($setupPresetValues['processing'])): ?>
        <div class="preset-pill-wrap" data-preset-group="processing">
          <?php foreach ($setupPresetValues['processing'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="processing" data-preset-mode="append" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <label>Wikipedia URL <input name="wikipedia_url" type="url" placeholder="https://en.wikipedia.org/wiki/Orion_Nebula" value="<?= htmlspecialchars((string) ($wikipedia_url ?? '')) ?>"></label>
      <label>Tags (comma-separated) <input id="tags-input" name="tags" placeholder="nebula, narrowband" value="<?= htmlspecialchars((string) ($_POST['tags'] ?? '')) ?>"></label>
      <?php if (!empty($setupPresetValues['tags'])): ?>
        <div class="preset-pill-wrap" data-preset-group="tags">
          <?php foreach ($setupPresetValues['tags'] as $preset): ?>
            <button type="button" class="secondary preset-pill" data-preset-pill="tags" data-preset-mode="append" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <div class="button-row">
        <button type="submit">Upload image</button>
      </div>
    </form>
  </section>
<?php endif; ?>

<?php if ($adminSection === 'setup_presets'): ?>
  <section class="panel">
    <h2>Setup presets</h2>
    <p class="muted">Save frequently used equipment values so the upload workflow stays fast and consistent.</p>

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
    <h2>Media library</h2>
    <p class="muted">Set homepage spotlight, open a dedicated edit page, or permanently delete entries.</p>
    <?php if (empty($images)): ?>
      <p class="muted">No uploaded images yet.</p>
    <?php else: ?>
      <ul class="admin-image-list">
        <?php foreach ($images as $image): ?>
          <?php $isSpotlight = !empty($image['is_spotlight']); ?>
          <li>
            <div>
              <strong><?= htmlspecialchars((string) ($image['title'] ?? 'Untitled')) ?></strong>
              <p class="muted"><?= htmlspecialchars((string) ($image['captured_at'] ?? 'Unknown date')) ?> · ID: <?= htmlspecialchars((string) ($image['id'] ?? '')) ?></p>
              <?php if ($isSpotlight): ?><p class="success">Current homepage spotlight</p><?php endif; ?>
            </div>
            <div class="admin-row-actions">
              <a class="button-link secondary" href="<?= htmlspecialchars($adminBase) ?>/edit-image?id=<?= urlencode((string) ($image['id'] ?? '')) ?>">Edit page</a>
              <form method="post" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="form_action" value="set_spotlight">
                <input type="hidden" name="image_id" value="<?= htmlspecialchars((string) ($image['id'] ?? '')) ?>">
                <button type="submit" class="secondary" <?= $isSpotlight ? 'disabled' : '' ?>><?= $isSpotlight ? 'Spotlight set' : 'Set spotlight' ?></button>
              </form>
              <form method="post" class="inline-form" onsubmit="return confirm('Delete this image permanently?');">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="form_action" value="delete_image">
                <input type="hidden" name="image_id" value="<?= htmlspecialchars((string) ($image['id'] ?? '')) ?>">
                <button type="submit" class="danger">Delete</button>
              </form>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

<?php endif; ?>

<?php if ($adminSection === 'edit_image'): ?>
  <section class="panel">
    <h2>Edit page metadata</h2>
    <p class="muted">Select an image from the media library, then edit all fields on this dedicated page.</p>
    <?php if ($editingImage === null): ?>
      <p class="muted">Choose <strong>Edit page</strong> from the Media library to start editing.</p>
    <?php else: ?>
      <h3><?= htmlspecialchars((string) ($editingImage['title'] ?? 'Untitled')) ?></h3>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="form_action" value="update_image_metadata">
        <input type="hidden" name="image_id" value="<?= htmlspecialchars((string) ($editingImage['id'] ?? '')) ?>">

        <label>Title <input required name="title" value="<?= htmlspecialchars(field_value('title', $editingImage)) ?>"></label>
        <label>Object name <input required name="object_name" value="<?= htmlspecialchars(field_value('object_name', $editingImage)) ?>"></label>

        <label>Object type
          <input id="edit-object-type-input" name="object_type" value="<?= htmlspecialchars(field_value('object_type', $editingImage)) ?>">
        </label>
        <?php if (!empty($setupPresetValues['object_type'])): ?>
          <div class="preset-pill-wrap" data-preset-group="object_type">
            <?php foreach ($setupPresetValues['object_type'] as $preset): ?>
              <button type="button" class="secondary preset-pill" data-preset-pill="object_type" data-preset-target="edit" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <label>Captured at <input required type="date" name="captured_at" value="<?= htmlspecialchars(field_value('captured_at', $editingImage)) ?>"></label>
        <label>Description <textarea name="description"><?= htmlspecialchars(field_value('description', $editingImage)) ?></textarea></label>

        <h4>Equipment + processing</h4>
        <?php
          $editSetupFields = [
              'scope_type' => ['Scope type', 'edit-scope-type-input'],
              'telescope' => ['Telescope / tube', 'edit-telescope-input'],
              'mount' => ['Mount', 'edit-mount-input'],
              'camera' => ['Camera', 'edit-camera-input'],
              'filter_wheel' => ['Filter wheel', 'edit-filter-wheel-input'],
              'filters' => ['Filters', 'edit-filters-input'],
              'filter_set' => ['Filter set', 'edit-filter-set-input'],
          ];
        ?>
        <?php foreach ($editSetupFields as $fieldKey => $fieldMeta): ?>
          <label><?= htmlspecialchars((string) $fieldMeta[0]) ?>
            <input id="<?= htmlspecialchars((string) $fieldMeta[1]) ?>" name="<?= htmlspecialchars((string) $fieldKey) ?>" value="<?= htmlspecialchars(field_value($fieldKey, $editingImage)) ?>">
          </label>
          <?php if (!empty($setupPresetValues[$fieldKey])): ?>
            <div class="preset-pill-wrap" data-preset-group="<?= htmlspecialchars((string) $fieldKey) ?>">
              <?php foreach ($setupPresetValues[$fieldKey] as $preset): ?>
                <button type="button" class="secondary preset-pill" data-preset-pill="<?= htmlspecialchars((string) $fieldKey) ?>" data-preset-target="edit" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>

        <label>Exposure <input name="exposure" value="<?= htmlspecialchars(field_value('exposure', $editingImage)) ?>"></label>
        <label>Processing <input id="edit-processing-input" name="processing" value="<?= htmlspecialchars(field_value('processing', $editingImage)) ?>"></label>
        <?php if (!empty($setupPresetValues['processing'])): ?>
          <div class="preset-pill-wrap" data-preset-group="processing">
            <?php foreach ($setupPresetValues['processing'] as $preset): ?>
              <button type="button" class="secondary preset-pill" data-preset-pill="processing" data-preset-target="edit" data-preset-mode="append" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <label>Wikipedia URL <input name="wikipedia_url" type="url" value="<?= htmlspecialchars(field_value('wikipedia_url', $editingImage) !== '' ? field_value('wikipedia_url', $editingImage) : field_value('wikipediaUrl', $editingImage)) ?>"></label>
        <label>Tags (comma-separated) <input id="edit-tags-input" name="tags" value="<?= htmlspecialchars(field_value('tags', $editingImage)) ?>"></label>
        <?php if (!empty($setupPresetValues['tags'])): ?>
          <div class="preset-pill-wrap" data-preset-group="tags">
            <?php foreach ($setupPresetValues['tags'] as $preset): ?>
              <button type="button" class="secondary preset-pill" data-preset-pill="tags" data-preset-target="edit" data-preset-mode="append" data-preset-value="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <h4>Meta tags</h4>
        <label>Meta title <input name="meta_title" placeholder="Custom share/search title" value="<?= htmlspecialchars(field_value('meta_title', $editingImage)) ?>"></label>
        <label>Meta description <textarea name="meta_description" placeholder="Custom summary for social/search cards"><?= htmlspecialchars(field_value('meta_description', $editingImage)) ?></textarea></label>
        <label>Meta keywords <input name="meta_keywords" placeholder="astrophotography, nebula, narrowband" value="<?= htmlspecialchars(field_value('meta_keywords', $editingImage)) ?>"></label>
        <div class="button-row">
          <button type="submit">Save metadata updates</button>
          <a class="button-link secondary" href="<?= htmlspecialchars($adminBase) ?>/manage-images">Back to media library</a>
        </div>
      </form>
    <?php endif; ?>
  </section>
<?php endif; ?>

<?php if ($adminSection === 'security'): ?>
  <section class="panel">
    <h2>Change admin password</h2>
    <p class="muted">Use a long, unique passphrase. This invalidates active remember-me tokens.</p>
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

  </div>
</div>

<script>
  (function () {
    const fieldIds = {
      upload: {
        object_type: 'object-type-input',
        scope_type: 'scope-type-input',
        telescope: 'telescope-input',
        mount: 'mount-input',
        camera: 'camera-input',
        filter_wheel: 'filter-wheel-input',
        filters: 'filters-input',
        filter_set: 'filter-set-input',
        processing: 'processing-input',
        tags: 'tags-input'
      },
      edit: {
        object_type: 'edit-object-type-input',
        scope_type: 'edit-scope-type-input',
        telescope: 'edit-telescope-input',
        mount: 'edit-mount-input',
        camera: 'edit-camera-input',
        filter_wheel: 'edit-filter-wheel-input',
        filters: 'edit-filters-input',
        filter_set: 'edit-filter-set-input',
        processing: 'edit-processing-input',
        tags: 'edit-tags-input'
      }
    };

    document.querySelectorAll('[data-preset-pill]').forEach((button) => {
      button.addEventListener('click', () => {
        const key = button.getAttribute('data-preset-pill') || '';
        const value = button.getAttribute('data-preset-value') || '';
        const target = button.getAttribute('data-preset-target') || 'upload';
        const targetFields = fieldIds[target] || {};
        const inputId = targetFields[key];
        if (!inputId) {
          return;
        }

        const input = document.getElementById(inputId);
        if (!input) {
          return;
        }

        const mode = button.getAttribute('data-preset-mode') || 'replace';

        if (mode === 'append') {
          const currentValues = input.value
            .split(',')
            .map((item) => item.trim())
            .filter((item) => item !== '');

          if (!currentValues.includes(value)) {
            currentValues.push(value);
          }

          input.value = currentValues.join(', ');
        } else {
          input.value = value;
        }

        input.focus();
      });
    });
  })();
</script>
