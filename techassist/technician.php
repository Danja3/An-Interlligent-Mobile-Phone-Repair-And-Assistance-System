<?php
require __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$t = one('SELECT * FROM technician_profiles WHERE id = ? AND status = "approved"', [$id]);

$pageTitle = ($t['business_name'] ?? 'Technician') . ' — TechAssist';
require __DIR__ . '/includes/header.php';

if (!$t):
?>
  <main class="container section center">
    <h1>Technician not found</h1>
    <a class="btn mt" href="<?= e(url('/technicians.php')) ?>">Browse all technicians</a>
  </main>
<?php else:
  $skills = array_column(all('SELECT skill FROM technician_skills WHERE technician_id = ? ORDER BY skill', [$id]), 'skill');
  $reviews = all('SELECT r.*, u.name FROM reviews r JOIN users u ON u.id = r.customer_id
                  WHERE r.technician_id = ? ORDER BY r.created_at DESC LIMIT 10', [$id]);
  $wa = 'https://wa.me/' . preg_replace('/\D/', '', $t['whatsapp_number'])
      . '?text=' . rawurlencode("Hi {$t['business_name']}, I found you on TechAssist. I need help with my phone.");
?>
  <main class="container mid section">
    <a class="muted" href="<?= e(url('/technicians.php')) ?>">← All technicians</a>
    <div class="card pad mt">
      <div class="row" style="flex-wrap:nowrap;align-items:flex-start;gap:18px">
        <div class="avatar lg">
          <?php if ($t['photo_url']): ?><img src="<?= e($t['photo_url']) ?>" alt="">
          <?php else: ?><?= e(mb_substr($t['business_name'], 0, 1)) ?><?php endif; ?>
        </div>
        <div class="flex1">
          <div class="row" style="gap:8px"><h1 style="margin:0"><?= e($t['business_name']) ?></h1>
            <span class="badge green">✓ Verified</span></div>
          <div class="muted mt" style="font-size:.92rem">📍 <?= e($t['shop_address']) ?>, <?= e($t['city']) ?></div>
          <?php if ((int)$t['review_count'] > 0): ?>
            <div style="margin-top:6px">★ <?= number_format((float)$t['avg_rating'], 1) ?>
              <span class="muted">(<?= (int)$t['review_count'] ?> reviews)</span></div>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($t['bio']): ?>
        <div class="mt"><h3>About</h3><p style="margin:0;white-space:pre-line"><?= e($t['bio']) ?></p></div>
      <?php endif; ?>

      <div class="mt"><h3>Skills</h3>
        <div class="row" style="gap:6px">
          <?php foreach ($skills as $s): ?><span class="chip"><?= e($s) ?></span><?php endforeach; ?>
        </div>
      </div>

      <div class="grid cols-2 mt">
        <a class="btn ghost" href="<?= e($wa) ?>" target="_blank" rel="noopener">💬 WhatsApp</a>
        <?php if (is_logged_in()): ?>
          <a class="btn" href="<?= e(url('/book.php?tech=' . $t['id'])) ?>">📅 Request booking</a>
        <?php else: ?>
          <a class="btn" href="<?= e(url('/login.php?redirect=' . urlencode('/techassist/book.php?tech=' . $t['id']))) ?>">Sign in to book</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($reviews): ?>
      <section class="mt">
        <h2>Recent reviews</h2>
        <div class="stack">
          <?php foreach ($reviews as $r): ?>
            <div class="card pad">
              <div class="stars">
                <?php for ($i = 0; $i < 5; $i++): ?>
                  <svg viewBox="0 0 24 24" fill="<?= $i < (int)$r['rating'] ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2"><polygon points="12 2 15 9 22 9.3 17 14 18.5 21 12 17.3 5.5 21 7 14 2 9.3 9 9"/></svg>
                <?php endfor; ?>
              </div>
              <?php if ($r['comment']): ?><p style="margin:6px 0 0"><?= e($r['comment']) ?></p><?php endif; ?>
              <div class="muted" style="font-size:.8rem;margin-top:4px"><?= e($r['name']) ?> · <?= e(date('M j, Y', strtotime($r['created_at']))) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </main>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
