<?php require 'config.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  global $conn;
  $conn->query("SET @msg = ''");
  q('CALL SafeInsertPurchase(?,?,?,?,?,@msg)', 'siiid', $_POST['date'], (int)$_POST['qty'], (int)$_POST['drug'], (int)$_POST['dist'], (float)$_POST['cost']);
  $m = $conn->query('SELECT @msg m')->fetch_assoc()['m'];
  $m === 'OK' ? flash('Purchase recorded and stock updated.') : flash($m, false);
  header('Location: purchase.php'); exit;
}
page_start('Stock Purchase List', 'purchase.php'); ?>
<div class="card"><form method="post" class="grid">
<div><label>Medicine</label><select name="drug"><?php foreach (rows('SELECT drug_id,name FROM drug ORDER BY name') as $d) echo "<option value='{$d['drug_id']}'>" . h($d['name']) . "</option>"; ?></select></div>
<div><label>Supplier</label><select name="dist"><?php foreach (rows('SELECT distributor_id,company_name FROM distributor ORDER BY company_name') as $d) echo "<option value='{$d['distributor_id']}'>" . h($d['company_name']) . "</option>"; ?></select></div>
<div><label>Quantity</label><input type="number" name="qty" min="1" required></div><div><label>Cost price (per unit)</label><input type="number" step="0.01" name="cost" required></div>
<div><label>Date</label><input type="date" name="date" value="<?= date('Y-m-d') ?>" required></div><div><button class="btn green">Add Purchase</button></div></form></div>
<table><tr><th>Purchase ID</th><th>Supplier</th><th>Medicine</th><th>Quantity</th><th>Cost price</th><th>Date</th></tr>
<?php foreach (rows('SELECT p.*, d.name, s.company_name FROM purchase p JOIN drug d ON d.drug_id=p.drug_id JOIN distributor s ON s.distributor_id=p.distributor_id ORDER BY p.purchase_id DESC') as $r): ?>
<tr><td><?= $r['purchase_id'] ?></td><td><?= h($r['company_name']) ?></td><td><?= h($r['name']) ?></td><td><?= $r['quantity'] ?></td><td><?= $r['cost_price'] ?></td><td><?= $r['date'] ?></td></tr>
<?php endforeach; ?></table><?php page_end();
