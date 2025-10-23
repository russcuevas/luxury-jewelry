<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$sale_id) {
    echo "Invalid sale ID.";
    exit;
}

// Fetch sale info
$saleStmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
$saleStmt->execute([$sale_id]);
$sale = $saleStmt->fetch(PDO::FETCH_ASSOC);
if (!$sale) {
    echo "Sale not found.";
    exit;
}

// Fetch sale items with product names
$itemStmt = $conn->prepare("
    SELECT si.*, p.product_name
    FROM sales_items si
    JOIN add_products p ON si.product_id = p.id
    WHERE si.sale_id = ?
");
$itemStmt->execute([$sale_id]);
$saleItems = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Receipt - Sale #<?= htmlspecialchars($sale['id']) ?></title>
<style>
  body {
    font-family: 'Courier New', Courier, monospace;
    width: 300px;
    margin: auto;
    padding: 10px;
  }
  h2, h3 {
    text-align: center;
    margin-bottom: 0;
  }
  .receipt-header {
    border-bottom: 1px dashed #000;
    margin-bottom: 10px;
    padding-bottom: 5px;
  }
  .info {
    font-size: 0.85em;
    margin-bottom: 10px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9em;
  }
  th, td {
    padding: 4px 0;
    text-align: left;
  }
  th {
    border-bottom: 1px dashed #000;
  }
  .right {
    text-align: right;
  }
  .totals {
    margin-top: 10px;
    border-top: 1px dashed #000;
    padding-top: 5px;
  }
  .totals div {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
  }
  .footer {
    margin-top: 20px;
    text-align: center;
    font-size: 0.8em;
    border-top: 1px dashed #000;
    padding-top: 5px;
  }
  @media print {
    body {
      width: auto;
      margin: 0;
      padding: 0;
    }
  }
</style>
</head>
<body onload="window.print()">
  <div class="receipt-header">
    <h2>Jewelry Store by Kimberly</h2>
    <h3>Sales Receipt</h3>
    <div class="info">
      <div>Sale ID: <?= htmlspecialchars($sale['id']) ?></div>
      <div>Date: <?= date("F j, Y, g:i A", strtotime($sale['sale_date'])) ?></div>
      <div>Cashier ID: <?= htmlspecialchars($sale['cashier_id']) ?></div>
      <div>Payment Method: <?= htmlspecialchars($sale['payment_method']) ?></div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th class="right">Price</th>
        <th class="right">Qty</th>
        <th class="right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($saleItems as $item): ?>
      <tr>
        <td><?= htmlspecialchars($item['product_name']) ?></td>
        <td class="right">₱<?= number_format($item['price'], 2) ?></td>
        <td class="right"><?= intval($item['quantity']) ?></td>
        <td class="right">₱<?= number_format($item['subtotal'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="totals">
    <div><span>Subtotal:</span> <span>₱<?= number_format($sale['sub_total'], 2) ?></span></div>
    <div><span>Tax (10%):</span> <span>₱<?= number_format($sale['tax'], 2) ?></span></div>
    <div><span>Grand Total:</span> <span>₱<?= number_format($sale['grand_total'], 2) ?></span></div>
  </div>

  <div class="footer">
    Thank you for your purchase!<br>
    Visit us again!
  </div>
</body>
</html>
