<?php require 'config.php'; require_login(true);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (($_POST['action'] ?? '') === 'delete') { q('DELETE FROM distributor WHERE distributor_id=?', 'i', (int)$_POST['id']); flash('Supplier deleted.'); }
    elseif (($_POST['action'] ?? '') === 'update') { q('UPDATE distributor SET company_name=?,contact_no=?,license=?,address=?,email=? WHERE distributor_id=?', 'sssssi', $_POST['company'], $_POST['phone'], $_POST['license'], $_POST['address'], $_POST['email'], (int)$_POST['id']); flash('Supplier updated.'); }
    else { q('INSERT INTO distributor (company_name,contact_no,license,address,email) VALUES (?,?,?,?,?)', 'sssss', $_POST['company'], $_POST['phone'], $_POST['license'], $_POST['address'], $_POST['email']); flash('Supplier added.'); }
  } catch (mysqli_sql_exception $e) { flash('Not saved (duplicate phone number or supplier has purchases).', false); }
  header('Location: suppliers.php'); exit;
}
$edit = isset($_GET['edit']) ? one('SELECT * FROM distributor WHERE distributor_id=?', 'i', (int)$_GET['edit']) : null; $e = $edit ?: [];
page_start('Suppliers List', 'suppliers.php'); ?>
<div class="card"><form method="post" class="grid"><input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>"><input type="hidden" name="id" value="<?= h($e['distributor_id'] ?? '') ?>">
<div><label>Company name</label><input name="company" required value="<?= h($e['company_name'] ?? '') ?>"></div><div><label>Phone</label><input name="phone" required value="<?= h($e['contact_no'] ?? '') ?>"></div>
<div><label>License no.</label><input name="license" required value="<?= h($e['license'] ?? '') ?>"></div><div><label>Address</label><input name="address" value="<?= h($e['address'] ?? '') ?>"></div>
<div><label>Email</label><input type="email" name="email" value="<?= h($e['email'] ?? '') ?>"></div><div><button class="btn green"><?= $edit ? 'Update' : 'Add Supplier' ?></button></div></form></div>
<table><tr><th>ID</th><th>Company</th><th>Address</th><th>Phone</th><th>Email</th><th>License</th><th>Action</th></tr>
<?php foreach (rows('SELECT * FROM distributor ORDER BY distributor_id') as $r): ?>
<tr><td><?= $r['distributor_id'] ?></td><td><?= h($r['company_name']) ?></td><td><?= h($r['address']) ?></td><td><?= h($r['contact_no']) ?></td><td><?= h($r['email']) ?></td><td><?= h($r['license']) ?></td>
<td><a class="btn green" href="?edit=<?= $r['distributor_id'] ?>">Edit</a> <form method="post" style="display:inline" onsubmit="return confirm('Delete supplier?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['distributor_id'] ?>"><button class="btn red">Delete</button></form></td></tr>
<?php endforeach; ?></table><?php page_end();
