<?php
$adm_title = 'Dashboard'; $adm_current = 'index';
require __DIR__ . '/../includes/admin_header.php';
$news = load_json('news', array());
$gal = load_json('gallery', array());
$msgs = array_reverse(load_json('messages', array()));
?>
<div class="stats">
  <div class="stat"><b><?= count(load_json('slides', array())) ?></b>Home slides</div>
  <div class="stat"><b><?= count($news) ?></b>News articles</div>
  <div class="stat"><b><?= count($gal) ?></b>Gallery images</div>
  <div class="stat"><b><?= $unread ?></b>Unread messages</div>
</div>
<div class="box">
  <h2>Management Sections</h2>
  <p>Use the menu to manage every part of the website:</p>
  <table class="tbl">
    <tr><th>Section</th><th>What you can do</th></tr>
    <tr><td><a href="slides.php">Home Slides</a></td><td>Change the three homepage slides: picture, heading, description and button.</td></tr>
    <tr><td><a href="news.php">News &amp; Updates</a></td><td>Add, edit and delete articles on academics/UNEB, sports and school life.</td></tr>
    <tr><td><a href="gallery.php">Gallery</a></td><td>Upload photos, edit captions and categories, hide or delete pictures.</td></tr>
    <tr><td><a href="messages.php">Messages</a></td><td>Read, mark and delete enquiries sent through the Contact Us form.</td></tr>
    <tr><td><a href="settings.php">Settings &amp; Password</a></td><td>Update contact details, mission, vision, history, map location and your password.</td></tr>
  </table>
</div>
<div class="box">
  <h2>Recent Messages</h2>
  <?php if (!$msgs): ?><p>No messages yet.</p><?php endif; ?>
  <?php foreach (array_slice($msgs, 0, 5) as $m): ?>
    <div class="msg"><strong><?= e($m['subject']) ?></strong> from <?= e($m['name']) ?> <span class="meta">(<?= e($m['date']) ?>)</span></div>
  <?php endforeach; ?>
  <?php if ($msgs): ?><p><a href="messages.php">Open all messages</a></p><?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
