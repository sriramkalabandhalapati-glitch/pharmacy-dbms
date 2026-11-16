<?php require 'config.php';
if (!empty($_SESSION['user'])) { header('Location: dashboard.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = one('SELECT * FROM users WHERE username=?', 's', trim($_POST['username'] ?? ''));
  if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user'] = ['id' => $u['user_id'], 'username' => $u['username'], 'role' => $u['role'], 'employee_id' => $u['employee_id']];
    header('Location: dashboard.php'); exit;
  }
  $err = 'Invalid username or password';
}
?><!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pharmacia Login</title><link rel="stylesheet" href="style.css"></head>
<body class="login"><div class="header"><h1>PHARMACIA</h1><div>Pharmacy Management System</div></div>
<div class="box"><h1>Login</h1><form method="post">
<?php if ($err) echo "<div class='flash err'>" . h($err) . "</div>"; ?>
<input name="username" placeholder="Username" required autofocus><input type="password" name="password" placeholder="Password" required>
<button class="btn blue">Login</button></form>
<div style="padding:0 18px 16px;font-size:12px;color:#6c7a89">Demo: admin / admin123 &nbsp;|&nbsp; pharmacist / pharma123</div></div></body></html>
