<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in (using user login session)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

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

// Check if product is in user's cart session
if (!isset($_SESSION['cart'][$id])) {
    echo json_encode(['status' => 'error', 'message' => 'Product not in cart']);
    exit;
}

// Optional: To prevent session locking issues during DB calls
session_write_close();
session_start();

// Current quantity in cart
$currentQty = $_SESSION['cart'][$id]['quantity'];

// Get product price and stock from DB
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

// Update quantity within valid limits
if ($action === 'increase' && $currentQty < $stock) {
    $currentQty++;
} elseif ($action === 'decrease' && $currentQty > 1) {
    $currentQty--;
}

// Update session cart quantity
$_SESSION['cart'][$id]['quantity'] = $currentQty;

// Calculate item total price
$itemTotal = $price * $currentQty;

// Calculate grand total for all items in cart
$grandTotal = 0;
$productIds = array_keys($_SESSION['cart']);

if (!empty($productIds)) {
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $types = str_repeat('i', count($productIds));

    $stmt = mysqli_prepare($con, "SELECT id, price FROM product WHERE id IN ($placeholders) AND deleted_at IS NULL");
    // For mysqli_stmt_bind_param, pass arguments by reference in PHP < 8.0, use call_user_func_array if needed:
    $refs = [];
    foreach ($productIds as $key => $value) {
        $refs[$key] = &$productIds[$key];
    }
    mysqli_stmt_bind_param($stmt, $types, ...$refs);
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

// Return JSON response with updated quantities and totals
echo json_encode([
    'status' => 'success',
    'quantity' => $currentQty,
    'item_total' => number_format($itemTotal, 2, '.', ''),
    'grand_total' => number_format($grandTotal, 2, '.', '')
]);
