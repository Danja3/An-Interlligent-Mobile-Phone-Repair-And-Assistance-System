<?php
require __DIR__ . '/includes/auth.php';
require_login();
$u = current_user();
$tech = current_technician();

$pageTitle = 'My account — TechAssist';
require __DIR__ . '/includes/header.php';
?>
<main class="container mid section">
  <div class="row" style="gap:14px">
    <div class="avatar"><?= e(mb_substr($u['name'], 0, 1)) ?></div>
    <div><h1 style="margin:0"><?= e($u['name']) ?></h1><div class="muted"><?= e($u['email']) ?></div></div>
  </div>

  <div class="grid cols-2 mt">
    <a class="tile" href="<?= e(url('/bookings.php')) ?>" style="grid-column:1/-1">
      <strong>My bookings</strong>
      <div class="muted" style="font-size:.9rem;margin-top:4px">Track repair requests and leave reviews.</div></a>
    <a class="tile" href="<?= e(url('/diagnose.php')) ?>">
      <strong>Run a diagnostic</strong>
      <div class="muted" style="font-size:.9rem;margin-top:4px">Find out what's wrong with your phone.</div></a>
    <a class="tile" href="<?= e(url('/technicians.php')) ?>">
      <strong>Browse technicians</strong>
      <div class="muted" style="font-size:.9rem;margin-top:4px">Find verified pros near you.</div></a>
    <?php if ($tech): ?>
      <a class="tile" href="<?= e(url('/dashboard.php')) ?>" style="grid-column:1/-1">
        <strong>Your technician dashboard</strong>
        <div class="muted" style="font-size:.9rem;margin-top:4px">Manage your shop profile, skills and bookings.</div></a>
    <?php else: ?>
      <a class="tile" href="<?= e(url('/become-technician.php')) ?>" style="grid-column:1/-1">
        <strong>Are you a repair pro?</strong>
        <div class="muted" style="font-size:.9rem;margin-top:4px">Apply to list your shop on TechAssist.</div></a>
    <?php endif; ?>
    <?php if ($u['role'] === 'admin'): ?>
      <a class="tile" href="<?= e(url('/admin.php')) ?>" style="grid-column:1/-1;border-color:color-mix(in srgb,var(--primary) 40%,var(--border));background:color-mix(in srgb,var(--primary) 6%,transparent)">
        <strong>Admin · Technician approvals</strong>
        <div class="muted" style="font-size:.9rem;margin-top:4px">Review applications and verify repair professionals.</div></a>
    <?php endif; ?>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
