<section class="panel">
  <h1>Upload image</h1>
  <?php if (!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <?php if (!empty($success)): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
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
    <label>Wikipedia URL <input name="wikipedia_url" placeholder="https://en.wikipedia.org/wiki/Orion_Nebula"></label>
    <button type="submit">Upload</button>
  </form>
  <p><a href="<?= htmlspecialchars($config['admin_route']) ?>/logout">Sign out</a></p>
</section>
