<?php require 'config.php'; require_login();
$list = rows("SELECT s.sale_id,s.date,s.quantity,CONCAT(c.first_name,' ',c.last_name) cust,d.name,d.mrp,d.discount,CONCAT(e.first_name,' ',e.last_name) emp,
  s.quantity*d.mrp*(1-d.discount/100) AS total FROM sale_transaction s JOIN customer c ON c.customer_id=s.customer_id JOIN drug d ON d.drug_id=s.drug_id JOIN employee e ON e.employee_id=s.employee_id ORDER BY s.sale_id DESC");
page_start('Sales Invoice Details', 'sales.php'); ?>
<table><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Medicine</th><th>Qty</th><th>MRP</th><th>Disc %</th><th>Total (Rs)</th><th>Sold by</th></tr>
<?php $sum = 0; foreach ($list as $r): $sum += $r['total']; ?>
<tr><td>#<?= $r['sale_id'] ?></td><td><?= $r['date'] ?></td><td><?= h($r['cust']) ?></td><td><?= h($r['name']) ?></td><td><?= $r['quantity'] ?></td><td><?= $r['mrp'] ?></td><td><?= $r['discount'] ?></td><td><?= number_format($r['total'], 2) ?></td><td><?= h($r['emp']) ?></td></tr>
<?php endforeach; ?><tr><td colspan="7" style="text-align:right"><b>Total revenue</b></td><td colspan="2"><b>Rs <?= number_format($sum, 2) ?></b></td></tr></table><?php page_end();
