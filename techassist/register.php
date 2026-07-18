<?php
require __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('/account.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if (!$errors && one('SELECT id FROM users WHERE email = ?', [$email])) {
        $errors[] = 'An account with that email already exists.';
    }

    if (!$errors) {
        q('INSERT INTO users (name, email, password_hash, role) VALUES (?,?,?,?)',
            [$name, $email, password_hash($password, PASSWORD_DEFAULT), 'customer']);
        $user = one('SELECT * FROM users WHERE email = ?', [$email]);
        login_user($user);
        flash('Account created. Welcome to TechAssist!');
        redirect('/account.php');
    }
}

$pageTitle = 'Create account — TechAssist';
require __DIR__ . '/includes/header.php';
?>
<main class="container narrow section">
  <div class="card pad">
    <h1>Create your account</h1>
    <p class="muted">It's free and takes 30 seconds.</p>
    <?php foreach ($errors as $err): ?><div class="flash error"><?= e($err) ?></div><?php endforeach; ?>
    <form method="post" class="stack mt">
      <?= csrf_field() ?>
      <div class="field"><label>Name</label>
        <input type="text" name="name" required maxlength="100" value="<?= e($_POST['name'] ?? '') ?>"></div>
      <div class="field"><label>Email</label>
        <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></div>
      <div class="field"><label>Password</label>
        <input type="password" name="password" required minlength="8" placeholder="At least 8 characters"></div>
      <button class="btn full" type="submit">Create account</button>
    </form>
    <p class="muted center mt">Already have an account?
      <a href="<?= e(url('/login.php')) ?>">Sign in</a></p>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
