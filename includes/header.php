<?php
require_once __DIR__ . '/bootstrap.php';
$S = site_settings();
$base = isset($base) ? $base : '';
$pages = array(
    'index'    => array('Home', 'index.php'),
    'about'    => array('About Us', 'about.php'),
    'services' => array('Services', 'services.php'),
    'gallery'  => array('Gallery', 'gallery.php'),
    'contact'  => array('Contact Us', 'contact.php'),
    'news'     => array('News &amp; Updates', 'news.php'),
);
$current = isset($current) ? $current : '';
$page_title = isset($page_title) ? $page_title : 'Home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?> | <?= e($S['school_name']) ?></title>
<meta name="description" content="<?= e($S['school_name']) ?>: a Catholic-founded secondary school in Fort Portal, Uganda, committed to holistic education, academic excellence and moral integrity.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="topbar">
  <div class="wrap">
    <span><?= e($S['phone']) ?></span>
    <span><?= e($S['email']) ?></span>
    <span class="topbar-hours"><?= e($S['hours_weekday']) ?></span>
  </div>
</div>
<header class="site-header">
  <div class="wrap nav-row">
    <a class="brand" href="index.php">
      <img src="assets/img/img45.jpg" alt="College crest">
      <span><strong><?= e($S['school_name']) ?></strong><small><?= e($S['motto']) ?></small></span>
    </a>
    <button class="nav-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="mainnav"><span></span><span></span><span></span></button>
    <nav id="mainnav" class="mainnav">
      <?php foreach ($pages as $key => $p): ?>
        <a href="<?= $p[1] ?>"<?= $current === $key ? ' class="active" aria-current="page"' : '' ?>><?= $p[0] ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
<main>
