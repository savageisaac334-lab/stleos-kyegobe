<?php
require_once __DIR__ . '/../includes/bootstrap.php';
ensure_users();
if (is_logged_in()) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['tries'] = isset($_SESSION['tries']) ? $_SESSION['tries'] : 0;
    if (!csrf_ok()) {
        $error = 'Session expired. Please try again.';
    } elseif ($_SESSION['tries'] >= 5 && time() - (isset($_SESSION['tries_at']) ? $_SESSION['tries_at'] : 0) < 300) {
        $error = 'Too many failed attempts. Please wait 5 minutes.';
    } else {
        $u = trim((string)(isset($_POST['username']) ? $_POST['username'] : ''));
        $p = (string)(isset($_POST['password']) ? $_POST['password'] : '');
        $ok = false;
        foreach (load_json('users', array()) as $user) {
            if (strcasecmp($user['username'], $u) === 0 && password_verify($p, $user['hash'])) { $ok = $user; break; }
        }
        if ($ok) {
            session_regenerate_id(true);
            $_SESSION['admin_user'] = $ok['username'];
            $_SESSION['tries'] = 0;
            header('Location: index.php');
            exit;
        }
        $_SESSION['tries']++;
        $_SESSION['tries_at'] = time();
        $error = 'Incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Management Login | St. Leo's College Kyegobe</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700&family=Figtree:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <img src="../assets/img/img45.jpg" alt="College crest">
    <h1>Management System</h1>
    <p class="sub">St. Leo's College Kyegobe: authorised staff only</p>
    <?php if ($error): ?><div class="alert bad" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="adm-form" autocomplete="off">
      <?= csrf_field() ?>
      <label>Username<input type="text" name="username" required autofocus></label>
      <label>Password<input type="password" name="password" required></label>
      <button class="btn btn-green" type="submit" style="width:100%">Log In</button>
    </form>
    <p class="center" style="margin:1rem 0 0"><a href="../index.php">&larr; Back to website</a></p>
  </div>
</div>
</body>
</html>
