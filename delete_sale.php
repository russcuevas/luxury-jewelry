<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$sale_id) {
    echo "Invalid sale ID.";
    exit;
}

try {
    $conn->beginTransaction();

    // Delete sale items first (if no FK ON DELETE CASCADE)
    $stmtItems = $conn->prepare("DELETE FROM sales_items WHERE sale_id = ?");
    $stmtItems->execute([$sale_id]);

    // Delete the sale itself
    $stmtSale = $conn->prepare("DELETE FROM sales WHERE id = ?");
    $stmtSale->execute([$sale_id]);

    $conn->commit();

    header("Location: manage_sales.php");
    exit;
} catch (Exception $e) {
    $conn->rollBack();
    echo "Failed to delete sale: " . $e->getMessage();
}
