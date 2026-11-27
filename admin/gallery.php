<?php
$adm_title = 'Gallery'; $adm_current = 'gallery';
require_once __DIR__ . '/../includes/bootstrap.php';
ensure_users(); require_login();
$cats = categories();
$items = load_json('gallery', array());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { flash('Session expired. Please try again.', 'bad'); header('Location: gallery.php'); exit; }
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
    if ($action === 'upload') {
        list($path, $uerr) = handle_upload('photo');
        if ($uerr || !$path) { flash($uerr ? $uerr : 'Please choose an image to upload.', 'bad'); }
        else {
            $cat = in_array($_POST['category'], $cats, true) ? $_POST['category'] : 'Campus';
            $title = trim((string)$_POST['title']);
            $items[] = array('id' => next_id($items), 'file' => $path, 'title' => $title !== '' ? $title : 'New photograph', 'desc' => trim((string)$_POST['desc']), 'category' => $cat, 'visible' => true);
            save_json('gallery', $items);
            flash('Photo added to the gallery.');
        }
    } elseif ($action === 'save') {
        foreach ($items as $i => $it) {
            if ((int)$it['id'] === $id) {
                $items[$i]['title'] = trim((string)$_POST['title']);
                $items[$i]['desc'] = trim((string)$_POST['desc']);
                if (in_array($_POST['category'], $cats, true)) { $items[$i]['category'] = $_POST['category']; }
                $items[$i]['visible'] = !empty($_POST['visible']);
            }
        }
        save_json('gallery', $items);
        flash('Gallery item saved.');
    } elseif ($action === 'delete') {
        $keep = array();
        foreach ($items as $it) {
            if ((int)$it['id'] === $id) {
                if (strpos($it['file'], 'assets/uploads/') === 0 && is_file(ROOT_DIR . '/' . $it['file'])) { @unlink(ROOT_DIR . '/' . $it['file']); }
            } else { $keep[] = $it; }
        }
        save_json('gallery', $keep);
        flash('Gallery item deleted.');
    }
    header('Location: gallery.php'); exit;
}
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="box">
  <h2>Upload a New Photo</h2>
  <form method="post" enctype="multipart/form-data" class="adm-form">
    <?= csrf_field() ?><input type="hidden" name="action" value="upload">
    <div class="row2">
      <label>Photo (JPG, PNG, WEBP, up to 6 MB)<input type="file" name="photo" accept="image/*" required></label>
      <label>Category<select name="category"><?php foreach ($cats as $c): ?><option><?= e($c) ?></option><?php endforeach; ?></select></label>
    </div>
    <label>Title<input type="text" name="title" maxlength="100"></label>
    <label>Description<textarea name="desc" rows="2" maxlength="300"></textarea></label>
    <button class="btn btn-green" type="submit">Upload Photo</button>
  </form>
</div>
<div class="box">
  <h2>Gallery Items (<?= count($items) ?>)</h2>
  <table class="tbl">
    <tr><th>Picture</th><th>Title</th><th>Description</th><th>Category</th><th>Shown</th><th>Actions</th></tr>
    <?php foreach ($items as $it): $fid = 'f' . (int)$it['id']; ?>
    <tr>
      <td><img src="../<?= e($it['file']) ?>" alt="" loading="lazy"></td>
      <td><input form="<?= $fid ?>" type="text" name="title" value="<?= e($it['title']) ?>" maxlength="100"></td>
      <td><input form="<?= $fid ?>" type="text" name="desc" value="<?= e($it['desc']) ?>" maxlength="300"></td>
      <td><select form="<?= $fid ?>" name="category"><?php foreach ($cats as $c): ?><option<?= $it['category'] === $c ? ' selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></td>
      <td><input form="<?= $fid ?>" type="checkbox" name="visible" value="1"<?= (!isset($it['visible']) || $it['visible']) ? ' checked' : '' ?>></td>
      <td>
        <form id="<?= $fid ?>" method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)$it['id'] ?>"><button class="btn btn-green btn-sm" type="submit">Save</button></form>
        <form method="post" class="inline" onsubmit="return confirm('Delete this gallery item?');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$it['id'] ?>"><button class="btn btn-danger btn-sm" type="submit">Delete</button></form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
