<section class="panel narrow">
  <h1>Admin login</h1>
  <?php if (!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <label>Username <input required name="username" autocomplete="username"></label>
    <label>Password <input required type="password" name="password" autocomplete="current-password"></label>
    <label class="checkbox"><input type="checkbox" name="remember_me" value="1"> Keep me signed in on this device</label>
    <button type="submit">Sign in</button>
  </form>
</section>
