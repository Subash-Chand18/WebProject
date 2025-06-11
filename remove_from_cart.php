<?php
session_start();

// Check if product ID is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Check if cart exists and product is in the cart
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]); // Remove the item
    }
}

// Redirect back to the cart page
header("Location: cart.php");
exit;
