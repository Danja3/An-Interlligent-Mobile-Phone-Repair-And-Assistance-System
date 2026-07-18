<?php
require __DIR__ . '/includes/auth.php';
require_login();

$u = current_user();
$tech = current_technician();

// --- Handle POST actions ---
if ($tech && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'booking') {
        $bid = (int) ($_POST['booking_id'] ?? 0);
        $new = $_POST['status'] ?? '';
        $note = trim($_POST['technician_notes'] ?? '') ?: null;
        $b = one('SELECT * FROM bookings WHERE id = ? AND technician_id = ?', [$bid, $tech['id']]);
        $allowed = [
            'requested'   => ['accepted', 'declined'],
            'accepted'    => ['in_progress'],
            'in_progress' => ['completed'],
        ];
        if ($b && in_array($new, $allowed[$b['status']] ?? [], true)) {
            q('UPDATE bookings SET status = ?, technician_notes = COALESCE(?, technician_notes) WHERE id = ?', [$new, $note, $bid]);
            flash('Booking updated.');
        }
        redirect('/dashboard.php');
    }

    if ($action === 'profile') {
        $city = trim($_POST['city'] ?? '');
        $address = trim($_POST['shop_address'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $whatsapp = normalize_phone(trim($_POST['whatsapp'] ?? ''));
        $skills = array_values(array_intersect(SKILLS, $_POST['skills'] ?? []));
        $photo = save_photo($_FILES['photo'] ?? []);
        if (!$whatsapp) { flash('Enter a valid Nigerian WhatsApp number.', 'error'); redirect('/dashboard.php'); }
        if ($photo['error']) { flash($photo['error'], 'error'); redirect('/dashboard.php'); }

        q('UPDATE technician_profiles SET bio=?, shop_address=?, city=?, whatsapp_number=?, photo_url=COALESCE(?, photo_url) WHERE id=?',
            [$bio ?: null, $address, $city, $whatsapp, $photo['url'], $tech['id']]);
        q('DELETE FROM technician_skills WHERE technician_id = ?', [$tech['id']]);
        if ($skills) {
            $ins = db()->prepare('INSERT INTO technician_skills (technician_id, skill) VALUES (?,?)');
            foreach ($skills as $s) $ins->execute([$tech['id'], $s]);
        }
        flash('Profile updated.');
        redirect('/dashboard.php');
    }
}

$pageTitle = 'Technician dashboard — TechAssist';
require __DIR__ . '/includes/header.php';

if (!$tech): ?>
  <main class="container narrow section center">
    <h1>You haven't applied yet</h1>
    <p class="muted">Apply to become a verified technician on TechAssist.</p>
    <a class="btn mt" href="<?= e(url('/become-technician.php')) ?>">Apply now</a>
  </main>
<?php else:
  $mySkills = array_column(all('SELECT skill FROM technician_skills WHERE technician_id = ?', [$tech['id']]), 'skill');
  $bookings = all('SELECT b.*, u.name AS customer_name FROM bookings b JOIN users u ON u.id = b.customer_id
                   WHERE b.technician_id = ? ORDER BY b.created_at DESC', [$tech['id']]);
  $statusBadge = ['pending' => 'gold', 'approved' => 'green', 'rejected' => 'clay'];
?>
  <main class="container section">
    <div class="spread">
      <div><h1 style="margin:0"><?= e($tech['business_name']) ?></h1>
        <div class="muted"><?= e($tech['shop_address']) ?>, <?= e($tech['city']) ?></div></div>
      <span class="badge <?= e($statusBadge[$tech['status']] ?? 'gray') ?>"><?= e(strtoupper($tech['status'])) ?></span>
    </div>

    <?php if ($tech['status'] === 'pending'): ?>
      <div class="flash info mt">Your application is under review. You'll be listed once approved.</div>
    <?php endif; ?>

    <div class="grid cols-2 mt">
      <div class="card pad">
        <div class="avatar" style="margin-bottom:8px">
          <?php if ($tech['photo_url']): ?><img src="<?= e($tech['photo_url']) ?>" alt="">
          <?php else: ?><?= e(mb_substr($tech['business_name'], 0, 1)) ?><?php endif; ?>
        </div>
        <strong>Skills</strong>
        <div class="row" style="gap:6px;margin-top:6px">
          <?php foreach ($mySkills as $s): ?><span class="chip"><?= e($s) ?></span><?php endforeach; ?>
        </div>
      </div>
      <div class="card pad">
        <strong>Reviews</strong>
        <div class="price"><?= number_format((float)$tech['avg_rating'], 1) ?>
          <span class="muted" style="font-size:.9rem;font-weight:400">(<?= (int)$tech['review_count'] ?>)</span></div>
        <?php if ($tech['status'] === 'approved'): ?>
          <a href="<?= e(url('/technician.php?id=' . $tech['id'])) ?>" style="font-size:.9rem">View public profile →</a>
        <?php endif; ?>
      </div>
    </div>

    <details class="card pad mt">
      <summary style="cursor:pointer;font-weight:600">Edit profile, photo & skills</summary>
      <form method="post" enctype="multipart/form-data" class="stack mt">
        <?= csrf_field() ?><input type="hidden" name="action" value="profile">
        <div class="field"><label>Shop photo</label><input type="file" name="photo" accept="image/*"></div>
        <div class="field"><label>City</label>
          <select name="city">
            <?php foreach (CITIES as $c): ?><option <?= $c === $tech['city'] ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
          </select></div>
        <div class="field"><label>Shop address</label>
          <input type="text" name="shop_address" maxlength="200" value="<?= e($tech['shop_address']) ?>"></div>
        <div class="field"><label>WhatsApp number</label>
          <input type="tel" name="whatsapp" value="<?= e($tech['whatsapp_number']) ?>"></div>
        <div class="field"><label>Bio</label>
          <textarea name="bio" rows="3" maxlength="500"><?= e($tech['bio']) ?></textarea></div>
        <div class="field"><label>Skills</label>
          <div class="row" style="gap:8px;margin-top:6px">
            <?php foreach (SKILLS as $s): ?>
              <label class="chip-btn"><input type="checkbox" name="skills[]" value="<?= e($s) ?>" style="margin-right:6px;width:auto" <?= in_array($s, $mySkills, true) ? 'checked' : '' ?>><?= e($s) ?></label>
            <?php endforeach; ?>
          </div></div>
        <button class="btn" type="submit">Save changes</button>
      </form>
    </details>

    <section class="mt">
      <h2>Booking requests</h2>
      <?php if (!$bookings): ?>
        <p class="muted">No bookings yet. Once approved, customers can book repairs with you.</p>
      <?php else: ?>
        <div class="stack">
          <?php foreach ($bookings as $b): ?>
            <div class="card pad">
              <div class="spread">
                <div>
                  <strong><?= e($b['model_label'] ?: 'Repair request') ?><?= $b['symptom_label'] ? ' — ' . e($b['symptom_label']) : '' ?></strong>
                  <div class="muted" style="font-size:.82rem"><?= e($b['customer_name']) ?> · <?= e(date('M j, Y g:i a', strtotime($b['created_at']))) ?></div>
                  <?php if ($b['customer_notes']): ?><p style="margin:8px 0 0"><?= e($b['customer_notes']) ?></p><?php endif; ?>
                  <?php if ($b['technician_notes']): ?><p class="muted" style="margin:6px 0 0;font-size:.88rem"><strong>Your note:</strong> <?= e($b['technician_notes']) ?></p><?php endif; ?>
                </div>
                <span class="badge gray"><?= e(strtoupper(str_replace('_', ' ', $b['status']))) ?></span>
              </div>
              <?php if (in_array($b['status'], ['requested', 'accepted', 'in_progress'], true)): ?>
                <form method="post" class="row mt" style="gap:8px;align-items:flex-end">
                  <?= csrf_field() ?><input type="hidden" name="action" value="booking">
                  <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                  <?php if (in_array($b['status'], ['accepted', 'in_progress'], true)): ?>
                    <div class="flex1"><label style="font-size:.8rem">Note for customer (optional)</label>
                      <input type="text" name="technician_notes" maxlength="500" value="<?= e($b['technician_notes']) ?>"></div>
                  <?php endif; ?>
                  <?php if ($b['status'] === 'requested'): ?>
                    <button class="btn sm" name="status" value="accepted">Accept</button>
                    <button class="btn ghost sm" name="status" value="declined">Decline</button>
                  <?php elseif ($b['status'] === 'accepted'): ?>
                    <button class="btn sm" name="status" value="in_progress">Mark in progress</button>
                  <?php elseif ($b['status'] === 'in_progress'): ?>
                    <button class="btn sm" name="status" value="completed">Mark completed</button>
                  <?php endif; ?>
                </form>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
