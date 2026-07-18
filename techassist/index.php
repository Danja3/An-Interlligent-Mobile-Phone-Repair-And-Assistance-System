<?php
require __DIR__ . '/includes/auth.php';
$pageTitle = 'TechAssist — Diagnose your phone & find verified technicians';
require __DIR__ . '/includes/header.php';

$props = [
    ['Instant diagnosis', 'Answer a few questions. Get a probable cause, DIY fix, and a ₦ cost range.'],
    ['Verified technicians', 'Every technician is reviewed and verified. No more shady shops.'],
    ['Near you', 'Filter by city and skill across the North. WhatsApp them in one tap.'],
];
$common = [
    ['screen', 'Screen issues', 'Cracks, unresponsive touch, lines, black screens.'],
    ['battery', 'Battery drain', 'Fast drain, overheating, random shutdowns.'],
    ['charging', 'Charging problems', "Won't charge, slow charge, loose port."],
];
?>
<main>
  <section class="hero">
    <div class="container">
      <span class="eyebrow">⚡ Free instant diagnostics</span>
      <h1>Phone acting up?<br>Get answers in 30 seconds.</h1>
      <p>Diagnose the fault, see the likely repair cost in Naira, and connect with verified technicians near you — from Kano to Maiduguri. No more guesswork, no more getting overcharged.</p>
      <div class="row" style="justify-content:center;margin-top:22px">
        <a class="btn gold" href="<?= e(url('/diagnose.php')) ?>">Diagnose my phone</a>
        <a class="btn ghost" style="color:#fff;border-color:rgba(255,255,255,.4)" href="<?= e(url('/technicians.php')) ?>">Find technicians</a>
      </div>
    </div>
  </section>

  <section class="container section">
    <div class="grid cols-3">
      <?php foreach ($props as [$t, $d]): ?>
        <div class="card pad">
          <h3><?= e($t) ?></h3>
          <p class="muted" style="margin:0"><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container section">
    <h2 class="center">Common phone problems we diagnose</h2>
    <p class="muted center mb">From cracked screens to rogue batteries — we've got you covered.</p>
    <div class="grid cols-3">
      <?php foreach ($common as [$icon, $t, $d]): ?>
        <a class="tile" href="<?= e(url('/diagnose.php')) ?>">
          <div class="choice"><div class="ic"><?= category_icon($icon) ?></div></div>
          <strong><?= e($t) ?></strong>
          <div class="muted" style="font-size:.9rem;margin-top:4px"><?= e($d) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container section">
    <div class="cta-band">
      <h2>Ready to find out what's wrong?</h2>
      <p style="color:rgba(255,255,255,.88)">It's free, takes 30 seconds, and no signup required.</p>
      <a class="btn gold" style="margin-top:10px" href="<?= e(url('/diagnose.php')) ?>">Start free diagnostic</a>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
