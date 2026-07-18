<footer class="site-footer">
  <div class="container cols">
    <div>
      <div class="brand" style="margin-bottom:8px"><span class="logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span> TechAssist</div>
      <p class="muted" style="margin:0">Diagnose phone faults instantly. Find verified repair technicians near you across Northern Nigeria.</p>
    </div>
    <div>
      <strong>Product</strong>
      <ul class="list-reset muted" style="margin-top:8px">
        <li><a href="<?= e(url('/diagnose.php')) ?>">Diagnose</a></li>
        <li><a href="<?= e(url('/technicians.php')) ?>">Technicians</a></li>
        <li><a href="<?= e(url('/become-technician.php')) ?>">Become a technician</a></li>
      </ul>
    </div>
    <div>
      <strong>Account</strong>
      <ul class="list-reset muted" style="margin-top:8px">
        <li><a href="<?= e(url('/login.php')) ?>">Sign in</a></li>
        <li><a href="<?= e(url('/register.php')) ?>">Create account</a></li>
      </ul>
    </div>
  </div>
  <div class="copyright">© <?= date('Y') ?> TechAssist · Built for trust in Northern Nigeria.</div>
</footer>
<script>window.__BASE__ = <?= json_encode(BASE_URL) ?>;</script>
<script src="<?= e(url('/assets/js/app.js')) ?>"></script>
</body>
</html>
