<?php
require __DIR__ . '/includes/auth.php';

$city = trim($_GET['city'] ?? '');
$skill = trim($_GET['skill'] ?? '');

// Approved technicians, optionally filtered.
$sql = 'SELECT t.* FROM technician_profiles t WHERE t.status = "approved"';
$params = [];
if ($city !== '') { $sql .= ' AND t.city = ?'; $params[] = $city; }
if ($skill !== '') {
    $sql .= ' AND EXISTS (SELECT 1 FROM technician_skills s WHERE s.technician_id = t.id AND s.skill = ?)';
    $params[] = $skill;
}
$sql .= ' ORDER BY t.avg_rating DESC, t.review_count DESC, t.business_name';
$techs = all($sql, $params);

// Skills per technician (grouped).
$skillsByTech = [];
foreach (all('SELECT s.technician_id, s.skill FROM technician_skills s
              JOIN technician_profiles t ON t.id = s.technician_id WHERE t.status = "approved"') as $r) {
    $skillsByTech[$r['technician_id']][] = $r['skill'];
}

$cities = array_column(all('SELECT DISTINCT city FROM technician_profiles WHERE status="approved" ORDER BY city'), 'city');
$skillOptions = array_column(all('SELECT DISTINCT skill FROM technician_skills s JOIN technician_profiles t ON t.id=s.technician_id WHERE t.status="approved" ORDER BY skill'), 'skill');

$pageTitle = 'Verified technicians — TechAssist';
require __DIR__ . '/includes/header.php';
?>
<main class="container section">
  <h1>Verified technicians</h1>
  <p class="muted">Every technician is reviewed and verified by real customers across Northern Nigeria.</p>

  <form method="get" class="row mt mb">
    <select name="city" onchange="this.form.submit()">
      <option value="">All cities</option>
      <?php foreach ($cities as $c): ?>
        <option value="<?= e($c) ?>" <?= $c === $city ? 'selected' : '' ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="skill" onchange="this.form.submit()">
      <option value="">All skills</option>
      <?php foreach ($skillOptions as $s): ?>
        <option value="<?= e($s) ?>" <?= $s === $skill ? 'selected' : '' ?>><?= e($s) ?></option>
      <?php endforeach; ?>
    </select>
    <?php if ($city || $skill): ?><a class="btn ghost sm" href="<?= e(url('/technicians.php')) ?>">Clear</a><?php endif; ?>
    <noscript><button class="btn sm" type="submit">Filter</button></noscript>
  </form>

  <?php if (!$techs): ?>
    <div class="card pad center">
      <h3>No technicians found</h3>
      <p class="muted">Try a different filter — or <a href="<?= e(url('/become-technician.php')) ?>">apply to be listed</a>.</p>
    </div>
  <?php else: ?>
    <div class="grid cols-2">
      <?php foreach ($techs as $t): $sk = $skillsByTech[$t['id']] ?? []; ?>
        <a class="tile" href="<?= e(url('/technician.php?id=' . $t['id'])) ?>">
          <div class="row" style="flex-wrap:nowrap;align-items:flex-start">
            <div class="avatar">
              <?php if ($t['photo_url']): ?><img src="<?= e($t['photo_url']) ?>" alt="" loading="lazy">
              <?php else: ?><?= e(mb_substr($t['business_name'], 0, 1)) ?><?php endif; ?>
            </div>
            <div class="flex1">
              <div class="row" style="gap:6px"><strong><?= e($t['business_name']) ?></strong>
                <span class="badge green">✓ Verified</span></div>
              <div class="muted" style="font-size:.85rem">📍 <?= e($t['city']) ?></div>
              <?php if ((int)$t['review_count'] > 0): ?>
                <div style="font-size:.85rem">★ <?= number_format((float)$t['avg_rating'], 1) ?> (<?= (int)$t['review_count'] ?>)</div>
              <?php endif; ?>
            </div>
          </div>
          <div class="row" style="gap:6px;margin-top:10px">
            <?php foreach (array_slice($sk, 0, 3) as $s): ?><span class="chip"><?= e($s) ?></span><?php endforeach; ?>
            <?php if (count($sk) > 3): ?><span class="muted" style="font-size:.8rem">+<?= count($sk) - 3 ?></span><?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
