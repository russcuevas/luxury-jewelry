<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    // Make sure cart exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_POST['increment'])) {
        // Increase quantity
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]++;
        } else {
            $_SESSION['cart'][$productId] = 1;
        }
    } elseif (isset($_POST['decrement'])) {
        // Decrease quantity
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]--;
            if ($_SESSION['cart'][$productId] <= 0) {
                // Remove if quantity <= 0
                unset($_SESSION['cart'][$productId]);
            }
        }
    }
    // Redirect back to the page with cart (dashboard)
    header("Location: cashier_dashboard.php");
    exit;
} else {
    // Invalid access
    header("Location: cashier_dashboard.php");
    exit;
}
