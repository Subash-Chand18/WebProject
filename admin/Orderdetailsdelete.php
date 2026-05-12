<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) die("Connection failed: " . mysqli_connect_error());

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header("Location: orderdetails.php");
    exit;
}

// Soft delete
$query = "UPDATE orders SET deleted_at = NOW() WHERE id = $id";
if (!mysqli_query($con, $query)) {
    die("Error deleting record: " . mysqli_error($con));
}

header("Location: orderdetails.php");
exit;
?>
