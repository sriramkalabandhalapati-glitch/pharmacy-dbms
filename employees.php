<?php require 'config.php'; require_login(true);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (($_POST['action'] ?? '') === 'delete') { q('DELETE FROM employee WHERE employee_id=?', 'i', (int)$_POST['id']); flash('Employee deleted.'); }
    else { q('INSERT INTO employee (first_name,last_name,contact_no,qualification,designation,salary) VALUES (?,?,?,?,?,?)', 'sssssd', $_POST['fn'], $_POST['ln'], $_POST['phone'], $_POST['qual'], $_POST['desig'], (float)$_POST['salary']); flash('Employee added.'); }
  } catch (mysqli_sql_exception $e) { flash('Not saved (duplicate phone, or employee has sales/login).', false); }
  header('Location: employees.php'); exit;
}
page_start('Employee List', 'employees.php'); ?>
<div class="card"><form method="post" class="grid"><input type="hidden" name="action" value="add">
<div><label>First name</label><input name="fn" required></div><div><label>Last name</label><input name="ln" required></div><div><label>Phone</label><input name="phone" required></div>
<div><label>Qualification</label><input name="qual"></div><div><label>Designation</label><input name="desig" value="Pharmacist"></div><div><label>Salary</label><input type="number" name="salary" step="0.01"></div>
<div><button class="btn green">Add Employee</button></div></form></div>
<table><tr><th>ID</th><th>Name</th><th>Phone</th><th>Qualification</th><th>Designation</th><th>Salary</th><th>Action</th></tr>
<?php foreach (rows('SELECT * FROM employee ORDER BY employee_id') as $r): ?>
<tr><td><?= $r['employee_id'] ?></td><td><?= h($r['first_name'] . ' ' . $r['last_name']) ?></td><td><?= h($r['contact_no']) ?></td><td><?= h($r['qualification']) ?></td><td><?= h($r['designation']) ?></td><td><?= $r['salary'] ?></td>
<td><form method="post" style="display:inline" onsubmit="return confirm('Delete employee?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['employee_id'] ?>"><button class="btn red">Delete</button></form></td></tr>
<?php endforeach; ?></table><?php page_end();
