<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and fetch POST data
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $phone = $_POST['mobile'];
    $email = $_POST['email'];
    $shipping = $_POST['shipping'];
    $payment_method = $_POST['payment_method'];
    $cart_ids = $_POST['cart_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if (empty($cart_ids) || empty($quantities) || count($cart_ids) !== count($quantities)) {
        die("Invalid cart data.");
    }

    // Start transaction
    $conn->beginTransaction();

    try {
        // Calculate total again from DB for security
        $total_amount = 0;
        $products = [];

        foreach ($cart_ids as $index => $cart_id) {
            $stmt = $conn->prepare("SELECT p.id AS product_id, p.product_name, p.product_price FROM cart c JOIN add_products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
            $stmt->execute([$cart_id, $user_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Invalid product in cart.");
            }

            $qty = (int)$quantities[$index];
            $subtotal = $product['product_price'] * $qty;
            $total_amount += $subtotal;

            $products[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'product_price' => $product['product_price'],
                'quantity' => $qty
            ];
        }

        // Insert into orders table
        $stmtOrder = $conn->prepare("INSERT INTO orders (user_id, fullname, address, phone_number, email, shipping, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtOrder->execute([$user_id, $fullname, $address, $phone, $email, $shipping, $payment_method, $total_amount]);
        $order_id = $conn->lastInsertId();

        // Insert order items
        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)");
        foreach ($products as $prod) {
            $stmtItem->execute([$order_id, $prod['product_id'], $prod['product_name'], $prod['product_price'], $prod['quantity']]);
        }

        // Optionally clear the cart after placing the order
        $stmtClearCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmtClearCart->execute([$user_id]);

        $conn->commit();

        // Redirect to order confirmation page or thank you page
        header("Location: order_success.php?order_id=" . $order_id);
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        echo "Failed to place order: " . $e->getMessage();
    }
} else {
    header("Location: checkout.php");
    exit;
}