<?php
require __DIR__ . '/includes/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int) ($_POST['technician_id'] ?? 0);
    $new = $_POST['status'] ?? '';
    if (in_array($new, ['approved', 'rejected', 'pending'], true) && $id) {
        q('UPDATE technician_profiles SET status = ? WHERE id = ?', [$new, $id]);
        flash('Technician ' . $new . '.');
    }
    redirect('/admin.php?status=' . urlencode($_POST['return'] ?? 'pending'));
}

$tab = $_GET['status'] ?? 'pending';
if (!in_array($tab, ['pending', 'approved', 'rejected', 'all'], true)) $tab = 'pending';

$counts = [];
foreach (all('SELECT status, COUNT(*) c FROM technician_profiles GROUP BY status') as $r) $counts[$r['status']] = (int) $r['c'];
$counts['all'] = array_sum($counts);

$where = $tab === 'all' ? '' : 'WHERE status = ' . db()->quote($tab);
$techs = all("SELECT * FROM technician_profiles $where ORDER BY created_at DESC");

$skillsByTech = [];
foreach (all('SELECT technician_id, skill FROM technician_skills') as $r) $skillsByTech[$r['technician_id']][] = $r['skill'];

$tabs = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'];
$pageTitle = 'Admin · Technician approvals — TechAssist';
require __DIR__ . '/includes/header.php';
?>
<main class="container section">
  <h1>Technician approvals</h1>
  <p class="muted">Review applications and approve verified repair professionals.</p>

  <div class="row mt mb" style="gap:8px">
    <?php foreach ($tabs as $key => $label): ?>
      <a class="chip-btn <?= $tab === $key ? 'on' : '' ?>" href="<?= e(url('/admin.php?status=' . $key)) ?>">
        <?= e($label) ?> (<?= (int) ($counts[$key] ?? 0) ?>)</a>
    <?php endforeach; ?>
  </div>

  <?php if (!$techs): ?>
    <div class="card pad center"><p class="muted">No <?= $tab === 'all' ? '' : e($tab) ?> technicians here.</p></div>
  <?php else: ?>
    <div class="stack">
      <?php foreach ($techs as $t): $sk = $skillsByTech[$t['id']] ?? []; ?>
        <div class="card pad">
          <div class="spread">
            <div>
              <div class="row" style="gap:8px"><strong><?= e($t['business_name']) ?></strong>
                <span class="badge <?= ['pending'=>'gold','approved'=>'green','rejected'=>'clay'][$t['status']] ?>"><?= e(strtoupper($t['status'])) ?></span></div>
              <div class="muted" style="font-size:.88rem">📍 <?= e($t['shop_address']) ?>, <?= e($t['city']) ?></div>
              <a class="muted" style="font-size:.88rem" href="https://wa.me/<?= e(preg_replace('/\D/', '', $t['whatsapp_number'])) ?>" target="_blank" rel="noopener">💬 <?= e($t['whatsapp_number']) ?></a>
              <?php if ($t['bio']): ?><p style="margin:8px 0 0"><?= e($t['bio']) ?></p><?php endif; ?>
              <div class="row" style="gap:6px;margin-top:8px">
                <?php foreach ($sk as $s): ?><span class="chip"><?= e($s) ?></span><?php endforeach; ?>
              </div>
              <div class="muted" style="font-size:.78rem;margin-top:8px">Applied <?= e(date('M j, Y', strtotime($t['created_at']))) ?></div>
            </div>
          </div>
          <form method="post" class="row mt" style="gap:8px">
            <?= csrf_field() ?>
            <input type="hidden" name="technician_id" value="<?= (int)$t['id'] ?>">
            <input type="hidden" name="return" value="<?= e($tab) ?>">
            <?php if ($t['status'] !== 'approved'): ?><button class="btn sm" name="status" value="approved">✓ Approve</button><?php endif; ?>
            <?php if ($t['status'] !== 'rejected'): ?><button class="btn ghost sm" name="status" value="rejected">✕ Reject</button><?php endif; ?>
            <?php if ($t['status'] === 'approved'): ?><a class="btn ghost sm" href="<?= e(url('/technician.php?id=' . $t['id'])) ?>">View public profile</a><?php endif; ?>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
