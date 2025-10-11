<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Validate order_id parameter
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order ID.");
}

$order_id = (int)$_GET['order_id'];

// Fetch order info, verify it belongs to the logged-in user
$stmtOrder = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmtOrder->execute([$order_id, $user_id]);
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found or access denied.");
}

// Fetch order items
$stmtItems = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmtItems->execute([$order_id]);
$orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Order Confirmation - LUXURY JEWELRY</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            padding: 2rem;
        }
        .order-summary table th,
        .order-summary table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4">Thank you for your order!</h1>

        <h3>Order Details</h3>
        <p><strong>Order Number:</strong> <?= htmlspecialchars($order['id']) ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
        <p><strong>Shipping Method:</strong> <?= htmlspecialchars($order['shipping']) ?></p>

        <h3>Billing Information</h3>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
        <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
        <p><strong>Phone Number:</strong> <?= htmlspecialchars($order['phone_number']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>

        <h3>Order Summary</h3>
        <div class="order-summary table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product Name</th>
                        <th>Unit Price (₱)</th>
                        <th>Quantity</th>
                        <th>Total (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= number_format($item['product_price'], 2) ?></td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td><?= number_format($item['product_price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">Grand Total</td>
                        <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a href="index.php" class="btn btn-primary mt-4">Continue Shopping</a>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
