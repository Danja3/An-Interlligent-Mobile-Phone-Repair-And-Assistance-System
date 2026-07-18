<?php
require __DIR__ . '/includes/auth.php';

$brandSlug = $_GET['brand'] ?? '';
$modelSlug = $_GET['model'] ?? '';
$catSlug   = $_GET['category'] ?? '';

$brand = $brandSlug ? one('SELECT * FROM phone_brands WHERE slug = ?', [$brandSlug]) : null;
$model = ($brand && $modelSlug) ? one('SELECT * FROM phone_models WHERE slug = ? AND brand_id = ?', [$modelSlug, $brand['id']]) : null;
$category = $catSlug ? one('SELECT * FROM problem_categories WHERE slug = ?', [$catSlug]) : null;

if (!$brand) $step = 1;
elseif (!$model) $step = 2;
elseif (!$category) $step = 3;
else $step = 4;

function dlink(array $params): string {
    return url('/diagnose.php') . (empty($params) ? '' : '?' . http_build_query($params));
}

$pageTitle = 'Diagnose your phone — TechAssist';
require __DIR__ . '/includes/header.php';

$labels = ['Brand', 'Model', 'Problem', 'Symptom'];
?>
<main class="container mid section">
  <div class="steps">
    <?php foreach ($labels as $i => $lbl): $n = $i + 1; $on = $n <= $step; ?>
      <div class="dot <?= $on ? 'on' : '' ?>"><?= $n ?></div>
      <span class="lbl <?= $on ? 'on' : '' ?>"><?= e($lbl) ?></span>
      <?php if ($i < 3): ?><span style="width:18px;height:1px;background:var(--border)"></span><?php endif; ?>
    <?php endforeach; ?>
  </div>

  <?php if ($step === 1):
    $brands = all('SELECT * FROM phone_brands ORDER BY sort_order, name'); ?>
    <h1>What brand is your phone?</h1>
    <div class="grid cols-3 mt">
      <?php foreach ($brands as $b): ?>
        <a class="tile choice" href="<?= e(dlink(['brand' => $b['slug']])) ?>"><strong><?= e($b['name']) ?></strong></a>
      <?php endforeach; ?>
    </div>

  <?php elseif ($step === 2):
    $models = all('SELECT * FROM phone_models WHERE brand_id = ? ORDER BY name', [$brand['id']]); ?>
    <div class="spread"><h1>Which <?= e($brand['name']) ?> model?</h1>
      <a class="muted" href="<?= e(dlink([])) ?>">← Back</a></div>
    <div class="grid cols-3 mt">
      <?php foreach ($models as $m): ?>
        <a class="tile choice" href="<?= e(dlink(['brand' => $brand['slug'], 'model' => $m['slug']])) ?>"><strong><?= e($m['name']) ?></strong></a>
      <?php endforeach; ?>
    </div>

  <?php elseif ($step === 3):
    $cats = all('SELECT * FROM problem_categories ORDER BY sort_order'); ?>
    <div class="spread"><h1>What's the problem?</h1>
      <a class="muted" href="<?= e(dlink(['brand' => $brand['slug']])) ?>">← Back</a></div>
    <div class="grid cols-3 mt">
      <?php foreach ($cats as $c): ?>
        <a class="tile choice" href="<?= e(dlink(['brand' => $brand['slug'], 'model' => $model['slug'], 'category' => $c['slug']])) ?>">
          <div class="ic"><?= category_icon($c['icon']) ?></div>
          <strong><?= e($c['name']) ?></strong>
          <div class="muted" style="font-size:.88rem;margin-top:4px"><?= e($c['description']) ?></div>
        </a>
      <?php endforeach; ?>
    </div>

  <?php else:
    $symptoms = all('SELECT * FROM symptoms WHERE category_id = ? ORDER BY name', [$category['id']]); ?>
    <div class="spread"><h1>Pick the closest symptom</h1>
      <a class="muted" href="<?= e(dlink(['brand' => $brand['slug'], 'model' => $model['slug']])) ?>">← Back</a></div>
    <div class="stack mt">
      <?php foreach ($symptoms as $s): ?>
        <a class="tile spread" href="<?= e(url('/result.php') . '?' . http_build_query(['brand' => $brand['slug'], 'model' => $model['slug'], 'symptom' => $s['slug']])) ?>">
          <div><strong><?= e($s['name']) ?></strong>
            <div class="muted" style="font-size:.9rem"><?= e($s['description']) ?></div></div>
          <span class="muted">›</span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
