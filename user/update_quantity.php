<?php
session_start();
header('Content-Type: application/json');

// Database connection
$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Validate request
if ($id <= 0 || !in_array($action, ['increase', 'decrease'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

if (!isset($_SESSION['cart'][$id])) {
    echo json_encode(['status' => 'error', 'message' => 'Product not in cart']);
    exit;
}

// Lock session to prevent write collisions
session_write_close();
session_start();

// Get current cart quantity
$currentQty = $_SESSION['cart'][$id]['quantity'];

// Fetch product stock and price
$stmt = mysqli_prepare($con, "SELECT price, quantity AS stock FROM product WHERE id = ? AND deleted_at IS NULL LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit;
}

$product = mysqli_fetch_assoc($result);
$price = (float)$product['price'];
$stock = (int)$product['stock'];

// Update quantity with boundary checks
if ($action === 'increase' && $currentQty < $stock) {
    $currentQty++;
} elseif ($action === 'decrease' && $currentQty > 1) {
    $currentQty--;
}

// Update session cart
$_SESSION['cart'][$id]['quantity'] = $currentQty;

// Calculate item total
$itemTotal = $price * $currentQty;

// Calculate grand total
$grandTotal = 0;
$productIds = array_keys($_SESSION['cart']);

if (!empty($productIds)) {
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $types = str_repeat('i', count($productIds));

    $stmt = mysqli_prepare($con, "SELECT id, price FROM product WHERE id IN ($placeholders) AND deleted_at IS NULL");
    mysqli_stmt_bind_param($stmt, $types, ...$productIds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $prices = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $prices[$row['id']] = $row['price'];
    }

    foreach ($_SESSION['cart'] as $pid => $item) {
        if (isset($prices[$pid])) {
            $grandTotal += $prices[$pid] * $item['quantity'];
        }
    }
}

mysqli_close($con);

// Return response
echo json_encode([
    'status' => 'success',
    'quantity' => $currentQty,
    'item_total' => number_format($itemTotal, 2, '.', ''),
    'grand_total' => number_format($grandTotal, 2, '.', '')
]);
?>
