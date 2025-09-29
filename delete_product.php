<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT product_image FROM add_products WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if (!empty($product['product_image']) && file_exists('uploads/' . $product['product_image'])) {
            unlink('uploads/' . $product['product_image']);
        }

        $delete = $conn->prepare("DELETE FROM add_products WHERE id = :id");
        $delete->bindParam(':id', $id, PDO::PARAM_INT);

        if ($delete->execute()) {
            echo "<script>alert('Product deleted successfully!'); window.location.href='manage_products.php';</script>";
        } else {
            echo "<script>alert('Error deleting product.'); window.location.href='manage_products.php';</script>";
        }
    } else {
        echo "<script>alert('Product not found.'); window.location.href='manage_products.php';</script>";
    }
}
