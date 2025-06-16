<?php
session_start();

// ✅ Check admin login
if (!isset($_SESSION['admin_type']) || $_SESSION['admin_type'] !== 'admin') {
    header("Location: Adminlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Handle search
$search_condition = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    $search_condition = "AND (o.id LIKE '%$search%' OR u.name LIKE '%$search%')";
}

// ✅ Fetch order details grouped per order
$sql = "
    SELECT 
        o.id AS order_id, 
        o.name AS order_name, 
        o.order_status, 
        o.shipping_charge, 
        o.created_at,
        s.shipping_address, 
        s.delivery_address,
        u.name AS user_name, 
        u.email AS user_email,
        GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') AS product_names,
        SUM(od.quantity) AS total_quantity,
        SUM(od.unit_price * od.quantity) + o.shipping_charge AS total_price
    FROM orders o
    LEFT JOIN user u ON o.user_id = u.id
    LEFT JOIN shipping s ON s.order_id = o.id
    LEFT JOIN orderdetail od ON od.order_id = o.id
    LEFT JOIN product p ON od.product_id = p.id
    WHERE o.deleted_at IS NULL $search_condition
    GROUP BY o.id
    ORDER BY o.created_at ASC
";

$res = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Order Details</title>
    <link href="../design-assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; font-family: Arial, sans-serif; }
        table { margin-top: 20px; }
        th, td { vertical-align: middle !important; }
    </style>
</head>
<body>
    <h1>Order Details</h1>

    <!-- ✅ Search Form -->
    <form method="GET" action="orderdetail.php" class="mb-3">
        <input type="text" name="search" placeholder="Search by Order ID or Username" class="form-control w-25 d-inline" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- ✅ Order Details Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-info">
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Product Name(s)</th>
                <th>Email</th>
                <th>Order Status</th>
                <th>Shipping Address</th>
                <th>Delivery Address</th>
                <th>Shipping Charge (Rs)</th>
                <th>Total Quantity</th>
                <th>Total (Rs)</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res && mysqli_num_rows($res) > 0): ?>
                <?php while ($order = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['order_name']) ?: htmlspecialchars($order['user_name']) ?></td>
                        <td><?= htmlspecialchars($order['product_names']) ?></td>
                        <td><?= htmlspecialchars($order['user_email']) ?></td>
                        <td><?= htmlspecialchars($order['order_status']) ?></td>
                        <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                        <td><?= htmlspecialchars($order['delivery_address']) ?></td>
                        <td><?= number_format($order['shipping_charge'], 2) ?></td>
                        <td><?= (int)$order['total_quantity'] ?></td>
                        <td><?= number_format($order['total_price'], 2) ?></td>
                        <td><?= date('Y-m-d h:i A', strtotime($order['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center text-muted">No order details found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script src="../design-assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
