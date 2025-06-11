<?php
session_start();

header('Content-Type: application/json');

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!$id || !in_array($action, ['increase', 'decrease'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

if (!isset($_SESSION['cart'][$id])) {
    echo json_encode(['status' => 'error', 'message' => 'Product not in cart']);
    exit;
}

// Lock session to avoid concurrent writes (simulate atomicity)
session_write_close(); // Unlock session for other scripts
session_start();       // Reopen session

// Fetch current cart quantity
$currentQty = $_SESSION['cart'][$id]['quantity'];

// Get product stock and price
$sql = "SELECT price, quantity AS stock FROM product WHERE id = ? AND deleted_at IS NULL LIMIT 1";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit;
}

$product = mysqli_fetch_assoc($result);
$price = (float)$product['price'];
$stock = (int)$product['stock'];

// Update quantity based on action with boundary checks
if ($action === 'increase' && $currentQty < $stock) {
    $currentQty++;
} elseif ($action === 'decrease' && $currentQty > 1) {
    $currentQty--;
}

// Update session cart quantity
$_SESSION['cart'][$id]['quantity'] = $currentQty;

// Calculate item total and grand total server side
$item_total = $price * $currentQty;
$grand_total = 0;

$product_ids = array_keys($_SESSION['cart']);
if (count($product_ids) > 0) {
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $types = str_repeat('i', count($product_ids));
    $sql_prices = "SELECT id, price FROM product WHERE id IN ($placeholders) AND deleted_at IS NULL";
    $stmt_prices = mysqli_prepare($con, $sql_prices);
    mysqli_stmt_bind_param($stmt_prices, $types, ...$product_ids);
    mysqli_stmt_execute($stmt_prices);
    $res_prices = mysqli_stmt_get_result($stmt_prices);

    $prices = [];
    while ($row = mysqli_fetch_assoc($res_prices)) {
        $prices[$row['id']] = $row['price'];
    }

    foreach ($_SESSION['cart'] as $pid => $item) {
        if (isset($prices[$pid])) {
            $grand_total += $prices[$pid] * $item['quantity'];
        }
    }
}

mysqli_close($con);

echo json_encode([
    'status' => 'success',
    'quantity' => $currentQty,
    'item_total' => number_format($item_total, 2, '.', ''),
    'grand_total' => number_format($grand_total, 2, '.', ''),
]);
