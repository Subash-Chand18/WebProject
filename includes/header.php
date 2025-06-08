<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Location: ../admin/Adminlogin.php");
    exit();
}
$adminName = $_SESSION['admin_name'] ?? $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-Clothing Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/Admindashboard.css" />
    <link rel="stylesheet" href="../assets/css/add_product.css" />
    <link rel="stylesheet" href="../assets/css/view_product_table.css" />
    <link rel="stylesheet" href="../css/edit_product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">


</head>
<body>
    <header class="topnav">
        <div class="logo"><i class="fas fa-tshirt"></i> E-Clothing Store</div>
        <nav class="topnav-menu">
            <a href="../admin/Admindashboard.php" class="nav-link">Home</a>
            <a href="#" class="nav-link">About</a>
            <a href="#" class="nav-link">Contact</a>
            <a href="#" class="nav-link">Help</a>
            <a href="../admin/Logout.php" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="welcome-msg"><i class="fas fa-user-circle"></i> Welcome, <strong><?php echo htmlspecialchars($adminName); ?></strong></div>
    </header>

    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="../admin/Admindashboard.php" class="sidebar-link"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li class="dropdown">
                <a href="#" class="sidebar-link dropdown-toggle"><i class="fas fa-box-open"></i> Products <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="../product/add.php" class="sidebar-sublink">Add Product</a></li>
                    <li><a href="../product/index.php" class="sidebar-sublink">View Products</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="sidebar-link dropdown-toggle"><i class="fas fa-tags"></i> Categories <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="#" class="sidebar-sublink">Men</a></li>
                    <li><a href="#" class="sidebar-sublink">Women</a></li>
                    <li><a href="#" class="sidebar-sublink">Babies</a></li>
                </ul>
            </li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-file-alt"></i> Reports</a></li>
        </ul>
    </aside>

    <main class="main-content">
