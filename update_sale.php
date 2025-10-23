<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Get sale ID from query string
$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$sale_id) {
    echo "Invalid sale ID.";
    exit;
}

// Handle form submission (POST) to update sale
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    if (!in_array($payment_method, ['GCASH', 'COD'])) {
        echo "Invalid payment method.";
        exit;
    }

    $quantities = $_POST['quantity'] ?? [];

    try {
        $conn->beginTransaction();

        // Update quantities and recalculate subtotals for sale items
        $total = 0;
        foreach ($quantities as $item_id => $qty) {
            $qty = max(1, intval($qty)); // minimum 1

            // Fetch price for the sale item
            // NOTE: Assuming sales_items table has 'id' as PK, 'sale_id' as FK
            $stmt = $conn->prepare("SELECT price FROM sales_items WHERE id = ? AND sale_id = ?");
            $stmt->execute([$item_id, $sale_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$item) continue;

            $subtotal = $item['price'] * $qty;
            $total += $subtotal;

            // Update sale item quantity and subtotal
            $updateItem = $conn->prepare("UPDATE sales_items SET quantity = ?, subtotal = ? WHERE id = ? AND sale_id = ?");
            $updateItem->execute([$qty, $subtotal, $item_id, $sale_id]);
        }

        $tax = $total * 0.10;
        $grand_total = $total + $tax;

        // Update sales table payment method and totals
        // NOTE: Assuming sales table has 'id' as PK, and columns: sub_total, tax, grand_total
        $updateSale = $conn->prepare("UPDATE sales SET payment_method = ?, sub_total = ?, tax = ?, grand_total = ? WHERE id = ?");
        $updateSale->execute([$payment_method, $total, $tax, $grand_total, $sale_id]);

        $conn->commit();

        header("Location: view_sale.php?id=" . $sale_id);
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Failed to update sale: " . $e->getMessage();
    }
}

// Fetch sale info and items for display
$saleStmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
$saleStmt->execute([$sale_id]);
$sale = $saleStmt->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    echo "Sale not found.";
    exit;
}

// Fetch sale items with product names (assuming you want to display product names)
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
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Update Sale #<?= $sale_id ?></title>
<link rel="stylesheet" href="assets/compiled/css/app.css" />
</head>
<body>
<div class="container mt-4">
  <h2>Update Sale #<?= $sale_id ?></h2>

  <form method="post" action="update_sale.php?id=<?= $sale_id ?>">
    <div class="mb-3">
      <label for="payment_method" class="form-label">Payment Method</label>
      <select id="payment_method" name="payment_method" class="form-select" required>
        <option value="GCASH" <?= $sale['payment_method'] === 'GCASH' ? 'selected' : '' ?>>GCASH</option>
        <option value="COD" <?= $sale['payment_method'] === 'COD' ? 'selected' : '' ?>>COD</option>
      </select>
    </div>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Price (₱)</th>
          <th>Quantity</th>
          <th>Subtotal (₱)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($saleItems as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['product_name']) ?></td>
          <td><?= number_format($item['price'], 2) ?></td>
          <td>
            <input
              type="number"
              name="quantity[<?= $item['id'] ?>]"
              value="<?= $item['quantity'] ?>"
              min="1"
              class="form-control"
              required
            />
          </td>
          <td><?= number_format($item['subtotal'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <button type="submit" class="btn btn-primary">Update Sale</button>
    <a href="manage_sales.php" class="btn btn-secondary">Back to Sales List</a>
  </form>
</div>
</body>
</html>
