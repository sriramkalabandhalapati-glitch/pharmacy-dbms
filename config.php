<?php
// ---- Database settings (XAMPP defaults) ----
$DB_HOST = 'localhost'; $DB_USER = 'root'; $DB_PASS = ''; $DB_NAME = 'pharmacia';

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try { $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME); $conn->set_charset('utf8mb4'); }
catch (Exception $e) { die('<h3>Database connection failed.</h3><p>Start MySQL in XAMPP and import database.sql first.</p>'); }

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function q($sql, $types = '', ...$p) { global $conn; $st = $conn->prepare($sql); if ($types) $st->bind_param($types, ...$p); $st->execute(); return $st; }
function rows($sql, $types = '', ...$p) { return q($sql, $types, ...$p)->get_result()->fetch_all(MYSQLI_ASSOC); }
function one($sql, $types = '', ...$p) { $r = rows($sql, $types, ...$p); return $r ? $r[0] : null; }
function flash($msg, $ok = true) { $_SESSION['flash'] = [$msg, $ok]; }
function require_login($adminOnly = false) {
  if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
  if ($adminOnly && $_SESSION['user']['role'] !== 'admin') { flash('Admin access only.', false); header('Location: dashboard.php'); exit; }
}
function is_admin() { return ($_SESSION['user']['role'] ?? '') === 'admin'; }

function page_start($title, $active) {
  $u = $_SESSION['user'];
  $nav = [['dashboard.php','Dashboard',0],['inventory.php','Inventory',0],['suppliers.php','Suppliers',1],['purchase.php','Stock Purchase',0],
          ['employees.php','Employees',1],['customers.php','Customers',0],['sales.php','Sales Invoice Details',0],['pos.php','Add New Sale',0],['reports.php','Reports',0]];
  echo "<!DOCTYPE html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>" . h($title) . " - Pharmacia</title><link rel='stylesheet' href='style.css'></head><body>";
  echo "<div class='sidenav'><h2>PHARMACIA</h2><div class='who'>" . h($u['username']) . " (" . h($u['role']) . ")</div>";
  foreach ($nav as $n) { if ($n[2] && !is_admin()) continue; echo "<a href='{$n[0]}'" . ($active === $n[0] ? " class='active'" : '') . ">{$n[1]}</a>"; }
  echo "</div><div class='main'><div class='topnav'><a href='logout.php'>Logout</a></div><div class='head'><h2>" . h(strtoupper($title)) . "</h2></div>";
  if (!empty($_SESSION['flash'])) { [$m, $ok] = $_SESSION['flash']; unset($_SESSION['flash']); echo "<div class='flash " . ($ok ? 'ok' : 'err') . "'>" . h($m) . "</div>"; }
}
function page_end() { echo "</div></body></html>"; }
