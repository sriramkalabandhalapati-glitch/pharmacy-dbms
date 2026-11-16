<?php require 'config.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $a = $_POST['action'] ?? '';
  try {
    if ($a === 'delete') { q('DELETE FROM drug WHERE drug_id=?', 'i', (int)$_POST['id']); flash('Medicine deleted.'); }
    else {
      $f = [trim($_POST['name']), $_POST['type'], (int)$_POST['stock'], (float)$_POST['cost'], (float)$_POST['mrp'], (float)$_POST['discount'], $_POST['expiry'], trim($_POST['location'])];
      if ($a === 'add') { q('INSERT INTO drug (name,type,stock,cost_price,mrp,discount,expiry_date,location) VALUES (?,?,?,?,?,?,?,?)', 'ssidddss', ...$f); flash('Medicine added.'); }
      if ($a === 'update') { q('UPDATE drug SET name=?,type=?,stock=?,cost_price=?,mrp=?,discount=?,expiry_date=?,location=? WHERE drug_id=?', 'ssidddssi', ...array_merge($f, [(int)$_POST['id']])); flash('Medicine updated.'); }
    }
  } catch (mysqli_sql_exception $e) { flash('Not saved: ' . (str_contains($e->getMessage(), 'foreign key') ? 'medicine is used in sales/purchases.' : 'check the values (stock cannot be negative).'), false); }
  header('Location: inventory.php'); exit;
}
$edit = isset($_GET['edit']) ? one('SELECT * FROM drug WHERE drug_id=?', 'i', (int)$_GET['edit']) : null;
$search = trim($_GET['s'] ?? '');
$list = rows('SELECT * FROM drug WHERE name LIKE ? ORDER BY drug_id', 's', "%$search%");
page_start('Medicine Inventory', 'inventory.php'); $e = $edit ?: []; ?>
<div class="card"><form method="post" class="grid">
<input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>"><input type="hidden" name="id" value="<?= h($e['drug_id'] ?? '') ?>">
<div><label>Medicine name</label><input name="name" required value="<?= h($e['name'] ?? '') ?>"></div>
<div><label>Type</label><select name="type"><?php foreach (['Tablet','Capsule','Syrup','Injection','Ointment'] as $t) echo "<option" . (($e['type'] ?? '') === $t ? ' selected' : '') . ">$t</option>"; ?></select></div>
<div><label>Stock</label><input type="number" name="stock" min="0" required value="<?= h($e['stock'] ?? '') ?>"></div>
<div><label>Cost price</label><input type="number" step="0.01" name="cost" required value="<?= h($e['cost_price'] ?? '') ?>"></div>
<div><label>MRP</label><input type="number" step="0.01" name="mrp" required value="<?= h($e['mrp'] ?? '') ?>"></div>
<div><label>Discount %</label><input type="number" step="0.01" name="discount" value="<?= h($e['discount'] ?? 0) ?>"></div>
<div><label>Expiry date</label><input type="date" name="expiry" required value="<?= h($e['expiry_date'] ?? '') ?>"></div>
<div><label>Location</label><input name="location" placeholder="rack 5" value="<?= h($e['location'] ?? '') ?>"></div>
<div><button class="btn green"><?= $edit ? 'Update' : 'Add Medicine' ?></button> <?php if ($edit) echo '<a class="btn grey" href="inventory.php">Cancel</a>'; ?></div>
</form></div>
<form method="get" style="margin-bottom:12px"><input name="s" placeholder="Search medicine..." value="<?= h($search) ?>" style="width:260px"> <button class="btn blue">Search</button></form>
<table><tr><th>ID</th><th>Name</th><th>Type</th><th>Stock</th><th>MRP</th><th>Disc %</th><th>Expiry</th><th>Location</th><th>Action</th></tr>
<?php foreach ($list as $r):
  $days = (strtotime($r['expiry_date']) - time()) / 86400; $cls = $days <= 90 ? 'exp' : ($r['stock'] < 30 ? 'low' : ''); ?>
<tr class="<?= $cls ?>"><td><?= $r['drug_id'] ?></td><td><?= h($r['name']) ?></td><td><?= h($r['type']) ?></td><td><?= $r['stock'] ?><?= $r['stock'] < 30 ? " <span class='pill y'>low</span>" : '' ?></td>
<td><?= $r['mrp'] ?></td><td><?= $r['discount'] ?></td><td><?= $r['expiry_date'] ?><?= $days <= 90 ? " <span class='pill r'>expiring</span>" : '' ?></td><td><?= h($r['location']) ?></td>
<td><a class="btn green" href="?edit=<?= $r['drug_id'] ?>">Edit</a>
<?php if (is_admin()): ?><form method="post" style="display:inline" onsubmit="return confirm('Delete this medicine?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['drug_id'] ?>"><button class="btn red">Delete</button></form><?php endif; ?></td></tr>
<?php endforeach; ?></table>
<?php page_end();
