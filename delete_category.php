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
        $sql = "DELETE FROM add_categories WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Category deleted successfully!'); window.location.href='manage_categories.php';</script>";
        } else {
            echo "<script>alert('Error deleting category.'); window.location.href='manage_categories.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('Invalid request. No ID provided.'); window.location.href='manage_categories.php';</script>";
}
?>
