<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Userlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "EClothingStore");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize form data
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $billing_address = mysqli_real_escape_string($con, $_POST['billing_address']);
    $shipping_address = mysqli_real_escape_string($con, $_POST['shipping_address']);
    $country = mysqli_real_escape_string($con, $_POST['country']);
    $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $shipping_charge = isset($_POST['shipping_charge']) ? (float)$_POST['shipping_charge'] : 0;
    $payment_method = mysqli_real_escape_string($con, $_POST['payment_method']);

    $user_id = $_SESSION['user_id'];
    $customer_name = $first_name . ' ' . $last_name;

    // Check stock availability
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['id'];
        $quantity = $item['quantity'];

        $checkStockQuery = "SELECT quantity FROM product WHERE id = $product_id";
        $stockResult = mysqli_query($con, $checkStockQuery);
        $stockRow = mysqli_fetch_assoc($stockResult);

        if ($stockRow['quantity'] < $quantity) {
            echo "<script>alert('Sorry, not enough stock for product ID: $product_id'); window.location.href='chackout.php';</script>";
            exit;
        }
    }

    // Calculate subtotal
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $quantity = $item['quantity'];
        $unit_price = $item['price'];
        $subtotal += $quantity * $unit_price;
    }

    // Final total
    $total = $subtotal + $shipping_charge;

    // Insert into orders (store shipping_charge)
    $insertOrderQuery = "INSERT INTO orders (user_id, name, payment_method, shipping_charge) 
                         VALUES ('$user_id', '$customer_name', '$payment_method', '$shipping_charge')";

    if (mysqli_query($con, $insertOrderQuery)) {
        $order_id = mysqli_insert_id($con);

        // Insert into shipping table
        $insertShippingQuery = "INSERT INTO shipping (order_id, billing_address, shipping_address) 
                                VALUES ('$order_id', '$billing_address', '$shipping_address')";
        mysqli_query($con, $insertShippingQuery);

        // Insert each product into orderdetail
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $unit_price = $item['price'];
            $product_total = $quantity * $unit_price;

            $insertOrderDetailQuery = "INSERT INTO orderdetail 
                (order_id, product_id, quantity, unit_price, total) 
                VALUES 
                ('$order_id', '$product_id', '$quantity', '$unit_price', '$product_total')";
            mysqli_query($con, $insertOrderDetailQuery);

            // Update product stock
            $updateProductQuery = "UPDATE product SET quantity = GREATEST(quantity - $quantity, 0) WHERE id = $product_id";
            mysqli_query($con, $updateProductQuery);
        }

        // Clear cart
        unset($_SESSION['cart']);

        // Redirect to success page with order ID
        header("Location: order_success.php?order_id=$order_id");
        exit;
    } else {
        echo "Error placing order: " . mysqli_error($con);
    }
} else {
    header("Location: chackout.php");
    exit;
}
?>
