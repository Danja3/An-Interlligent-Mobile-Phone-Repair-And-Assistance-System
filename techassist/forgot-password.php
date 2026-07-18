<?php
require __DIR__ . '/includes/auth.php';

$link = null;
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $user = one('SELECT * FROM users WHERE email = ?', [$email]);
    if ($user) {
        $raw = bin2hex(random_bytes(32));
        q('DELETE FROM password_resets WHERE user_id = ?', [$user['id']]);
        q('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?, DATE_ADD(NOW(), INTERVAL 1 HOUR))',
            [$user['id'], hash('sha256', $raw)]);
        // No mail server in dev — show the link directly so the flow is testable.
        $scheme = (($_SERVER['HTTPS'] ?? '') === 'on') ? 'https' : 'http';
        $link = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('/reset-password.php?token=' . $raw);
    }
    $sent = true;
}

$pageTitle = 'Forgot password — TechAssist';
require __DIR__ . '/includes/header.php';
?>
<main class="container narrow section">
  <div class="card pad">
    <h1>Reset your password</h1>
    <?php if ($sent): ?>
      <div class="flash success">If an account exists for that email, a reset link has been created.</div>
      <?php if ($link): ?>
        <p class="muted" style="font-size:.85rem">Demo mode (no email server): use this link to reset your password.</p>
        <a href="<?= e($link) ?>" class="btn full">Reset my password</a>
      <?php endif; ?>
    <?php else: ?>
      <p class="muted">Enter your email and we'll send you a link to set a new password.</p>
      <form method="post" class="stack mt">
        <?= csrf_field() ?>
        <div class="field"><label>Email</label><input type="email" name="email" required autofocus></div>
        <button class="btn full" type="submit">Send reset link</button>
      </form>
    <?php endif; ?>
    <p class="muted center mt"><a href="<?= e(url('/login.php')) ?>">Back to sign in</a></p>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
