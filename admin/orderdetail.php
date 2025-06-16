<?php
session_start();

// ✅ Check admin login
if (!isset($_SESSION['admin_email']) || $_SESSION['admin_type'] !== 'admin') {
    header("Location: Adminlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Handle search input safely
$search_condition = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    // search by order id or user name (partial match)
    $search_condition = " AND (o.id LIKE '%$search%' OR u.name LIKE '%$search%') ";
}

// ✅ Fetch order details grouped by order
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
    LEFT JOIN user u ON o.user_id = u.id AND u.deleted_at IS NULL
    LEFT JOIN shipping s ON s.order_id = o.id AND s.deleted_at IS NULL
    LEFT JOIN orderdetail od ON od.order_id = o.id AND od.deleted_at IS NULL
    LEFT JOIN product p ON od.product_id = p.id AND p.deleted_at IS NULL
    WHERE o.deleted_at IS NULL
    $search_condition
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
    <title>Admin - Order Details</title>
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
        .order-container { padding: 20px; }
        h2 { margin-bottom: 20px; }
        input[type="text"] { padding: 6px; }
        .search-form { margin-bottom: 20px; display: flex; gap: 10px; align-items: center; }
    </style>
</head>

<body>
    <!-- Top Navigation -->
    <header class="topnav">
        <div class="logo">
            <i class="fas fa-tshirt"></i> E-Clothing Store
        </div>
        <nav class="topnav-menu">
            <a href="#" class="nav-link">Home</a>
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
            <li><a href="admin_orders.php" class="sidebar-link"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="orderdetail.php" class="sidebar-link active"><i class="fas fa-clipboard-list"></i> Order Details</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <section class="order-container">
            <h2><i class="fas fa-clipboard-list"></i> Order Details</h2>

            <!-- Search form -->
            <form method="GET" action="orderdetail.php" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by Order ID or Username" 
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
                />
                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <!-- Orders table -->
            <div class="table-wrapper">
                <table>
                    <thead>
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
