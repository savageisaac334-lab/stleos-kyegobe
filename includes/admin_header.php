<?php
require_once __DIR__ . '/bootstrap.php';
ensure_users();
require_login();
$base = '../';
$S = site_settings();
$nav = array(
    'index'    => array('Dashboard', 'index.php'),
    'slides'   => array('Home Slides', 'slides.php'),
    'news'     => array('News & Updates', 'news.php'),
    'gallery'  => array('Gallery', 'gallery.php'),
    'messages' => array('Messages', 'messages.php'),
    'settings' => array('Settings & Password', 'settings.php'),
);
$unread = 0;
foreach (load_json('messages', array()) as $m) { if (empty($m['read'])) { $unread++; } }
$flash = flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= e($adm_title) ?> | Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="adm">
<aside class="adm-side">
  <div class="adm-brand"><img src="../assets/img/img45.jpg" alt=""><span>St. Leo's College<br>Kyegobe</span></div>
  <nav>
    <?php foreach ($nav as $k => $n): ?>
      <a href="<?= $n[1] ?>"<?= $adm_current === $k ? ' class="on"' : '' ?>><?= e($n[0]) ?><?php if ($k === 'messages' && $unread): ?> <b class="badge"><?= $unread ?></b><?php endif; ?></a>
    <?php endforeach; ?>
    <a href="../index.php" target="_blank" rel="noopener">View Website</a>
    <a href="logout.php">Log Out</a>
  </nav>
</aside>
<div class="adm-main">
  <div class="adm-top">
    <h1><?= e($adm_title) ?></h1>
    <span>Signed in as <strong><?= e($_SESSION['admin_user']) ?></strong></span>
  </div>
  <div class="adm-body">
  <?php if ($flash): ?><div class="alert <?= $flash['t'] === 'ok' ? 'ok' : 'bad' ?>"><?= e($flash['m']) ?></div><?php endif; ?>
