<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) die("Connection failed: " . mysqli_connect_error());

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: view.php");
    exit;
}

mysqli_query($con, "UPDATE product SET deleted_at = NOW() WHERE id = '$id'");
header("Location: view.php");
exit;
?>