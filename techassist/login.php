<?php
require __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect(current_technician() ? '/dashboard.php' : '/account.php');
}

$redirectTo = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = one('SELECT * FROM users WHERE email = ?', [$email]);
    if ($user && password_verify($password, $user['password_hash'])) {
        login_user($user);
        flash('Welcome back, ' . $user['name'] . '!');
        // Only allow internal redirects.
        if ($redirectTo && str_starts_with($redirectTo, '/')) {
            header('Location: ' . $redirectTo);
            exit;
        }
        // Query directly (current_user() was cached as null before login this request).
        $isTech = one('SELECT id FROM technician_profiles WHERE user_id = ?', [$user['id']]);
        redirect($isTech ? '/dashboard.php' : '/account.php');
    }
    $error = 'Incorrect email or password.';
}

$pageTitle = 'Sign in — TechAssist';
require __DIR__ . '/includes/header.php';
?>
<main class="container narrow section">
  <div class="card pad">
    <h1>Welcome back</h1>
    <p class="muted">Sign in to book technicians and manage your repairs.</p>
    <?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="stack mt">
      <?= csrf_field() ?>
      <input type="hidden" name="redirect" value="<?= e($redirectTo) ?>">
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" required autofocus value="<?= e($_POST['email'] ?? '') ?>">
      </div>
      <div class="field">
        <div class="spread"><label>Password</label>
          <a href="<?= e(url('/forgot-password.php')) ?>" style="font-size:.8rem">Forgot password?</a></div>
        <input type="password" name="password" required minlength="8">
      </div>
      <button class="btn full" type="submit">Sign in</button>
    </form>
    <p class="muted center mt">New to TechAssist?
      <a href="<?= e(url('/register.php')) ?>">Create an account</a></p>
    <p class="muted center" style="font-size:.85rem">Are you a repair pro?
      <a href="<?= e(url('/become-technician.php')) ?>">Apply here</a></p>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
