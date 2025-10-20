<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM suppliers WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Suppliers deleted successfully!'); window.location.href='manage_suppliers.php';</script>";
        } else {
            echo "<script>alert('Error deleting deleted.'); window.location.href='manage_suppliers.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('Invalid request. No ID provided.'); window.location.href='manage_suppliers.php';</script>";
}
?>
