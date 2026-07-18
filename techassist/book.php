<?php
require __DIR__ . '/includes/auth.php';
require_login();

$id = (int)($_GET['tech'] ?? 0);
$t = one('SELECT id, business_name, city FROM technician_profiles WHERE id = ? AND status = "approved"', [$id]);

$pageTitle = 'Request a booking — TechAssist';

if ($t && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $u = current_user();
    q('INSERT INTO bookings (customer_id, technician_id, symptom_label, model_label, customer_notes, status)
       VALUES (?,?,?,?,?, "requested")', [
        $u['id'], $t['id'],
        trim($_POST['symptom_label'] ?? '') ?: null,
        trim($_POST['model_label'] ?? '') ?: null,
        trim($_POST['customer_notes'] ?? '') ?: null,
    ]);
    flash('Booking request sent to ' . $t['business_name'] . '!');
    redirect('/bookings.php');
}

require __DIR__ . '/includes/header.php';
if (!$t): ?>
  <main class="container section center"><h1>Technician not found</h1>
    <a class="btn mt" href="<?= e(url('/technicians.php')) ?>">Browse technicians</a></main>
<?php else: ?>
  <main class="container narrow section">
    <a class="muted" href="<?= e(url('/technician.php?id=' . $t['id'])) ?>">← Back to <?= e($t['business_name']) ?></a>
    <h1 class="mt">Request a booking</h1>
    <p class="muted">Send your repair request to <strong><?= e($t['business_name']) ?></strong> in <?= e($t['city']) ?>. They'll respond shortly.</p>
    <form method="post" class="card pad stack mt">
      <?= csrf_field() ?>
      <div class="field"><label>Phone model</label>
        <input type="text" name="model_label" maxlength="120" placeholder="e.g. Tecno Spark 10"></div>
      <div class="field"><label>What's the issue?</label>
        <input type="text" name="symptom_label" maxlength="150" placeholder="e.g. Cracked screen"></div>
      <div class="field"><label>Additional notes</label>
        <textarea name="customer_notes" rows="4" maxlength="1000" placeholder="When did it start? Any other details?"></textarea></div>
      <button class="btn full" type="submit">Send booking request</button>
    </form>
  </main>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
