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
    $payment_method = mysqli_real_escape_string($con, $_POST['payment_method']);
    
    // Get shipping charge from form, default 0 if not provided
    $shipping_charge = isset($_POST['shipping_charge']) ? (float)$_POST['shipping_charge'] : 0;

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
            echo "<script>alert('Sorry, not enough stock for product ID: $product_id'); window.location.href='checkout.php';</script>";
            exit;
        }
    }

    // Insert into orders including shipping_charge
    $insertOrderQuery = "INSERT INTO orders (user_id, name, payment_method, shipping_charge) 
                         VALUES ('$user_id', '$customer_name', '$payment_method', '$shipping_charge')";
    if (mysqli_query($con, $insertOrderQuery)) {
        $order_id = mysqli_insert_id($con);

        // Insert shipping details
        $insertShippingQuery = "INSERT INTO shipping (order_id, shipping_address, delivery_address) 
                                VALUES ('$order_id', '$billing_address', '$shipping_address')";
        mysqli_query($con, $insertShippingQuery);

        // Insert order details and update stock
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $unit_price = $item['price'];

            // Calculate total (without shipping here, as it's handled in orders)
            $total = $quantity * $unit_price;

            $insertOrderDetailQuery = "INSERT INTO orderdetail 
                (order_id, product_id, quantity, unit_price, total) 
                VALUES 
                ('$order_id', '$product_id', '$quantity', '$unit_price', '$total')";
            mysqli_query($con, $insertOrderDetailQuery);

            // Update stock
            $updateProductQuery = "UPDATE product SET quantity = GREATEST(quantity - $quantity, 0) WHERE id = $product_id";
            mysqli_query($con, $updateProductQuery);
        }

        // Clear the cart
        unset($_SESSION['cart']);

        header("Location: order_success.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($con);
    }
} else {
    header("Location: checkout.php");
    exit;
}
?>
