<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}
//include "../../settings.php";
// echo BASE_URL; exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>E-Clothing Store</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../design-assets/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="../design-assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../design-assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../design-assets/css/product_details.css" rel="stylesheet">
    <link href="../design-assets/css/order_success.css" rel="stylesheet">
    <link href="../design-assets/css/myorder.css" rel="stylesheet">
    <link href="../design-assets/css/style.css" rel="stylesheet"> 
</head>

<body>

<!-- Spinner Start -->
<div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
    <div class="spinner-grow text-primary" role="status"></div>
</div>
<!-- Spinner End -->

<!-- Navbar start -->
<div class="container-fluid fixed-top">
    <div class="container topbar bg-primary d-none d-lg-block">
        <div class="d-flex justify-content-between">
            <div class="top-info ps-2">
                <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">Dhangadhi ,Kailali</a></small>
                <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">user@gmail.com</a></small>
            </div>
            <div class="top-link pe-2">
                <a href="#" class="text-white"><small class="text-white mx-2">Privacy Policy</small>/</a>
                <a href="#" class="text-white"><small class="text-white mx-2">Terms of Use</small>/</a>
            </div>
        </div>
    </div><br>
    <div class="container px-0">
        <nav class="navbar navbar-light bg-white navbar-expand-xl">
            <a href="./../index.php" class="navbar-brand">
                <h2 class="text-primary display-6">Clothes</h2>
                <h3 class="mb-3 text-secondary">Buy now pay Latter</h3>
            </a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="./../index.php" class="nav-item nav-link active">Home</a> 
                    <a href="./our_shop.php" class="nav-item nav-link">Shop</a>
                    <a href="./contact.php" class="nav-item nav-link">Contact</a>
                    <?php if (isset($_SESSION['user_id'])): ?><a href="./myorders.php" class="nav-item nav-link">My Orders</a><?php endif; ?>
                </div>
                        <div class="d-flex align-items-center gap-3">
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <a href="./Userlogin.php" class="btn btn-outline-dark">Login</a>
                            <?php else: ?>
                                <span class="text-dark fw-bold">Welcome,
                                    <?= htmlspecialchars($_SESSION['user_name']); ?></span>
                                <a href="./logout.php" class="btn btn-danger">Logout</a>
                            <?php endif; ?>
                        </div>                
                    <div class="d-flex m-3 me-0">
                        <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button>
                        <a href="./cart.php" class="position-relative me-4 my-auto">
                        <i class="fa fa-shopping-bag fa-2x"></i>
                        <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;"> <?php echo isset($cartCount) ? $cartCount : 0; ?></span>
                    </a>
                    <a href="#" class="my-auto">
                        <i class="fas fa-user fa-2x"></i>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</div>
<!-- Navbar End -->

<!-- Modal Search Start -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center">
                <div class="input-group w-75 mx-auto d-flex">
                    <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                    <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>