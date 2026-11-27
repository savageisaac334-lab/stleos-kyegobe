<?php
$adm_title = 'Settings & Password'; $adm_current = 'settings';
require_once __DIR__ . '/../includes/bootstrap.php';
ensure_users(); require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { flash('Session expired. Please try again.', 'bad'); header('Location: settings.php'); exit; }
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'site') {
        $S = site_settings();
        foreach (array('school_name', 'motto', 'address', 'phone', 'email', 'hours_weekday', 'hours_sat', 'mission', 'vision', 'history', 'map_query') as $k) {
            if (isset($_POST[$k])) { $S[$k] = trim((string)$_POST[$k]); }
        }
        if ($S['map_query'] === '') { $S['map_query'] = 'Fort Portal, Uganda'; }
        save_json('settings', $S);
        flash('Website settings saved.');
    } elseif ($action === 'password') {
        $users = load_json('users', array());
        $cur = (string)$_POST['current']; $new = (string)$_POST['new']; $conf = (string)$_POST['confirm'];
        $done = false;
        foreach ($users as $i => $u) {
            if ($u['username'] === $_SESSION['admin_user']) {
                if (!password_verify($cur, $u['hash'])) { flash('Your current password is incorrect.', 'bad'); }
                elseif (strlen($new) < 8) { flash('The new password must be at least 8 characters.', 'bad'); }
                elseif ($new !== $conf) { flash('The new password and confirmation do not match.', 'bad'); }
                else { $users[$i]['hash'] = password_hash($new, PASSWORD_DEFAULT); save_json('users', $users); flash('Password changed successfully.'); $done = true; }
            }
        }
    }
    header('Location: settings.php'); exit;
}
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="box">
  <h2>Website Information</h2>
  <form method="post" class="adm-form">
    <?= csrf_field() ?><input type="hidden" name="action" value="site">
    <div class="row2">
      <label>School name<input type="text" name="school_name" value="<?= e($S['school_name']) ?>"></label>
      <label>Motto<input type="text" name="motto" value="<?= e($S['motto']) ?>"></label>
    </div>
    <label>Address<input type="text" name="address" value="<?= e($S['address']) ?>"></label>
    <div class="row2">
      <label>Phone<input type="text" name="phone" value="<?= e($S['phone']) ?>"></label>
      <label>Email<input type="text" name="email" value="<?= e($S['email']) ?>"></label>
    </div>
    <div class="row2">
      <label>Office hours (Mon - Fri)<input type="text" name="hours_weekday" value="<?= e($S['hours_weekday']) ?>"></label>
      <label>Office hours (Saturday)<input type="text" name="hours_sat" value="<?= e($S['hours_sat']) ?>"></label>
    </div>
    <label>Mission<textarea name="mission" rows="3"><?= e($S['mission']) ?></textarea></label>
    <label>Vision<textarea name="vision" rows="3"><?= e($S['vision']) ?></textarea></label>
    <label>History<textarea name="history" rows="3"><?= e($S['history']) ?></textarea></label>
    <label>Map search text (what Google Maps should show)<input type="text" name="map_query" value="<?= e($S['map_query']) ?>"></label>
    <button class="btn btn-green" type="submit">Save Settings</button>
  </form>
</div>
<div class="box">
  <h2>Change Password</h2>
  <form method="post" class="adm-form" style="max-width:460px" autocomplete="off">
    <?= csrf_field() ?><input type="hidden" name="action" value="password">
    <label>Current password<input type="password" name="current" required></label>
    <label>New password (minimum 8 characters)<input type="password" name="new" minlength="8" required></label>
    <label>Confirm new password<input type="password" name="confirm" minlength="8" required></label>
    <button class="btn btn-green" type="submit">Update Password</button>
  </form>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
