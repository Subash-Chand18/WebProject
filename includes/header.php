<?php
session_start();
if (!isset($_SESSION['admin_email']) || $_SESSION['admin_type'] !== 'admin') {

    header("Location: ../admin/Adminlogin.php");
    exit();
}
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
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
    <link rel="stylesheet" href="../assets/css/add_product.css" />
    <link rel="stylesheet" href="../assets/css/view_product.css" />
    <link rel="stylesheet" href="../assets/css/edit_product.css" />
    <link rel="stylesheet" href="../assets/css/customers.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

</head>

<body>
    <!-- Top Navigation -->
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
            <a href="#" class="nav-link active">Home</a>
            <a href="../admin/logout.php" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="welcome-msg">
            <i class="fas fa-user-circle"></i> Welcome, <strong><?php echo htmlspecialchars($adminName); ?></strong>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="../admin/Admindashboard.php" class="sidebar-link active"><i class="fas fa-chart-line"></i>
                    Dashboard</a></li>

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

            <li><a href="../admin/customers.php" class="sidebar-link"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="../admin/Adminorders.php" class="sidebar-link"><i class="fas fa-shopping-cart"></i> Orders</a>
            </li>
            <li><a href="../admin/orderdetails.php" class="sidebar-link"><i class="fas fa-clipboard-list"></i> Order
                    Details</a></li>
            <li><a href="../admin/product_rating_review.php" class="sidebar-link"><i class="fas fa-comment-dots"></i>
                    Review</a></li>
            <li><a href="../admin/report.php" class="sidebar-link"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="../admin/setting.php" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>

        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">