</main>
<footer class="site-footer">
  <div class="wrap foot-grid">
    <div>
      <h3><?= e($S['school_name']) ?></h3>
      <p><?= e($S['motto']) ?>. A Catholic-founded secondary school forming disciplined, capable and morally upright young people since 1921.</p>
    </div>
    <div>
      <h3>Quick Links</h3>
      <ul>
        <?php foreach ($pages as $p): ?><li><a href="<?= $p[1] ?>"><?= $p[0] ?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h3>Get in Touch</h3>
      <ul class="plain">
        <li><?= e($S['address']) ?></li>
        <li><?= e($S['phone']) ?></li>
        <li><?= e($S['email']) ?></li>
        <li><?= e($S['hours_weekday']) ?><br><?= e($S['hours_sat']) ?></li>
      </ul>
    </div>
  </div>
  <div class="foot-bottom">
    <div class="wrap">
      <span>&copy; <?= date('Y') ?> <?= e($S['school_name']) ?>. All rights reserved.</span>
      <a class="stafflogin" href="admin/login.php">Staff &amp; Management Login</a>
    </div>
  </div>
</footer>
<script src="assets/js/main.js"></script>
</body>
</html>
