<?php
$adm_title = 'News & Updates'; $adm_current = 'news';
require_once __DIR__ . '/../includes/bootstrap.php';
ensure_users(); require_login();
$cats = array('Academics & UNEB', 'Sports', 'School Life');
$news = load_json('news', array());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { flash('Session expired. Please try again.', 'bad'); header('Location: news.php'); exit; }
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
    if ($action === 'delete') {
        $news = array_values(array_filter($news, function ($n) use ($id) { return (int)$n['id'] !== $id; }));
        save_json('news', $news);
        flash('Article deleted.');
    } elseif ($action === 'save') {
        $title = trim((string)$_POST['title']);
        $body = trim((string)$_POST['body']);
        $cat = in_array($_POST['category'], $cats, true) ? $_POST['category'] : 'School Life';
        $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$_POST['date']) ? $_POST['date'] : date('Y-m-d');
        $excerpt = trim((string)$_POST['excerpt']);
        if ($title === '' || $body === '') {
            flash('Title and article text are required.', 'bad');
            header('Location: news.php' . ($id ? '?edit=' . $id : '')); exit;
        }
        if ($excerpt === '') { $excerpt = substr($body, 0, 150) . '...'; }
        $image = '';
        $chosen = isset($_POST['image']) ? $_POST['image'] : '';
        foreach (gallery_items(false) as $g) { if ($g['file'] === $chosen) { $image = $chosen; } }
        list($path, $uerr) = handle_upload('upload');
        if ($uerr) { flash($uerr, 'bad'); header('Location: news.php' . ($id ? '?edit=' . $id : '')); exit; }
        if ($path) { $image = $path; }
        $found = false;
        foreach ($news as $i => $n) {
            if ((int)$n['id'] === $id) {
                $news[$i] = array('id' => $id, 'category' => $cat, 'date' => $date, 'image' => $image ? $image : $n['image'], 'title' => $title, 'excerpt' => $excerpt, 'body' => $body);
                $found = true;
            }
        }
        if (!$found) {
            $news[] = array('id' => next_id($news), 'category' => $cat, 'date' => $date, 'image' => $image ? $image : 'assets/img/img39.jpg', 'title' => $title, 'excerpt' => $excerpt, 'body' => $body);
        }
        save_json('news', $news);
        flash($found ? 'Article updated.' : 'Article published.');
    }
    header('Location: news.php'); exit;
}

$edit = null;
if (isset($_GET['edit'])) { foreach ($news as $n) { if ((int)$n['id'] === (int)$_GET['edit']) { $edit = $n; } } }
usort($news, function ($a, $b) { return strcmp($b['date'], $a['date']); });
require __DIR__ . '/../includes/admin_header.php';
$v = $edit ? $edit : array('id' => 0, 'category' => 'Academics & UNEB', 'date' => date('Y-m-d'), 'image' => '', 'title' => '', 'excerpt' => '', 'body' => '');
?>
<div class="box">
  <h2><?= $edit ? 'Edit Article' : 'Add New Article' ?></h2>
  <form method="post" enctype="multipart/form-data" class="adm-form">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
    <label>Headline<input type="text" name="title" value="<?= e($v['title']) ?>" maxlength="160" required></label>
    <div class="row2">
      <label>Category<select name="category"><?php foreach ($cats as $c): ?><option<?= $v['category'] === $c ? ' selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></label>
      <label>Date<input type="date" name="date" value="<?= e($v['date']) ?>" required></label>
    </div>
    <div class="row2">
      <label>Picture from gallery<select name="image"><option value="">(keep current / default)</option><?= image_options($v['image']) ?></select></label>
      <label>Or upload a picture<input type="file" name="upload" accept="image/*"></label>
    </div>
    <?php if ($v['image']): ?><img class="thumb" src="../<?= e($v['image']) ?>" alt=""><?php endif; ?>
    <label>Short summary (shown on cards)<textarea name="excerpt" rows="2" maxlength="260"><?= e($v['excerpt']) ?></textarea></label>
    <label>Full article (leave a blank line between paragraphs)<textarea name="body" rows="8" required><?= e($v['body']) ?></textarea></label>
    <button class="btn btn-green" type="submit"><?= $edit ? 'Update Article' : 'Publish Article' ?></button>
    <?php if ($edit): ?><a class="btn btn-grey" href="news.php">Cancel</a><?php endif; ?>
  </form>
</div>
<div class="box">
  <h2>All Articles (<?= count($news) ?>)</h2>
  <table class="tbl">
    <tr><th>Picture</th><th>Headline</th><th>Category</th><th>Date</th><th>Actions</th></tr>
    <?php foreach ($news as $n): ?>
    <tr>
      <td><img src="../<?= e($n['image']) ?>" alt=""></td>
      <td><?= e($n['title']) ?></td><td><?= e($n['category']) ?></td><td><?= e($n['date']) ?></td>
      <td>
        <a class="btn btn-green btn-sm" href="news.php?edit=<?= (int)$n['id'] ?>">Edit</a>
        <form method="post" class="inline" onsubmit="return confirm('Delete this article permanently?');">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
          <button class="btn btn-danger btn-sm" type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
