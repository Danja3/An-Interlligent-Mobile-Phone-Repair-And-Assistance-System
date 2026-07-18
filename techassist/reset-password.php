<?php
require __DIR__ . '/includes/auth.php';

$raw = $_POST['token'] ?? ($_GET['token'] ?? '');
$row = $raw ? one('SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()', [hash('sha256', $raw)]) : null;
$errors = [];

if ($row && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $pw = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (strlen($pw) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($pw !== $confirm) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($pw, PASSWORD_DEFAULT), $row['user_id']]);
        q('DELETE FROM password_resets WHERE user_id = ?', [$row['user_id']]);
        flash('Password updated. You can sign in now.');
        redirect('/login.php');
    }
}

$pageTitle = 'Set a new password — TechAssist';
require __DIR__ . '/includes/header.php';
?>
<main class="container narrow section">
  <div class="card pad">
    <h1>Set a new password</h1>
    <?php if (!$row): ?>
      <p class="muted">This reset link is invalid or has expired. Please request a new one from the
        <a href="<?= e(url('/forgot-password.php')) ?>">forgot-password page</a>.</p>
    <?php else: ?>
      <?php foreach ($errors as $err): ?><div class="flash error"><?= e($err) ?></div><?php endforeach; ?>
      <form method="post" class="stack mt">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($raw) ?>">
        <div class="field"><label>New password</label><input type="password" name="password" required minlength="8"></div>
        <div class="field"><label>Confirm password</label><input type="password" name="confirm" required minlength="8"></div>
        <button class="btn full" type="submit">Update password</button>
      </form>
    <?php endif; ?>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
