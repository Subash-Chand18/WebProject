<?php
session_start();
header('Content-Type: application/json');

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';
$cart = $_SESSION['cart'] ?? [];

if (!isset($cart[$id])) {
    echo json_encode(['status' => 'error', 'message' => 'Product not in cart']);
    exit;
}

// Get current stock from DB
$res = mysqli_query($con, "SELECT price, quantity FROM product WHERE id = $id AND deleted_at IS NULL");
if (!$res || mysqli_num_rows($res) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit;
}

$row = mysqli_fetch_assoc($res);
$stock = (int) $row['quantity'];
$price = (float) $row['price'];

$currentQty = (int) $cart[$id]['quantity'];

if ($action === 'increase' && $currentQty < $stock) {
    $cart[$id]['quantity']++;
} elseif ($action === 'decrease' && $currentQty > 1) {
    $cart[$id]['quantity']--;
}

$_SESSION['cart'] = $cart;

// Calculate item total and grand total
$itemTotal = number_format($cart[$id]['quantity'] * $price, 2);

$grandTotal = 0;
foreach ($cart as $pid => $item) {
    $res = mysqli_query($con, "SELECT price FROM product WHERE id = $pid AND deleted_at IS NULL");
    if ($res && mysqli_num_rows($res) > 0) {
        $p = mysqli_fetch_assoc($res);
        $grandTotal += $item['quantity'] * $p['price'];
    }
}

echo json_encode([
    'status' => 'success',
    'quantity' => $cart[$id]['quantity'],
    'item_total' => $itemTotal,
    'grand_total' => number_format($grandTotal, 2)
]);
?>
