<?php
$adminSection = (string) ($admin_section ?? 'upload');
$adminBase = (string) ($config['admin_route'] ?? '/hidden-admin');
?>
<section class="panel">
  <h1>Admin portal</h1>
  <p class="muted">Use task pages to upload images, manage reusable scope types, curate media, and update security settings.</p>
  <nav class="admin-nav" aria-label="Admin tasks">
    <a href="<?= htmlspecialchars($adminBase) ?>/upload" class="<?= $adminSection === 'upload' ? 'is-active' : '' ?>">Upload image</a>
    <a href="<?= htmlspecialchars($adminBase) ?>/scope-types" class="<?= $adminSection === 'scope_types' ? 'is-active' : '' ?>">Scope type responses</a>
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
      <label>Captured at <input required type="date" name="captured_at" value="<?= htmlspecialchars((string) ($_POST['captured_at'] ?? '')) ?>"></label>
      <label>Description <textarea name="description"><?= htmlspecialchars((string) ($_POST['description'] ?? '')) ?></textarea></label>
      <label>Equipment <input required name="equipment" placeholder="Camera, scope, mount, filter" value="<?= htmlspecialchars((string) ($_POST['equipment'] ?? '')) ?>"></label>

      <label>Scope type response
        <input id="scope-type-input" name="scope_type" placeholder="e.g. APO refractor, Newtonian reflector" value="<?= htmlspecialchars((string) ($_POST['scope_type'] ?? '')) ?>">
      </label>
      <?php if (!empty($scope_type_presets)): ?>
        <div class="preset-pill-wrap" data-scope-type-presets>
          <?php foreach ($scope_type_presets as $preset): ?>
            <button type="button" class="secondary preset-pill" data-scope-type-pill="<?= htmlspecialchars((string) $preset) ?>"><?= htmlspecialchars((string) $preset) ?></button>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="muted">No scope type responses saved yet. Add them in the Scope type responses page.</p>
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

<?php if ($adminSection === 'scope_types'): ?>
  <section class="panel">
    <h2>Scope type responses</h2>
    <p class="muted">Upload reusable scope-type responses so image uploads can be filled by clicking pills.</p>

    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="form_action" value="add_scope_type">
      <label>New scope type response <input required name="scope_type_name" placeholder="e.g. 8&quot; Newtonian reflector"></label>
      <button type="submit">Save scope type response</button>
    </form>

    <h3>Saved responses</h3>
    <?php if (empty($scope_type_presets)): ?>
      <p class="muted">No saved responses yet.</p>
    <?php else: ?>
      <ul class="admin-image-list">
        <?php foreach ($scope_type_presets as $preset): ?>
          <li>
            <div><strong><?= htmlspecialchars((string) $preset) ?></strong></div>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this scope type response?');">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="form_action" value="delete_scope_type">
              <input type="hidden" name="scope_type_name" value="<?= htmlspecialchars((string) $preset) ?>">
              <button type="submit" class="danger">Delete</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
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
    const scopeTypeInput = document.getElementById('scope-type-input');
    if (!scopeTypeInput) {
      return;
    }

    document.querySelectorAll('[data-scope-type-pill]').forEach((button) => {
      button.addEventListener('click', () => {
        scopeTypeInput.value = button.getAttribute('data-scope-type-pill') || '';
        scopeTypeInput.focus();
      });
    });
  })();
</script>
