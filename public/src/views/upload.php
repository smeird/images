<section class="panel">
  <h1>Upload image</h1>
  <?php if (!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <?php if (!empty($success)): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="form_action" value="upload_image">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <label>Image <input required type="file" accept="image/*" name="image"></label>
    <label>Title <input required name="title"></label>
    <label>Object name <input required name="object_name"></label>
    <label>Captured at <input required type="date" name="captured_at"></label>
    <label>Description <textarea name="description"></textarea></label>
    <label>Equipment <input required name="equipment" placeholder="Camera, scope, mount, filter"></label>
    <label>Exposure <input required name="exposure" placeholder="30x180s @ ISO 800"></label>
    <label>Processing <input required name="processing" placeholder="Siril + PixInsight"></label>
    <label>Tags (comma-separated) <input name="tags" placeholder="nebula, narrowband"></label>
    <button type="submit">Upload</button>
  </form>
  <p><a href="<?= htmlspecialchars($config['admin_route']) ?>/logout">Sign out</a></p>
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
