<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $paymentMethod = $_POST['payment_method'] ?? '';
    $cart = json_decode($_POST['cart'], true);

    if (empty($paymentMethod) || empty($cart)) {
        // Invalid input
        header("Location: cashier_dashboard.php");
        exit;
    }

    // Fetch product details for price validation
    $productIds = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $conn->prepare("SELECT id, product_price FROM add_products WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate totals again for security
    $subTotal = 0;
    $itemsData = [];

    foreach ($products as $product) {
        $pid = $product['id'];
        $price = floatval($product['product_price']);
        $qty = isset($cart[$pid]) ? intval($cart[$pid]) : 0;
        if ($qty <= 0) continue;

        $subtotal = $price * $qty;
        $subTotal += $subtotal;

        $itemsData[] = [
            'product_id' => $pid,
            'quantity' => $qty,
            'price' => $price,
            'subtotal' => $subtotal
        ];
    }

    $tax = $subTotal * 0.10;
    $grandTotal = $subTotal + $tax;

    try {
        $conn->beginTransaction();

        // Insert into sales
        $saleStmt = $conn->prepare("INSERT INTO sales (cashier_id, payment_method, sub_total, tax, grand_total) VALUES (?, ?, ?, ?, ?)");
        $saleStmt->execute([$_SESSION['id'], $paymentMethod, $subTotal, $tax, $grandTotal]);
        $saleId = $conn->lastInsertId();

        // Insert sales items
        $itemStmt = $conn->prepare("INSERT INTO sales_items (sale_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
        foreach ($itemsData as $item) {
            $itemStmt->execute([$saleId, $item['product_id'], $item['quantity'], $item['price'], $item['subtotal']]);
        }

        $conn->commit();

        // Clear cart
        unset($_SESSION['cart']);

        // Redirect to sales management or receipt page
        header("Location: view_sale.php?id=$saleId");
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        echo "Failed to place order: " . $e->getMessage();
    }
} else {
    header("Location: cashier_dashboard.php");
    exit;
}
