<?php require 'config.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (($_POST['action'] ?? '') === 'delete' && is_admin()) { q('DELETE FROM customer WHERE customer_id=?', 'i', (int)$_POST['id']); flash('Customer deleted.'); }
    elseif (($_POST['action'] ?? '') === 'add') { q('INSERT INTO customer (first_name,last_name,contact_no,address,street_no) VALUES (?,?,?,?,?)', 'sssss', $_POST['fn'], $_POST['ln'], $_POST['phone'], $_POST['address'], $_POST['street']); flash('Customer added.'); }
  } catch (mysqli_sql_exception $e) { flash('Not saved (duplicate phone, or customer has sales).', false); }
  header('Location: customers.php'); exit;
}
page_start('Customer List', 'customers.php'); ?>
<div class="card"><form method="post" class="grid"><input type="hidden" name="action" value="add">
<div><label>First name</label><input name="fn" required></div><div><label>Last name</label><input name="ln" required></div><div><label>Phone</label><input name="phone" required></div>
<div><label>Address</label><input name="address"></div><div><label>Street no.</label><input name="street"></div><div><button class="btn green">Add Customer</button></div></form></div>
<table><tr><th>ID</th><th>Name</th><th>Phone</th><th>Address</th><th>Total spent (Rs)</th><?php if (is_admin()) echo '<th>Action</th>'; ?></tr>
<?php foreach (rows('SELECT c.*, GetCustomerTotalSpent(c.customer_id) AS spent FROM customer c ORDER BY customer_id') as $r): ?>
<tr><td><?= $r['customer_id'] ?></td><td><?= h($r['first_name'] . ' ' . $r['last_name']) ?></td><td><?= h($r['contact_no']) ?></td><td><?= h($r['address'] . ' ' . $r['street_no']) ?></td><td><?= number_format($r['spent'], 2) ?></td>
<?php if (is_admin()): ?><td><form method="post" style="display:inline" onsubmit="return confirm('Delete customer?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['customer_id'] ?>"><button class="btn red">Delete</button></form></td><?php endif; ?></tr>
<?php endforeach; ?></table><?php page_end();
