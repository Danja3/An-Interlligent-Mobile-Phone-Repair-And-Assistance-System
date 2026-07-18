<?php
require __DIR__ . '/includes/auth.php';

$brand = one('SELECT * FROM phone_brands WHERE slug = ?', [$_GET['brand'] ?? '']);
$model = one('SELECT * FROM phone_models WHERE slug = ?', [$_GET['model'] ?? '']);
$symptom = one('SELECT * FROM symptoms WHERE slug = ?', [$_GET['symptom'] ?? '']);

$dx = null;
if ($symptom) {
    if ($model) {
        $dx = one('SELECT * FROM diagnoses WHERE symptom_id = ? AND model_id = ?', [$symptom['id'], $model['id']]);
    }
    if (!$dx) {
        $dx = one('SELECT * FROM diagnoses WHERE symptom_id = ? AND model_id IS NULL', [$symptom['id']]);
    }
}

$sevClass = ['low' => 'green', 'medium' => 'indigo', 'high' => 'clay'];
$pageTitle = ($symptom['name'] ?? 'Diagnosis') . ' — TechAssist';
require __DIR__ . '/includes/header.php';
?>
<main class="container mid section">
  <a class="muted" href="<?= e(url('/diagnose.php') . '?' . http_build_query(['brand' => $_GET['brand'] ?? '', 'model' => $_GET['model'] ?? ''])) ?>">← Back</a>

  <?php if (!$symptom || !$dx): ?>
    <div class="card pad center mt">
      <p class="muted">We couldn't find a diagnosis for this combination yet.</p>
      <a class="btn mt" href="<?= e(url('/diagnose.php')) ?>">Try again</a>
    </div>
  <?php else: ?>
    <div class="muted mt" style="font-size:.9rem"><?= e($brand['name'] ?? '') ?> · <?= e($model['name'] ?? '') ?></div>
    <h1 style="margin-top:4px"><?= e($symptom['name']) ?></h1>
    <span class="badge <?= e($sevClass[$dx['severity']] ?? 'indigo') ?>"><?= e(strtoupper($dx['severity'])) ?> SEVERITY</span>

    <section class="card pad mt">
      <h3>⚠ Probable cause</h3>
      <p style="margin:0"><?= e($dx['probable_cause']) ?></p>
    </section>

    <?php if (!empty($dx['diy_solution'])): ?>
    <section class="card pad mt">
      <h3 style="color:var(--success)">✓ Try this first (DIY)</h3>
      <p style="margin:0;white-space:pre-line"><?= e($dx['diy_solution']) ?></p>
    </section>
    <?php endif; ?>

    <section class="card pad mt">
      <h3>🔧 Estimated repair cost</h3>
      <div class="price">
        <?php if ((int)$dx['cost_min'] === 0 && (int)$dx['cost_max'] === 0): ?>
          Likely free / DIY
        <?php else: ?>
          <?= e(money((int)$dx['cost_min'], $dx['currency'])) ?> – <?= e(money((int)$dx['cost_max'], $dx['currency'])) ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($dx['required_skill'])): ?>
        <div class="muted" style="font-size:.9rem;margin-top:4px">Skill needed: <?= e($dx['required_skill']) ?></div>
      <?php endif; ?>
    </section>

    <?php if ((int)$dx['requires_technician'] === 1): ?>
      <div class="cta-band mt">
        <h2>Need a technician?</h2>
        <p style="color:rgba(255,255,255,.88)">Find a verified repair pro near you<?= !empty($dx['required_skill']) ? ' for ' . e($dx['required_skill']) : '' ?>.</p>
        <a class="btn gold" style="margin-top:8px" href="<?= e(url('/technicians.php') . ($dx['required_skill'] ? '?skill=' . urlencode($dx['required_skill']) : '')) ?>">Find verified technicians</a>
      </div>
    <?php endif; ?>

    <p class="muted center mt" style="font-size:.8rem">This diagnostic is informational. Always confirm with a qualified technician before any hardware repair.</p>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
