<?php
session_start();

if (!isset($_SESSION['admin_email']) || $_SESSION['admin_type'] !== 'admin') {
    header("Location: Adminlogin.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Dashboard Stats
$res = mysqli_query($con, "SELECT COUNT(*) AS total FROM product WHERE deleted_at IS NULL");
$totalProducts = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(*) AS total FROM users WHERE user_type = 'user' AND deleted_at IS NULL");
$totalUsers = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(*) AS total FROM orders WHERE order_status='pending' AND deleted_at IS NULL");
$pendingOrders = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT SUM(od.quantity * od.unit_price) + SUM(o.shipping_charge) AS total 
                           FROM orderdetail od 
                           JOIN orders o ON od.order_id = o.id 
                           WHERE o.deleted_at IS NULL 
                             AND o.order_status = 'Delivered'");

$totalRevenue = mysqli_fetch_assoc($res)['total'] ?? 0;

$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_email'];

$storeSettingsQuery = mysqli_query($con, "SELECT store_name, store_logo FROM store_settings ORDER BY id DESC LIMIT 1");
$storeSettings = mysqli_fetch_assoc($storeSettingsQuery);

$storeName = $storeSettings['store_name'] ?? "E-Clothing Store";
$storeLogo = $storeSettings['store_logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Clothing Store Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/Admindashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

<header class="topnav">
    <div class="logo">
    <?php if ($storeLogo): ?>
        <img src="../assets/images/<?= htmlspecialchars($storeLogo) ?>" 
             alt="<?= htmlspecialchars($storeName) ?> Logo" 
             style="height: 40px; width: 40px; border-radius: 50%; object-fit: cover; vertical-align: middle; margin-right: 8px;">
    <?php else: ?>
        <i class="fas fa-tshirt"></i>
    <?php endif; ?>
    <?= htmlspecialchars($storeName) ?>
</div>

    <nav class="topnav-menu">
        <a href="Admindashboard.php" class="nav-link active">Home</a>
        <a href="logout.php" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    <div class="welcome-msg">
        <i class="fas fa-user-circle"></i> Welcome, <strong><?= htmlspecialchars($adminName) ?></strong>
    </div>
</header>

<aside class="sidebar">
    <ul class="sidebar-menu">
        <li><a href="Admindashboard.php" class="sidebar-link active"><i class="fas fa-chart-line"></i> Dashboard</a></li>

        <li class="dropdown">
            <a href="#" class="sidebar-link dropdown-toggle">
                <i class="fas fa-box-open"></i> Products <i class="fas fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a href="../product/add.php" class="sidebar-sublink">Add Product</a></li>
                <li><a href="../product/view.php" class="sidebar-sublink">View Products</a></li>
            </ul>
        </li>

        <li class="dropdown">
            <a href="#" class="sidebar-link dropdown-toggle">
                <i class="fas fa-tags"></i> Categories <i class="fas fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a href="../category/add_category.php" class="sidebar-sublink">Add Category</a></li>
                <li><a href="../category/view_category.php" class="sidebar-sublink">View Categories</a></li>
            </ul>
        </li>

        <li><a href="customers.php" class="sidebar-link"><i class="fas fa-users"></i> Customers</a></li>
        <li><a href="Adminorders.php" class="sidebar-link"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="orderdetails.php" class="sidebar-link"><i class="fas fa-clipboard-list"></i> Order Details</a></li>
         <li><a href="product_rating_review.php" class="sidebar-link"><i class="fas fa-comment-dots"></i> Review</a></li>
        <li><a href="report.php" class="sidebar-link"><i class="fas fa-file-alt"></i> Reports</a></li>
        <li><a href="setting.php" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
</aside>

<main class="main-content">
    <h1>Dashboard Overview</h1>

    <div class="stats-container">
        <div class="stat-box">
            <i class="fas fa-box stat-icon"></i>
            <h3>Total Products</h3>
           <p><span class="count" data-target="<?= $totalProducts ?>">0</span></p>

        </div>
        <div class="stat-box">
            <i class="fas fa-users stat-icon"></i>
            <h3>Total Users</h3>
            <p><span class="count" data-target="<?= $totalUsers ?>">0</span></p>
        </div>
        <div class="stat-box">
            <i class="fas fa-hourglass-half stat-icon"></i>
            <h3>Pending Orders</h3>
            <p><span class="count" data-target="<?= $pendingOrders ?>">0</span></p>
        </div>
        <div class="stat-box">
            <i class="fas fa-dollar-sign stat-icon"></i>
            <h3>Total Revenue</h3>
            <p>Rs. <span class="count" data-target="<?= (int)$totalRevenue ?>">0</span>.00</p>

        </div>
    </div>
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
    <script>
    const counters = document.querySelectorAll('.count');
    const speed = 100; // animation speed, adjust as needed

    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        let count = 0;

        const updateCount = () => {
            const increment = Math.ceil(target / speed);

            if (count < target) {
                count += increment;
                if (count > target) count = target;
                counter.innerText = count.toLocaleString('en-IN'); // adds commas for thousands
                setTimeout(updateCount, 20);
            } else {
                counter.innerText = target.toLocaleString('en-IN');
            }
        };

        updateCount();
    });
</script>

</body>
</html>