<?php require 'config.php'; require_login(); global $conn;
$type = $_GET['type'] ?? 'summary'; page_start('Reports', 'reports.php'); ?>
<p class="tabs"><a class="btn blue" href="?type=summary">Summary</a><a class="btn blue" href="?type=stock">Low stock</a><a class="btn blue" href="?type=expiry">Soon to expire</a><a class="btn blue" href="?type=sales">Sales by medicine</a></p>
<?php if ($type === 'stock'): ?>
<table><tr><th>ID</th><th>Medicine</th><th>Stock</th><th>Location</th></tr><?php foreach (rows('SELECT * FROM drug WHERE stock < 30 ORDER BY stock') as $r) echo "<tr class='low'><td>{$r['drug_id']}</td><td>" . h($r['name']) . "</td><td>{$r['stock']}</td><td>" . h($r['location']) . "</td></tr>"; ?></table>
<?php elseif ($type === 'expiry'):
  $res = $conn->query('CALL GenerateExpiryReport()'); $alerts = $res->fetch_all(MYSQLI_ASSOC); $res->free(); while ($conn->more_results()) $conn->next_result(); ?>
<div class="card"><b>Cursor-based expiry alerts (stored procedure GenerateExpiryReport)</b><br><br>
<?php foreach ($alerts as $a) echo "<div class='flash err'>" . h($a['msg']) . "</div>"; if (!$alerts) echo 'No medicines expiring within 3 months.'; ?></div>
<?php elseif ($type === 'sales'): ?>
<table><tr><th>Medicine</th><th>Units sold</th><th>Revenue (Rs)</th></tr><?php foreach (rows('SELECT d.name, SUM(s.quantity) u, SUM(s.quantity*d.mrp*(1-d.discount/100)) r FROM sale_transaction s JOIN drug d ON d.drug_id=s.drug_id GROUP BY d.drug_id ORDER BY r DESC') as $r) echo "<tr><td>" . h($r['name']) . "</td><td>{$r['u']}</td><td>" . number_format($r['r'], 2) . "</td></tr>"; ?></table>
<?php else: $s = one('SELECT COUNT(*) c, SUM(stock) st, AVG(mrp) av, SUM(stock*cost_price) val FROM drug'); $rev = one('SELECT COALESCE(SUM(s.quantity*d.mrp*(1-d.discount/100)),0) t FROM sale_transaction s JOIN drug d ON d.drug_id=s.drug_id')['t']; ?>
<div class="tiles"><div class="tile"><span class="n"><?= $s['c'] ?></span>COUNT: medicines</div><div class="tile"><span class="n"><?= $s['st'] ?></span>SUM: units in stock</div>
<div class="tile"><span class="n"><?= number_format($s['av'], 2) ?></span>AVG: price (MRP)</div><div class="tile"><span class="n"><?= number_format($s['val'], 0) ?></span>Stock value at cost (Rs)</div><div class="tile"><span class="n"><?= number_format($rev, 0) ?></span>Total revenue (Rs)</div></div>
<?php endif; page_end();
