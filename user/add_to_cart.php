<?php
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = (int) $_GET['id'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    // Increase quantity
    $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
    // First time adding product - fetch product details
    $con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");

    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $stmt = mysqli_prepare($con, "SELECT id, name, price, image FROM product WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        $_SESSION['cart'][$product_id] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => 1
        ];
    } else {
        die("Product not found.");
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

header("Location: ../index.php");
exit;