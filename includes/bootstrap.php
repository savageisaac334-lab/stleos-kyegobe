<?php
/**
 * St Leo College Kyegobe - core bootstrap (plain PHP, no database needed).
 * Data is stored in /data/*.json files.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Africa/Kampala');

define('ROOT_DIR', dirname(__DIR__));
define('DATA_DIR', ROOT_DIR . '/data');
define('UPLOAD_DIR', ROOT_DIR . '/assets/uploads');
define('DEFAULT_ADMIN_USER', 'admin');
define('DEFAULT_ADMIN_PASS', 'StLeo@2026');

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function load_json($name, $default = array()) {
    $f = DATA_DIR . '/' . $name . '.json';
    if (!is_file($f)) { return $default; }
    $d = json_decode(file_get_contents($f), true);
    return is_array($d) ? $d : $default;
}

function save_json($name, $data) {
    if (!is_dir(DATA_DIR)) { @mkdir(DATA_DIR, 0755, true); }
    $f = DATA_DIR . '/' . $name . '.json';
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($f, $json, LOCK_EX) !== false;
}

function next_id($items) {
    $max = 0;
    foreach ($items as $it) { if (isset($it['id']) && $it['id'] > $max) { $max = (int)$it['id']; } }
    return $max + 1;
}

function site_settings() {
    $defaults = array(
        'school_name' => "ST. LEO'S COLLEGE KYEGOBE", 'motto' => 'Strive for Excellence',
        'address' => '', 'phone' => '', 'email' => '', 'hours_weekday' => '', 'hours_sat' => '',
        'mission' => '', 'vision' => '', 'history' => '', 'map_query' => 'Fort Portal, Uganda'
    );
    return array_merge($defaults, load_json('settings', array()));
}

/* ---------- Users / authentication ---------- */
function ensure_users() {
    if (!is_file(DATA_DIR . '/users.json')) {
        save_json('users', array(array(
            'id' => 1,
            'username' => DEFAULT_ADMIN_USER,
            'name' => 'System Administrator',
            'hash' => password_hash(DEFAULT_ADMIN_PASS, PASSWORD_DEFAULT)
        )));
    }
}

function is_logged_in() { return !empty($_SESSION['admin_user']); }

function require_login() {
    if (!is_logged_in()) { header('Location: login.php'); exit; }
}

/* ---------- CSRF + flash ---------- */
function csrf_token() {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_ok() {
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
}
function flash($msg = null, $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = array('m' => $msg, 't' => $type); return; }
    if (!empty($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}

/* ---------- Images / uploads ---------- */
function categories() { return array('Campus', 'Academics', 'Faith', 'Sports', 'Community', 'Emblems'); }

function handle_upload($field) {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) { return array(null, null); }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) { return array(null, 'Upload failed. Please try a smaller image.'); }
    if ($f['size'] > 6 * 1024 * 1024) { return array(null, 'Image is too large (max 6 MB).'); }
    $info = @getimagesize($f['tmp_name']);
    if (!$info) { return array(null, 'The selected file is not a valid image.'); }
    $ext = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif');
    if (defined('IMAGETYPE_WEBP')) { $ext[IMAGETYPE_WEBP] = 'webp'; }
    if (!isset($ext[$info[2]])) { return array(null, 'Only JPG, PNG, GIF or WEBP images are allowed.'); }
    if (!is_dir(UPLOAD_DIR)) { @mkdir(UPLOAD_DIR, 0755, true); }
    $name = 'up_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext[$info[2]];
    if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . '/' . $name)) {
        return array(null, 'Could not save the image. Check folder permissions on assets/uploads.');
    }
    return array('assets/uploads/' . $name, null);
}

function gallery_items($onlyVisible = true) {
    $g = load_json('gallery', array());
    if (!$onlyVisible) { return $g; }
    $out = array();
    foreach ($g as $it) { if (!isset($it['visible']) || $it['visible']) { $out[] = $it; } }
    return $out;
}

function image_options($selected) {
    $html = '';
    foreach (gallery_items(false) as $it) {
        $sel = ($it['file'] === $selected) ? ' selected' : '';
        $html .= '<option value="' . e($it['file']) . '"' . $sel . '>' . e($it['title']) . '</option>';
    }
    return $html;
}

function fmt_date($d) {
    $t = strtotime($d);
    return $t ? date('j F Y', $t) : $d;
}
