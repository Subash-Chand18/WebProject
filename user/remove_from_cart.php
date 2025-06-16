<?php
session_start();

// Check if product ID is provided via GET
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Check if cart exists and the product ID is in the cart
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]); // Remove product from cart
    }
}

// Redirect user back to the cart page
header("Location: cart.php");
exit;
?>
