<?php
$adm_title = 'Home Slides'; $adm_current = 'slides';
require_once __DIR__ . '/../includes/bootstrap.php';
ensure_users(); require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { flash('Session expired. Please try again.', 'bad'); header('Location: slides.php'); exit; }
    $slides = load_json('slides', array());
    $err = '';
    foreach ($slides as $i => $s) {
        $k = (string)$s['id'];
        $slides[$i]['title'] = trim((string)(isset($_POST['title'][$k]) ? $_POST['title'][$k] : $s['title']));
        $slides[$i]['text']  = trim((string)(isset($_POST['text'][$k]) ? $_POST['text'][$k] : $s['text']));
        $slides[$i]['btn']   = trim((string)(isset($_POST['btn'][$k]) ? $_POST['btn'][$k] : $s['btn']));
        $link = trim((string)(isset($_POST['link'][$k]) ? $_POST['link'][$k] : $s['link']));
        $slides[$i]['link']  = preg_match('/^(https?:\/\/|[a-z0-9_\-]+\.php)/i', $link) ? $link : $s['link'];
        $chosen = isset($_POST['image'][$k]) ? $_POST['image'][$k] : $s['image'];
        $valid = false;
        foreach (gallery_items(false) as $g) { if ($g['file'] === $chosen) { $valid = true; } }
        if ($valid) { $slides[$i]['image'] = $chosen; }
        if (!empty($_FILES['upload']['name'][$k])) {
            $_FILES['one'] = array('name' => $_FILES['upload']['name'][$k], 'type' => $_FILES['upload']['type'][$k], 'tmp_name' => $_FILES['upload']['tmp_name'][$k], 'error' => $_FILES['upload']['error'][$k], 'size' => $_FILES['upload']['size'][$k]);
            list($path, $uerr) = handle_upload('one');
            if ($uerr) { $err = $uerr; } elseif ($path) { $slides[$i]['image'] = $path; }
        }
    }
    save_json('slides', $slides);
    flash($err ? $err : 'Home slides updated successfully.', $err ? 'bad' : 'ok');
    header('Location: slides.php'); exit;
}
require __DIR__ . '/../includes/admin_header.php';
$slides = load_json('slides', array());
?>
<form method="post" enctype="multipart/form-data" class="adm-form">
<?= csrf_field() ?>
<?php foreach ($slides as $n => $s): $k = (int)$s['id']; ?>
<div class="box">
  <h2>Slide <?= $n + 1 ?></h2>
  <img class="thumb" src="../<?= e($s['image']) ?>" alt="">
  <div class="row2">
    <label>Choose picture from gallery<select name="image[<?= $k ?>]"><?php
        $found = false;
        foreach (gallery_items(false) as $g) { $sel = $g['file'] === $s['image']; if ($sel) { $found = true; } echo '<option value="' . e($g['file']) . '"' . ($sel ? ' selected' : '') . '>' . e($g['title']) . '</option>'; }
        if (!$found) { echo '<option value="' . e($s['image']) . '" selected>Current (uploaded) picture</option>'; }
    ?></select></label>
    <label>Or upload a new picture<input type="file" name="upload[<?= $k ?>]" accept="image/*"></label>
  </div>
  <label>Heading<input type="text" name="title[<?= $k ?>]" value="<?= e($s['title']) ?>" maxlength="120"></label>
  <label>Description<textarea name="text[<?= $k ?>]" rows="3" maxlength="400"><?= e($s['text']) ?></textarea></label>
  <div class="row2">
    <label>Button text<input type="text" name="btn[<?= $k ?>]" value="<?= e($s['btn']) ?>" maxlength="40"></label>
    <label>Button link (e.g. about.php)<input type="text" name="link[<?= $k ?>]" value="<?= e($s['link']) ?>"></label>
  </div>
</div>
<?php endforeach; ?>
<button class="btn btn-green" type="submit">Save Slides</button>
</form>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
