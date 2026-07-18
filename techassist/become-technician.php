<?php
require __DIR__ . '/includes/auth.php';
require_login();

$u = current_user();
$existing = current_technician();
$errors = [];

if (!$existing && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $business = trim($_POST['business_name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['shop_address'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $whatsappRaw = trim($_POST['whatsapp'] ?? '');
    $skills = array_values(array_intersect(SKILLS, $_POST['skills'] ?? []));

    if ($business === '') $errors[] = 'Enter your business / shop name.';
    if ($city === '') $errors[] = 'Choose your city.';
    if ($address === '') $errors[] = 'Enter your shop address.';
    $whatsapp = normalize_phone($whatsappRaw);
    if (!$whatsapp) $errors[] = 'Enter a valid Nigerian WhatsApp number, e.g. 08012345678.';
    if (!$skills) $errors[] = 'Pick at least one skill.';

    $photo = save_photo($_FILES['photo'] ?? []);
    if ($photo['error']) $errors[] = $photo['error'];

    if (!$errors) {
        q('INSERT INTO technician_profiles (user_id, business_name, bio, shop_address, city, whatsapp_number, photo_url, status)
           VALUES (?,?,?,?,?,?,?, "pending")',
            [$u['id'], $business, $bio ?: null, $address, $city, $whatsapp, $photo['url']]);
        $techId = (int) db()->lastInsertId();
        $ins = db()->prepare('INSERT INTO technician_skills (technician_id, skill) VALUES (?,?)');
        foreach ($skills as $s) $ins->execute([$techId, $s]);
        q('UPDATE users SET role = "technician" WHERE id = ? AND role <> "admin"', [$u['id']]);
        flash('Application submitted! Our team will review it shortly.');
        redirect('/dashboard.php');
    }
}

$pageTitle = 'Become a verified technician — TechAssist';
require __DIR__ . '/includes/header.php';

if ($existing): ?>
  <main class="container narrow section">
    <div class="card pad center">
      <h1>Application status: <?= e(ucfirst($existing['status'])) ?></h1>
      <p class="muted">
        <?php if ($existing['status'] === 'pending'): ?>We're reviewing your application. You'll be listed once approved.
        <?php elseif ($existing['status'] === 'approved'): ?>You're verified! Your shop is live on TechAssist.
        <?php else: ?>Your application was not approved. Contact support for details.<?php endif; ?>
      </p>
      <a class="btn mt" href="<?= e(url('/dashboard.php')) ?>">Go to dashboard</a>
    </div>
  </main>
<?php else: ?>
  <main class="container narrow section">
    <h1>Become a verified technician</h1>
    <p class="muted">Tell us about your shop. Our team will review and approve it.</p>
    <?php foreach ($errors as $err): ?><div class="flash error"><?= e($err) ?></div><?php endforeach; ?>
    <form method="post" enctype="multipart/form-data" class="card pad stack mt">
      <?= csrf_field() ?>
      <div class="field"><label>Business / Shop name</label>
        <input type="text" name="business_name" required maxlength="120" value="<?= e($_POST['business_name'] ?? '') ?>"></div>
      <div class="field"><label>City</label>
        <select name="city" required>
          <option value="">Choose a city…</option>
          <?php foreach (CITIES as $c): ?><option <?= ($_POST['city'] ?? '') === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
        </select></div>
      <div class="field"><label>Shop address</label>
        <input type="text" name="shop_address" required maxlength="200" value="<?= e($_POST['shop_address'] ?? '') ?>"></div>
      <div class="field"><label>WhatsApp number</label>
        <input type="tel" name="whatsapp" required placeholder="e.g. 08012345678" value="<?= e($_POST['whatsapp'] ?? '') ?>"></div>
      <div class="field"><label>Short bio</label>
        <textarea name="bio" rows="4" maxlength="500" placeholder="Years of experience, what you specialize in, etc."><?= e($_POST['bio'] ?? '') ?></textarea></div>
      <div class="field"><label>Shop photo (optional)</label>
        <input type="file" name="photo" accept="image/*"></div>
      <div class="field"><label>Skills (pick all that apply)</label>
        <div class="row" style="gap:8px;margin-top:6px">
          <?php foreach (SKILLS as $s): ?>
            <label class="chip-btn"><input type="checkbox" name="skills[]" value="<?= e($s) ?>" style="margin-right:6px;width:auto"><?= e($s) ?></label>
          <?php endforeach; ?>
        </div></div>
      <button class="btn full" type="submit">Submit application</button>
    </form>
  </main>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
