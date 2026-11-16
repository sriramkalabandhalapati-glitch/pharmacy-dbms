<?php require 'config.php'; require_login();
$s = one("SELECT COUNT(*) c, COALESCE(SUM(stock),0) st, COALESCE(AVG(mrp),0) av FROM drug");
$low = one("SELECT COUNT(*) c FROM drug WHERE stock < 30")['c'];
$exp = one("SELECT COUNT(*) c FROM drug WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH)")['c'];
$today = one("SELECT COALESCE(SUM(s.quantity*d.mrp*(1-d.discount/100)),0) t, COUNT(*) n FROM sale_transaction s JOIN drug d ON d.drug_id=s.drug_id WHERE s.date=CURDATE()");
page_start(is_admin() ? 'Admin Dashboard' : 'Pharmacist Dashboard', 'dashboard.php'); ?>
<div class="tiles">
<a class="tile" href="inventory.php"><span class="ic">&#128138;</span><span class="n"><?= $s['c'] ?></span>Medicines</a>
<a class="tile" href="pos.php"><span class="ic">&#128722;</span><span class="n">New</span>Sale / Billing</a>
<a class="tile" href="sales.php"><span class="ic">&#128176;</span><span class="n">Rs <?= number_format($today['t'], 2) ?></span>Today's sales (<?= $today['n'] ?>)</a>
<a class="tile" href="reports.php?type=stock"><span class="ic">&#128230;</span><span class="n"><?= $low ?></span>Low-stock items</a>
<a class="tile" href="reports.php?type=expiry"><span class="ic">&#9888;&#65039;</span><span class="n"><?= $exp ?></span>Expiring in 3 months</a>
<a class="tile" href="reports.php?type=summary"><span class="ic">&#128202;</span><span class="n"><?= $s['st'] ?></span>Units in stock</a>
</div>
<div class="card"><b>Quick tips</b><br>Selling a medicine automatically reduces stock (database trigger). Purchases add stock through a stored procedure with rollback on error.</div>
<?php page_end();
