<?php
$adm_title = 'Messages'; $adm_current = 'messages';
require_once __DIR__ . '/../includes/bootstrap.php';
ensure_users(); require_login();
$msgs = load_json('messages', array());
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { flash('Session expired. Please try again.', 'bad'); header('Location: messages.php'); exit; }
    $id = (int)$_POST['id'];
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'delete') {
        $msgs = array_values(array_filter($msgs, function ($m) use ($id) { return (int)$m['id'] !== $id; }));
        flash('Message deleted.');
    } elseif ($action === 'toggle') {
        foreach ($msgs as $i => $m) { if ((int)$m['id'] === $id) { $msgs[$i]['read'] = empty($m['read']); } }
        flash('Message updated.');
    }
    save_json('messages', $msgs);
    header('Location: messages.php'); exit;
}
require __DIR__ . '/../includes/admin_header.php';
$msgs = array_reverse($msgs);
?>
<div class="box">
  <h2>Contact Form Enquiries (<?= count($msgs) ?>)</h2>
  <?php if (!$msgs): ?><p>No messages have been received yet.</p><?php endif; ?>
  <?php foreach ($msgs as $m): ?>
  <details class="msg<?= empty($m['read']) ? ' unread' : '' ?>">
    <summary><?= e($m['subject']) ?> &mdash; <?= e($m['name']) ?> <span class="meta"><?= e($m['date']) ?><?= empty($m['read']) ? ' (new)' : '' ?></span></summary>
    <div class="meta">Email: <a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a><?= $m['phone'] !== '' ? ' | Phone: ' . e($m['phone']) : '' ?></div>
    <p><?= nl2br(e($m['message'])) ?></p>
    <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><button class="btn btn-green btn-sm" type="submit"><?= empty($m['read']) ? 'Mark as read' : 'Mark as unread' ?></button></form>
    <form method="post" class="inline" onsubmit="return confirm('Delete this message?');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><button class="btn btn-danger btn-sm" type="submit">Delete</button></form>
  </details>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
