<?php
session_start();

// Make sure user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add product or increase quantity
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]++;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }

    // Redirect back to dashboard or page where products are listed
    header("Location: cashier_dashboard.php");
    exit;
} else {
    // Invalid request, redirect back
    header("Location: cashier_dashboard.php");
    exit;
}
