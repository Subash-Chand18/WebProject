<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get category ID from query param
$id = $_GET['id'] ?? null;

if (!$id) {
    // Redirect if no ID provided
    header("Location: view_category.php");
    exit;
}

// Soft delete by setting deleted_at timestamp
$deleted_at = date('Y-m-d H:i:s');
$sql = "UPDATE category SET deleted_at = '$deleted_at' WHERE id = '$id'";

if (mysqli_query($con, $sql)) {
    // Redirect back to category list after deletion
    header("Location: view_category.php");
    exit;
} else {
    echo "Error deleting category: " . mysqli_error($con);
}
?>