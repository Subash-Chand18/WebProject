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
    $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
    $con = mysqli_connect("localhost", "root", "", "EClothingStore");
    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $stmt = mysqli_prepare($con, "SELECT id, name, price, image FROM product WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        // Store only image filename (not full path)
        $_SESSION['cart'][$product_id] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],  // Only filename
            'quantity' => 1
        ];
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        die("Product not found.");
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

// Redirect back to product listing or previous page
header("Location: index.php");
exit();