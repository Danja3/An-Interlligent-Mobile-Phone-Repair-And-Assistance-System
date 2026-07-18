<?php
require_once __DIR__ . '/auth.php';
$u = current_user();
$tech = $u ? current_technician() : null;
$pageTitle = $pageTitle ?? APP_NAME;
$cur = basename($_SERVER['SCRIPT_NAME'] ?? '');
function navlink(string $file, string $label, string $cur): void {
    $active = $cur === $file ? ' active' : '';
    echo '<a class="' . trim($active) . '" href="' . e(url('/' . $file)) . '">' . e($label) . '</a>';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="Diagnose phone faults in seconds and find verified repair technicians near you in Northern Nigeria.">
<meta name="theme-color" content="#27408b">
<link rel="icon" href="<?= e(url('/assets/img/icon.svg')) ?>" type="image/svg+xml">
<link rel="manifest" href="<?= e(url('/manifest.webmanifest')) ?>">
<link rel="stylesheet" href="<?= e(url('/assets/css/styles.css')) ?>">
</head>
<body>
<header class="site-header">
  <div class="container bar">
    <a class="brand" href="<?= e(url('/index.php')) ?>">
      <span class="logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span>
      TechAssist
    </a>
    <button class="nav-toggle" aria-label="Menu" onclick="document.getElementById('nav').classList.toggle('open')">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <nav class="nav" id="nav">
      <?php navlink('diagnose.php', 'Diagnose', $cur); ?>
      <?php navlink('technicians.php', 'Technicians', $cur); ?>
      <?php if ($u && $u['role'] === 'admin') navlink('admin.php', 'Admin', $cur); ?>
      <?php if ($u): ?>
        <a href="<?= e(url($tech ? '/dashboard.php' : '/account.php')) ?>"><?= $tech ? 'My shop' : 'Account' ?></a>
        <a href="<?= e(url('/logout.php')) ?>">Sign out</a>
      <?php else: ?>
        <a href="<?= e(url('/login.php')) ?>">Sign in</a>
        <a class="btn sm" href="<?= e(url('/diagnose.php')) ?>">Diagnose now</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<?php foreach (take_flashes() as $f): ?>
  <div class="container" style="padding-top:14px"><div class="flash <?= e($f['type']) ?>"><?= e($f['message']) ?></div></div>
<?php endforeach; ?>
