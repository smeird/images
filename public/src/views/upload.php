<section class="panel">
  <h1>Upload image</h1>
  <?php if (!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <?php if (!empty($limit_error)): ?><p class="error"><?= htmlspecialchars($limit_error) ?></p><?php endif; ?>
  <?php if (!empty($success)): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
  <p>Maximum accepted upload size: <strong><?= htmlspecialchars((string) ($effective_upload_limit_human ?? '10.0 MB')) ?></strong></p>

  <?php if (!empty($storage_summary) && is_array($storage_summary)): ?>
    <section class="storage-summary">
      <h2>System storage</h2>
      <p class="muted">Available space: <strong><?= htmlspecialchars((string) ($storage_summary['free_human'] ?? 'Unknown')) ?></strong></p>
      <ul class="storage-list">
        <li><strong>Used:</strong> <?= htmlspecialchars((string) ($storage_summary['used_human'] ?? 'Unknown')) ?></li>
        <li><strong>Total:</strong> <?= htmlspecialchars((string) ($storage_summary['total_human'] ?? 'Unknown')) ?></li>
      </ul>
    </section>
  <?php else: ?>
    <p class="muted">System storage information is unavailable in this environment.</p>
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
    <label>Exposure <input required name="exposure" placeholder="30x180s @ ISO 800" value="<?= htmlspecialchars((string) ($_POST['exposure'] ?? '')) ?>"></label>
    <label>Processing <input required name="processing" placeholder="Siril + PixInsight" value="<?= htmlspecialchars((string) ($_POST['processing'] ?? '')) ?>"></label>
    <label>Wikipedia URL <input name="wikipedia_url" type="url" placeholder="https://en.wikipedia.org/wiki/Orion_Nebula" value="<?= htmlspecialchars((string) ($wikipedia_url ?? '')) ?>"></label>
    <label>Tags (comma-separated) <input name="tags" placeholder="nebula, narrowband" value="<?= htmlspecialchars((string) ($_POST['tags'] ?? '')) ?>"></label>
    <div class="button-row">
      <button type="submit">Upload</button>
      <button type="submit" name="preview_wikipedia" value="1" class="secondary" formnovalidate>Preview Wikipedia</button>
    </div>
  </form>

  <?php if (!empty($wikipedia_preview)): ?>
    <section class="wiki-panel">
      <h2>Wikipedia preview</h2>
      <p><strong><?= htmlspecialchars((string) ($wikipedia_preview['title'] ?? '')) ?></strong></p>
      <?php if (!empty($wikipedia_preview['thumbnail'])): ?>
        <img class="wiki-thumb" src="<?= htmlspecialchars((string) $wikipedia_preview['thumbnail']) ?>" alt="Wikipedia thumbnail preview">
      <?php endif; ?>
      <p><?= htmlspecialchars((string) ($wikipedia_preview['extract'] ?? '')) ?></p>
      <?php if (!empty($wikipedia_preview['canonical_url'])): ?>
        <p><a href="<?= htmlspecialchars((string) $wikipedia_preview['canonical_url']) ?>" target="_blank" rel="noopener noreferrer">View canonical Wikipedia page</a></p>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <p><a href="<?= htmlspecialchars($config['admin_route']) ?>/logout">Sign out</a></p>
</section>

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
