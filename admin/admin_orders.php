<?php
session_start();

// Check admin login
if (!isset($_SESSION['admin_email']) || $_SESSION['admin_type'] !== 'admin') {
    header("Location: Adminlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = (int)$_POST['order_id'];
    $order_status = mysqli_real_escape_string($con, $_POST['order_status']);
    $update_sql = "UPDATE orders SET order_status='$order_status' WHERE id=$order_id";
    mysqli_query($con, $update_sql);
    // Redirect to avoid form resubmission
    header("Location: admin_orders.php");
    exit();
}

// Fetch orders with user and product details
$sql = "
    SELECT 
        o.id AS order_id, 
        o.name AS order_name, 
        o.order_status, 
        o.payment_method, 
        o.created_at,
        u.name AS user_name, 
        u.email AS user_email,
        GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names,
        SUM(od.quantity) AS total_quantity,
        SUM(od.unit_price * od.quantity) AS total_price
    FROM orders o
    LEFT JOIN user u ON o.user_id = u.id
    LEFT JOIN orderdetail od ON od.order_id = o.id
    LEFT JOIN product p ON od.product_id = p.id
    WHERE o.deleted_at IS NULL
    GROUP BY o.id
    ORDER BY o.created_at ASC
";

$res = mysqli_query($con, $sql);
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Orders Management</title>
    <link rel="stylesheet" href="../assets/css/Admindashboard.css" />
    <link rel="stylesheet" href="../assets/css/add_product.css" />
    <link rel="stylesheet" href="../assets/css/view_product_table.css" />
    <link rel="stylesheet" href="../assets/css/edit_product.css" />
    <link rel="stylesheet" href="../assets/css/add_category.css">
    <link rel="stylesheet" href="../assets/css/edit_category.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        table { margin-top: 20px; width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        .status-select { width: 150px; padding: 5px; }
        .orders-container { padding: 20px; }
        .table-wrapper { overflow-x: auto; }
        h2 { margin-bottom: 20px; }
    </style>
</head>

<body>
    <!-- Top Navigation -->
    <header class="topnav">
        <div class="logo">
            <i class="fas fa-tshirt"></i> E-Clothing Store
        </div>
        <nav class="topnav-menu">
            <a href="#" class="nav-link active">Home</a>
            <a href="logout.php" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="welcome-msg">
            <i class="fas fa-user-circle"></i> Welcome, <strong><?php echo htmlspecialchars($adminName); ?></strong>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="Admindashboard.php" class="sidebar-link"><i class="fas fa-chart-line"></i> Dashboard</a></li>

            <!-- Products with dropdown -->
            <li class="dropdown">
                <a href="#" class="sidebar-link dropdown-toggle">
                    <i class="fas fa-box-open"></i> Products <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../product/add.php" class="sidebar-sublink">Add Product</a></li>
                    <li><a href="../product/view.php" class="sidebar-sublink">View Products</a></li>
                </ul>
            </li>

            <!-- Categories with dropdown -->
            <li class="dropdown">
                <a href="#" class="sidebar-link dropdown-toggle">
                    <i class="fas fa-tags"></i> Categories <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../category/add_category.php" class="sidebar-sublink">Add Category</a></li>
                    <li><a href="../category/view_category.php" class="sidebar-sublink">View Categories</a></li>
                </ul>
            </li>

            <li><a href="customer.php" class="sidebar-link"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="admin_orders.php" class="sidebar-link active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="orderdetail.php" class="sidebar-link"><i class="fas fa-clipboard-list"></i> Order Details</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <section class="orders-container">
            <h2><i class="fas fa-shopping-cart"></i> Orders Management</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#Order ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Order Date</th>
                            <th>Payment Method</th>
                            <th>Product Names</th>
                            <th>Total Qty</th>
                            <th>Total Price (Rs)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res && mysqli_num_rows($res) > 0): ?>
                            <?php while ($order = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td><?= $order['order_id'] ?></td>
                                <td><?= htmlspecialchars($order['order_name']) ?: htmlspecialchars($order['user_name']) ?></td>
                                <td><?= htmlspecialchars($order['user_email']) ?></td>
                                <td><?= date('Y-m-d h:i A', strtotime($order['created_at'])) ?></td>
                                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                <td><?= htmlspecialchars($order['product_names']) ?></td>
                                <td><?= (int)$order['total_quantity'] ?></td>
                                <td><?= number_format($order['total_price'], 2) ?></td>
                                <td>
                                    <form method="POST" action="admin_orders.php">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <select name="order_status" class="status-select" onchange="this.form.submit()">
                                            <?php 
                                            $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
                                            foreach ($statuses as $status):
                                            ?>
                                            <option value="<?= $status ?>" <?= ($order['order_status'] === $status) ? 'selected' : '' ?>><?= $status ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <noscript><button type="submit" class="btn btn-primary btn-sm">Update</button></noscript>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="9" class="text-center text-muted">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 E-Clothing Store. All Rights Reserved.</p>
    </footer>

    <!-- Scripts -->
    <script>
        // Dropdown toggle for categories and products
        document.querySelectorAll('.dropdown-toggle').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('open');
            });
        });

        // Sidebar link active state toggle
        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            link.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-link').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Topnav menu active state toggle
        document.querySelectorAll('.topnav-menu .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                document.querySelectorAll('.topnav-menu .nav-link').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>
