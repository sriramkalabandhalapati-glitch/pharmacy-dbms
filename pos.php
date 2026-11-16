<?php require 'config.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  global $conn; $qty = (int)$_POST['qty']; $did = (int)$_POST['drug']; $cid = (int)$_POST['customer'];
  try {
    $conn->begin_transaction();
    $d = one('SELECT name, stock, expiry_date FROM drug WHERE drug_id=? FOR UPDATE', 'i', $did);   // row-level lock
    if (!$d || $qty < 1) throw new Exception('Invalid medicine or quantity.');
    if ($d['expiry_date'] < date('Y-m-d')) throw new Exception($d['name'] . ' is expired and cannot be sold.');
    if ($d['stock'] < $qty) throw new Exception('Only ' . $d['stock'] . ' units of ' . $d['name'] . ' in stock.');
    q('INSERT INTO sale_transaction (date,quantity,customer_id,drug_id,employee_id) VALUES (CURDATE(),?,?,?,?)', 'iiii', $qty, $cid, $did, $_SESSION['user']['employee_id']);  // trigger reduces stock
    $id = $conn->insert_id; $conn->commit();
    flash("Sale #$id completed. Stock updated automatically."); header('Location: sales.php'); exit;
  } catch (Exception $e) { $conn->rollback(); flash($e->getMessage(), false); header('Location: pos.php'); exit; }
}
page_start('Point of Sale', 'pos.php'); ?>
<div class="card"><form method="post" class="grid" id="f">
<div><label>Customer</label><select name="customer"><?php foreach (rows('SELECT customer_id,first_name,last_name FROM customer ORDER BY first_name') as $c) echo "<option value='{$c['customer_id']}'>" . h($c['first_name'] . ' ' . $c['last_name']) . "</option>"; ?></select></div>
<div><label>Medicine</label><select name="drug" id="drug"><?php foreach (rows('SELECT * FROM drug WHERE stock>0 ORDER BY name') as $d) echo "<option value='{$d['drug_id']}' data-p='" . round($d['mrp'] * (1 - $d['discount'] / 100), 2) . "' data-s='{$d['stock']}'>" . h($d['name']) . " (stock {$d['stock']})</option>"; ?></select></div>
<div><label>Quantity</label><input type="number" name="qty" id="qty" min="1" value="1" required></div>
<div><label>Unit price (after discount)</label><input id="price" readonly></div><div><label>Bill amount</label><input id="total" readonly></div>
<div><button class="btn blue">Complete Sale</button></div></form></div>
<script>const dr=document.getElementById('drug'),q=document.getElementById('qty'),p=document.getElementById('price'),t=document.getElementById('total');
function calc(){const o=dr.options[dr.selectedIndex];if(!o)return;const u=parseFloat(o.dataset.p);p.value=u.toFixed(2);q.max=o.dataset.s;t.value=(u*(q.value||0)).toFixed(2)}
dr.onchange=q.oninput=calc;calc();</script><?php page_end();
