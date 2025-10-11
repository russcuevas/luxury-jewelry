<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    // Not logged in
    header('Location: user_login.php');
    exit;
}

$userId = $_SESSION['id'];
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($productId <= 0) {
    // Invalid product ID
    header('Location: shop.php?error=InvalidProduct');
    exit;
}

// Check if the product exists and is active
$productCheckStmt = $conn->prepare("SELECT * FROM add_products WHERE id = :product_id AND product_status = 'active'");
$productCheckStmt->execute([':product_id' => $productId]);
$product = $productCheckStmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    // Product not found or inactive
    header('Location: shop.php?error=ProductNotFound');
    exit;
}

// Check if product already in user's cart
$cartCheckStmt = $conn->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
$cartCheckStmt->execute([':user_id' => $userId, ':product_id' => $productId]);
$cartItem = $cartCheckStmt->fetch(PDO::FETCH_ASSOC);

if ($cartItem) {
    // Update quantity if already in cart
    $updateStmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = :cart_id");
    $updateStmt->execute([':cart_id' => $cartItem['id']]);
} else {
    // Insert new cart item
    $insertStmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)");
    $insertStmt->execute([':user_id' => $userId, ':product_id' => $productId]);
}

header('Location: shop.php?success=added');
exit;
?>
