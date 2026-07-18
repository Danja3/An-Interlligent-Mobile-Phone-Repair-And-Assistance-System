<?php
require __DIR__ . '/includes/auth.php';
require_login();
$u = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $bid = (int) ($_POST['booking_id'] ?? 0);
    $b = one('SELECT * FROM bookings WHERE id = ? AND customer_id = ?', [$bid, $u['id']]);

    if ($b && $action === 'cancel' && in_array($b['status'], ['requested', 'accepted'], true)) {
        q('UPDATE bookings SET status = "cancelled" WHERE id = ?', [$bid]);
        flash('Booking cancelled.');
        redirect('/bookings.php');
    }

    if ($b && $action === 'review' && $b['status'] === 'completed') {
        $rating = max(1, min(5, (int) ($_POST['rating'] ?? 0)));
        $comment = trim($_POST['comment'] ?? '') ?: null;
        $exists = one('SELECT id FROM reviews WHERE booking_id = ?', [$bid]);
        if (!$exists) {
            q('INSERT INTO reviews (booking_id, technician_id, customer_id, rating, comment) VALUES (?,?,?,?,?)',
                [$bid, $b['technician_id'], $u['id'], $rating, $comment]);
            // Recalculate the technician's aggregate rating.
            q('UPDATE technician_profiles t SET
                 avg_rating  = (SELECT ROUND(AVG(rating),2) FROM reviews WHERE technician_id = t.id),
                 review_count = (SELECT COUNT(*) FROM reviews WHERE technician_id = t.id)
               WHERE t.id = ?', [$b['technician_id']]);
            flash('Thanks for your review!');
        }
        redirect('/bookings.php');
    }
    redirect('/bookings.php');
}

$rows = all('SELECT b.*, t.business_name, t.city, t.whatsapp_number,
                    (SELECT COUNT(*) FROM reviews r WHERE r.booking_id = b.id) AS reviewed
             FROM bookings b JOIN technician_profiles t ON t.id = b.technician_id
             WHERE b.customer_id = ? ORDER BY b.created_at DESC', [$u['id']]);

$pillClass = ['requested'=>'indigo','accepted'=>'green','declined'=>'clay','in_progress'=>'indigo','completed'=>'green','cancelled'=>'gray'];
$pageTitle = 'My bookings — TechAssist';
require __DIR__ . '/includes/header.php';
?>
<main class="container mid section">
  <a class="muted" href="<?= e(url('/account.php')) ?>">← Account</a>
  <h1 class="mt">My bookings</h1>

  <?php if (!$rows): ?>
    <div class="card pad center mt">
      <p class="muted">You haven't booked any repairs yet.</p>
      <a class="btn mt" href="<?= e(url('/technicians.php')) ?>">Find a technician</a>
    </div>
  <?php else: ?>
    <div class="stack mt">
      <?php foreach ($rows as $b): ?>
        <div class="card pad">
          <div class="spread">
            <div>
              <strong><?= e($b['business_name']) ?></strong> <span class="muted">· <?= e($b['city']) ?></span>
              <div class="muted" style="font-size:.9rem"><?= $b['model_label'] ? e($b['model_label']) . ' · ' : '' ?><?= e($b['symptom_label'] ?: 'Repair request') ?></div>
              <?php if ($b['customer_notes']): ?><p style="margin:8px 0 0"><?= e($b['customer_notes']) ?></p><?php endif; ?>
              <?php if ($b['technician_notes']): ?><p class="muted" style="margin:6px 0 0;font-size:.88rem"><strong>Technician:</strong> <?= e($b['technician_notes']) ?></p><?php endif; ?>
            </div>
            <span class="badge <?= e($pillClass[$b['status']] ?? 'gray') ?>"><?= e(strtoupper(str_replace('_', ' ', $b['status']))) ?></span>
          </div>
          <div class="row mt" style="gap:8px">
            <a class="btn ghost sm" href="<?= e(url('/technician.php?id=' . $b['technician_id'])) ?>">View shop</a>
            <?php if ($b['status'] === 'accepted'): ?>
              <a class="btn gold sm" target="_blank" rel="noopener" href="https://wa.me/<?= e(preg_replace('/\D/', '', $b['whatsapp_number'])) ?>">WhatsApp</a>
            <?php endif; ?>
            <?php if (in_array($b['status'], ['requested', 'accepted'], true)): ?>
              <form method="post" data-confirm="Cancel this booking?" style="margin-left:auto">
                <?= csrf_field() ?><input type="hidden" name="action" value="cancel"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                <button class="btn ghost sm" type="submit" style="color:var(--danger)">Cancel</button>
              </form>
            <?php elseif ($b['status'] === 'completed' && !$b['reviewed']): ?>
              <details style="margin-left:auto">
                <summary class="btn sm" style="list-style:none">Leave review</summary>
                <form method="post" class="card pad stack mt" style="position:absolute;z-index:5;max-width:340px">
                  <?= csrf_field() ?><input type="hidden" name="action" value="review"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                  <strong>Rate <?= e($b['business_name']) ?></strong>
                  <input type="hidden" name="rating" value="5">
                  <div class="stars" data-stars style="cursor:pointer">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                      <button type="button" class="on" style="background:none;border:0;padding:0;cursor:pointer;color:var(--gold-500)"><svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><polygon points="12 2 15 9 22 9.3 17 14 18.5 21 12 17.3 5.5 21 7 14 2 9.3 9 9"/></svg></button>
                    <?php endfor; ?>
                  </div>
                  <textarea name="comment" rows="3" maxlength="1000" placeholder="Optional comment"></textarea>
                  <button class="btn sm" type="submit">Submit review</button>
                </form>
              </details>
            <?php elseif ($b['status'] === 'completed' && $b['reviewed']): ?>
              <span class="muted" style="margin-left:auto;font-size:.85rem">Review submitted ✓</span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
