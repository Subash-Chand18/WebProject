<?php
session_start();
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// db connection
$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all products with category name
$sql = "SELECT p.*, c.name AS category_name 
        FROM product p
        LEFT JOIN category c ON p.category_id = c.id
        ORDER BY p.id DESC";
$res = mysqli_query($con, $sql);

// Group products by category name
$productsByCategory = [];
$allProducts = [];

while ($row = mysqli_fetch_assoc($res)) {
    $allProducts[] = $row;  // For "All Products" tab
    $category = $row['category_name'] ?? 'Uncategorized';
    $productsByCategory[$category][] = $row;
}

// Fetch ONLY New Products (within the last 2 days)
$sqlNew = "SELECT p.*, c.name AS category_name 
           FROM product p
           LEFT JOIN category c ON p.category_id = c.id
           WHERE p.created_at >= NOW() - INTERVAL 2 DAY
           ORDER BY p.created_at DESC";

$resNew = mysqli_query($con, $sqlNew);

// Store New Products
$newProducts = [];

while ($rowNew = mysqli_fetch_assoc($resNew)) {
    $newProducts[] = $rowNew;
}

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>E-Clothing Store</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>


        <!-- Libraries Stylesheet -->
        <link href="design-assets/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="design-assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="design-assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="design-assets/css/style.css" rel="stylesheet">

        <style>
        .clothing-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .clothing-item:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 128, 0, 0.4); /* green shadow */
            z-index: 10;
        }
        </style>
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
                        <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">Dhangadhi, Kailali</a></small>
                        <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">user@gmail.com</a></small>
                    </div>
                    <div class="top-link pe-2">
                        <a href="#" class="text-white"><small class="text-white mx-2">Privacy Policy</small>/</a>
                        <a href="#" class="text-white"><small class="text-white mx-2">Terms of Use</small></a>
                    </div>
                </div>
            </div>

            <div class="container px-0">
                <nav class="navbar navbar-light bg-white navbar-expand-xl">
                    <a href="index.php" class="navbar-brand"><h2 class="text-primary display-6">E-Clothing Store</h2></a>
                    <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars text-primary"></span>
                    </button>

                    <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                        <div class="navbar-nav mx-auto">
                            <a href="index.php" class="nav-item nav-link active">Home</a>
                            <a href="#" class="nav-item nav-link">Shop</a>
                            <a href="#" class="nav-item nav-link">Contact</a>
                            <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Show this link only when the user is logged in -->
                                <a href="user/myorders.php" class="nav-item nav-link">My Orders</a>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <a href="user/Userlogin.php" class="btn btn-outline-dark">Login</a>
                            <?php else: ?>
                                <span class="text-dark fw-bold">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></span>
                                <a href="user/logout.php" class="btn btn-danger">Logout</a>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex m-3 me-0">
                            <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal">
                                <i class="fas fa-search text-primary"></i>
                            </button>
                            <a href="user/cart.php" class="position-relative me-4 my-auto">
                                <i class="fa fa-shopping-bag fa-2x"></i>
                                <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                                    style="top: -5px; left: 15px; height: 20px; min-width: 20px;">
                                    <?php echo isset($cartCount) ? $cartCount : 0; ?>
                                </span>
                            </a>
                            <a href="#" class="my-auto"><i class="fas fa-user fa-2x"></i>
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
        <!-- Modal Search End -->


        <!-- Hero Start -->
        <div class="container-fluid py-5 mb-5 hero-header">
            <style>
                /* Hover Effects */
                .carousel-item img {
                    transition: transform 0.5s ease, box-shadow 0.5s ease;
                }

                .carousel-item img:hover {
                    transform: scale(1.05);
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
                    z-index: 1;
                }

                .carousel-item a {
                    transition: background-color 0.3s ease, color 0.3s ease;
                }

                .carousel-item a:hover {
                    background-color: white !important;
                    color: #0d6efd !important; /* Bootstrap Primary color */
                }

                .btn-primary:hover {
                    background-color: #0b5ed7 !important;
                    border-color: #0a58ca !important;
                }
            </style>

            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-md-12 col-lg-7">
                        <h4 class="mb-3 text-secondary">100% Best Suitable Clothes</h4>
                        <h1 class="mb-5 display-3 text-primary">Brand New Clothes</h1>
                        <div class="position-relative mx-auto">
                            <input class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill" type="text" placeholder="Search">
                            <button type="submit" class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100" style="top: 0; right: 25%;">Submit Now</button>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-5">
                        <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                            <div class="carousel-inner" role="listbox">

                                <div class="carousel-item active rounded">
                                    <img src="assets/images/shoes2.jpg" class="img-fluid w-100 h-100 bg-secondary rounded" alt="First slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Men</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/black gown.jpeg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Women</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/classic white t-shirt.jpg" class="img-fluid w-100 h-100 bg-secondary rounded" alt="First slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Men</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/babies combo set.jpg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Babies</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/Kurta set.jpg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Women</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/unisex hiphop tshirt.jpg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Free Sized</a>
                                </div>

                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero End -->



        <!-- Features Section Start -->
        <style>
            /* Container background */
            .featurs {
                background-color: #f8f9fa;
            }

            /* Feature item base styles */
            .featurs-item {
                background-color: #f8f9fa;
                background-clip: padding-box;
                border-radius: 0.5rem;
                padding: 1.5rem;
                text-align: center;
                background-color: #f8f9fa;
                cursor: pointer;

                /* Transition for smooth hover */
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            /* Hover effect with green shadow and slight scale */
            .featurs-item:hover {
                transform: scale(1.05);
                box-shadow: 0 8px 16px rgba(0, 128, 0, 0.4); /* green shadow */
                z-index: 2;
            }

            /* Icon container common styles */
            .featurs-icon {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                margin: 0 auto 1rem auto;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Gradient backgrounds for icons */
            .bg-gradient-blue {
                background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            }

            .bg-gradient-orange {
                background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
            }

            .bg-gradient-green {
                background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            }

            .bg-gradient-red {
                background: linear-gradient(135deg, #ff5f6d 0%, #ffc371 100%);
            }

            /* Text styling */
            .featurs-content h5 {
                margin-bottom: 0.5rem;
            }

            .text-muted {
                color: #6c757d;
            }
        </style>

        <div class="container-fluid featurs py-5" style="background-color: #f8f9fa;">
            <div class="container py-5">
                <div class="row g-4">

                    <!-- Feature Item 1 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-4">
                            <div class="featurs-icon btn-square rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); width: 80px; height: 80px;">
                                <i class="fas fa-shipping-fast fa-3x text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h5 class="mb-2">Free Shipping</h5>
                                <p class="text-muted">On orders above Rs 50000, fast &amp; secure delivery with no delivery charges.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Item 2 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-4">
                            <div class="featurs-icon btn-square rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); width: 80px; height: 80px;">
                                <i class="fas fa-lock fa-3x text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h5 class="mb-2">Secure Payment</h5>
                                <p class="text-muted">100% safe transactions with modern encryption.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Item 3 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-4">
                            <div class="featurs-icon btn-square rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%); width: 80px; height: 80px;">
                                <i class="fas fa-credit-card fa-3x text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h5 class="mb-2">Buy Now Pay Later</h5>
                                <p class="text-muted">Exclusive credit available for our loyal customers with repeat purchases.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Item 4 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-4">
                            <div class="featurs-icon btn-square rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #ff5f6d 0%, #ffc371 100%); width: 80px; height: 80px;">
                                <i class="fas fa-headset fa-3x text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h5 class="mb-2">24/7 Support</h5>
                                <p class="text-muted">We are here anytime you need help.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Features Section End -->



        <!-- Clothes Shop Start -->
        <div class="container-fluid clothing py-5">
            <div class="container py-5">
                <div class="tab-class text-center">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-4 text-start">
                            <h1>All Clothes</h1>
                        </div>
                        <div class="col-lg-8 text-end">
                            <ul class="nav nav-pills d-inline-flex text-center mb-5" id="categoryTabs">
                                <li class="nav-item">
                                    <a class="d-flex m-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill" href="#tab-1"
                                    style="transition: transform 0.3s, box-shadow 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.15); background-color: #e0f0ff;"
                                    onmouseover="this.style.transform='scale(1.1)';" 
                                    onmouseout="this.style.transform='scale(1)';">
                                        <span class="text-dark" style="width: 130px;">All Products</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="d-flex py-2 m-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-2"
                                    style="transition: transform 0.3s, box-shadow 0.3s;"
                                    onmouseover="this.style.transform='scale(1.1)';" 
                                    onmouseout="this.style.transform='scale(1)';">
                                        <span class="text-dark" style="width: 130px;">Men</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-3"
                                    style="transition: transform 0.3s, box-shadow 0.3s;"
                                    onmouseover="this.style.transform='scale(1.1)';" 
                                    onmouseout="this.style.transform='scale(1)';">
                                        <span class="text-dark" style="width: 130px;">Women</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-4"
                                    style="transition: transform 0.3s, box-shadow 0.3s;"
                                    onmouseover="this.style.transform='scale(1.1)';" 
                                    onmouseout="this.style.transform='scale(1)';">
                                        <span class="text-dark" style="width: 130px;">Babies</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-5"
                                    style="transition: transform 0.3s, box-shadow 0.3s;"
                                    onmouseover="this.style.transform='scale(1.1)';" 
                                    onmouseout="this.style.transform='scale(1)';">
                                        <span class="text-dark" style="width: 130px;">Free Sized</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <script>
                            const tabs = document.querySelectorAll('#categoryTabs a');

                            tabs.forEach(tab => {
                                tab.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    // Remove active styles from all
                                    tabs.forEach(t => {
                                        t.classList.remove('active');
                                        t.style.boxShadow = 'none';
                                        t.style.backgroundColor = 'lightyellow';
                                    });
                                    // Add active styles to clicked
                                    this.classList.add('active');
                                    this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.3)';
                                    this.style.backgroundColor = '#d0eaff';

                                    // Also trigger Bootstrap tab functionality manually if needed:
                                    let tabTrigger = new bootstrap.Tab(this);
                                    tabTrigger.show();
                                });
                            });
                        </script>
                    </div>

                    <div class="tab-content">
                       <!-- All Products Tab -->
                        <div id="tab-1" class="tab-pane fade show p-0 active">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="row g-4">
                                        <?php if (!empty($allProducts)) {
                                            foreach ($allProducts as $row) { ?>
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="rounded position-relative clothing-item">
                                                        <a href="user/product_details.php?id=<?php echo $row['id']; ?>">
                                                            <img src="assets/images/<?php echo $row['image']; ?>" class="img-fluid w-100 rounded-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                                        </a>
                                                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                                            <?php echo htmlspecialchars($row['category_name']); ?>
                                                        </div>
                                                        <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                                                            <div class="d-flex justify-content-between flex-lg-wrap">
                                                                <p class="text-dark fs-5 fw-bold mb-0">Rs <?php echo number_format($row['price'], 2); ?></p>

                                                                <?php if ($row['quantity'] > 0) { ?>
                                                                    <a href="user/add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                        <i class="fa fa-tshirt me-2 text-primary"></i> Add to cart
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <button class="btn border border-secondary rounded-pill px-3 text-danger" disabled>
                                                                        Out of Stock
                                                                    </button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } else {
                                            echo "<p class='text-center'>No products available.</p>";
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Men Tab -->
                        <div id="tab-2" class="tab-pane fade show p-0">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="row g-4">
                                        <?php
                                        $category = 'Men';
                                        if (!empty($productsByCategory[$category])) {
                                            foreach ($productsByCategory[$category] as $row) { ?>
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="rounded position-relative clothing-item tab-click-effect">
                                                        <a href="user/product_details.php?id=<?php echo $row['id']; ?>">
                                                            <img src="assets/images/<?php echo $row['image']; ?>" class="img-fluid w-100 rounded-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                                        </a>
                                                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                                            <?php echo htmlspecialchars($row['category_name']); ?>
                                                        </div>
                                                        <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                                                            <div class="d-flex justify-content-between flex-lg-wrap">
                                                                <p class="text-dark fs-5 fw-bold mb-0">Rs <?php echo number_format($row['price'], 2); ?></p>

                                                                <?php if ($row['quantity'] > 0) { ?>
                                                                    <a href="user/add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                        <i class="fa fa-tshirt me-2 text-primary"></i> Add to cart
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <button class="btn border border-secondary rounded-pill px-3 text-danger" disabled>
                                                                        Out of Stock
                                                                    </button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } else {
                                            echo "<p class='text-center'>No product found in this category.</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                       <!-- Women Tab -->
                        <div id="tab-3" class="tab-pane fade show p-0">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="row g-4">
                                        <?php
                                        $category = 'Women';
                                        if (!empty($productsByCategory[$category])) {
                                            foreach ($productsByCategory[$category] as $row) { ?>
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="rounded position-relative clothing-item tab-click-effect">
                                                        <a href="user/product_details.php?id=<?php echo $row['id']; ?>">
                                                            <img src="assets/images/<?php echo $row['image']; ?>" class="img-fluid w-100 rounded-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                                        </a>
                                                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                                            <?php echo htmlspecialchars($row['category_name']); ?>
                                                        </div>
                                                        <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                                                            <div class="d-flex justify-content-between flex-lg-wrap">
                                                                <p class="text-dark fs-5 fw-bold mb-0">Rs <?php echo number_format($row['price'], 2); ?></p>

                                                                <?php if ($row['quantity'] > 0) { ?>
                                                                    <a href="user/add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                        <i class="fa fa-tshirt me-2 text-primary"></i> Add to cart
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <button class="btn border border-secondary rounded-pill px-3 text-danger" disabled>
                                                                        Out of Stock
                                                                    </button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } else {
                                            echo "<p class='text-center'>No product found in this category.</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Babies Tab -->
                        <div id="tab-4" class="tab-pane fade show p-0">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="row g-4">
                                        <?php
                                        $category = 'Babies';
                                        if (!empty($productsByCategory[$category])) {
                                            foreach ($productsByCategory[$category] as $row) { ?>
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="rounded position-relative clothing-item tab-click-effect">
                                                        <a href="user/product_details.php?id=<?php echo $row['id']; ?>">
                                                            <img src="assets/images/<?php echo $row['image']; ?>" class="img-fluid w-100 rounded-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                                        </a>
                                                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                                            <?php echo htmlspecialchars($row['category_name']); ?>
                                                        </div>
                                                        <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                                                            <div class="d-flex justify-content-between flex-lg-wrap">
                                                                <p class="text-dark fs-5 fw-bold mb-0">Rs <?php echo number_format($row['price'], 2); ?></p>

                                                                <?php if ($row['quantity'] > 0) { ?>
                                                                    <a href="user/add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                        <i class="fa fa-tshirt me-2 text-primary"></i> Add to cart
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <button class="btn border border-secondary rounded-pill px-3 text-danger" disabled>
                                                                        Out of Stock
                                                                    </button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } else {
                                            echo "<p class='text-center'>No products found in this category.</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Free Sized Tab -->
                        <div id="tab-5" class="tab-pane fade show p-0">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="row g-4">
                                        <?php
                                        $category = 'Free Sized';
                                        if (!empty($productsByCategory[$category])) {
                                            foreach ($productsByCategory[$category] as $row) { ?>
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="rounded position-relative clothing-item tab-click-effect">
                                                        <a href="user/product_details.php?id=<?php echo $row['id']; ?>">
                                                            <img src="assets/images/<?php echo $row['image']; ?>" class="img-fluid w-100 rounded-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                                        </a>
                                                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                                            <?php echo htmlspecialchars($row['category_name']); ?>
                                                        </div>
                                                        <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                                                            <div class="d-flex justify-content-between flex-lg-wrap">
                                                                <p class="text-dark fs-5 fw-bold mb-0">Rs <?php echo number_format($row['price'], 2); ?></p>

                                                                <?php if ($row['quantity'] > 0) { ?>
                                                                    <a href="user/add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                        <i class="fa fa-tshirt me-2 text-primary"></i> Add to cart
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <button class="btn border border-secondary rounded-pill px-3 text-danger" disabled>
                                                                        Out of Stock
                                                                    </button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } else {
                                            echo "<p class='text-center'>No products found in this category.</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <!-- Clothes Shop End -->



        <!-- Features Start -->
        <div class="container-fluid service py-5" style="background-color: #f8f9fa; padding: 50px 0;">
            <style>
                .service-item:hover {
                    transform: scale(1.05);
                    border-color: green !important;
                    box-shadow: 0 8px 16px rgba(0, 128, 0, 0.4);
                }

                .service-item {
                    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out, border-color 0.3s ease-in-out;
                }
            </style>

            <div class="container py-5">
                <div class="row g-4 justify-content-center">

                    <!-- Trendy T-Shirt -->
                    <div class="col-md-6 col-lg-4">
                        <a href="#" style="text-decoration: none;">
                            <div class="service-item" style="background-color: #6c757d; border: 2px solid #6c757d; border-radius: 10px;">
                                <div class="icon-wrapper text-center py-5">
                                    <i class="fa-solid fa-shirt fa-6x text-white"></i>
                                </div>
                                <div class="px-4 rounded-bottom">
                                    <div class="service-content text-center p-4 rounded" style="background-color: #007bff; border-radius: 10px;">
                                        <h6 class="text-white" style="margin: 0;">Trendy T-Shirt</h6>
                                        <h4 style="color: #ffc107; margin: 5px 0 0;">Flat 25% OFF</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Fashionable Dresses -->
                    <div class="col-md-6 col-lg-4">
                        <a href="#" style="text-decoration: none;">
                            <div class="service-item" style="background-color: #343a40; border: 2px solid #343a40; border-radius: 10px;">
                                <div class="icon-wrapper text-center py-5">
                                    <i class="fa-solid fa-female fa-6x text-primary"></i>
                                </div>
                                <div class="px-4 rounded-bottom">
                                    <div class="service-content text-center p-4 rounded" style="background-color: #f8f9fa; border-radius: 10px;">
                                        <h6 class="text-primary" style="margin: 0;">Fashionable Dresses</h6>
                                        <h5 style="color: #28a745; margin: 5px 0 0;">Free Delivery on Orders Over Rs 30000</h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Premium Shoes -->
                    <div class="col-md-6 col-lg-4">
                        <a href="#" style="text-decoration: none;">
                            <div class="service-item" style="background-color: #007bff; border: 2px solid #007bff; border-radius: 10px;">
                                <div class="icon-wrapper text-center py-5">
                                    <i class="fa-solid fa-shoe-prints fa-6x text-white"></i>
                                </div>
                                <div class="px-4 rounded-bottom">
                                    <div class="service-content text-center p-4 rounded" style="background-color: #6c757d; border-radius: 10px;">
                                        <h6 class="text-white" style="margin: 0;">Premium Shoes</h6>
                                        <h5 style="color: #ffc107; margin: 5px 0 0;">Elegant Style & Flat 10% OFF</h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
        <!-- Features End -->



        <!-- Bestseller Products Start -->
        
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                    <h1 class="display-4">Bestseller Products</h1>
                    <p>Our Top Picks Just For You</p>
                </div>
                <div class="row g-4">

                    <!-- Start Product -->
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="clothing-item p-4 rounded bg-light h-100 d-flex flex-column">
                            <div class="text-center mb-3">
                                <img src="assets/images/black gown.jpeg" alt="Wedding Gown (Ball Gown)" class="img-fluid">
                            </div>
                            <h5 class="text-center mb-2"><a href="#" class="text-decoration-none text-dark">Wedding Gown (Ball Gown)</a></h5>
                            <div class="d-flex justify-content-center mb-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star-half-alt text-primary"></i>
                            </div>
                            <h4 class="text-center text-primary mb-3">Rs 9000</h4>
                        </div>
                    </div>
                    <!-- End Product -->

                    <!-- Start Product -->
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="clothing-item p-4 rounded bg-light h-100 d-flex flex-column">
                            <div class="text-center mb-3">
                                <img src="assets/images/babies combo set.jpg" alt="Kids Outfit Set" class="img-fluid">
                            </div>
                            <h5 class="text-center mb-2"><a href="#" class="text-decoration-none text-dark">Kids Outfit Set</a></h5>
                            <div class="d-flex justify-content-center mb-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                            </div>
                            <h4 class="text-center text-primary mb-3">Rs 7000</h4>
                        </div>
                    </div>
                    <!-- End Product -->

                    <!-- Start Product -->
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="clothing-item p-4 rounded bg-light h-100 d-flex flex-column">
                            <div class="text-center mb-3">
                                <img src="assets/images/shoes2.jpg" alt="Nike Air Force" class="img-fluid">
                            </div>
                            <h5 class="text-center mb-2"><a href="#" class="text-decoration-none text-dark">Nike Air Force</a></h5>
                            <div class="d-flex justify-content-center mb-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                            </div>
                            <h4 class="text-center text-primary mb-3">Rs 7500</h4>
                        </div>
                    </div>
                    <!-- End Product -->

                    <!-- Start Product -->
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="clothing-item p-4 rounded bg-light h-100 d-flex flex-column">
                            <div class="text-center mb-3">
                                <img src="assets/images/unisex hiphop tshirt.jpg" alt="Hip hop T-Shirt" class="img-fluid">
                            </div>
                            <h5 class="text-center mb-2"><a href="#" class="text-decoration-none text-dark">Hip hop T-Shirt</a></h5>
                            <div class="d-flex justify-content-center mb-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star-half-alt text-primary"></i>
                            </div>
                            <h4 class="text-center text-primary mb-3">Rs 2000</h4>
                        </div>
                    </div>
                    <!-- End Product -->

                    <!-- Start Product -->
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="clothing-item p-4 rounded bg-light h-100 d-flex flex-column">
                            <div class="text-center mb-3">
                                <img src="assets/images/Kurta set.jpg" alt="Kurta set" class="img-fluid">
                            </div>
                            <h5 class="text-center mb-2"><a href="#" class="text-decoration-none text-dark">Kurta Set</a></h5>
                            <div class="d-flex justify-content-center mb-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star-half-alt text-primary"></i>
                            </div>
                            <h4 class="text-center text-primary mb-3">Rs 2000</h4>
                        </div>
                    </div>
                    <!-- End Product -->

                    <!-- Start Product -->
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="clothing-item p-4 rounded bg-light h-100 d-flex flex-column">
                            <div class="text-center mb-3">
                                <img src="assets/images/High Heels Boots.jpeg" alt="Block Heel Boots" class="img-fluid">
                            </div>
                            <h5 class="text-center mb-2"><a href="#" class="text-decoration-none text-dark">Block Heel Boots</a></h5>
                            <div class="d-flex justify-content-center mb-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star-half-alt text-primary"></i>
                            </div>
                            <h4 class="text-center text-primary mb-3">Rs 2000</h4>
                        </div>
                    </div>
                    <!-- End Product -->

                    <!-- Start Product -->
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="clothing-item p-4 rounded bg-light h-100 d-flex flex-column">
                            <div class="text-center mb-3">
                                <img src="assets/images/ladies sandle.jpg" alt="Strap Sandals" class="img-fluid">
                            </div>
                            <h5 class="text-center mb-2"><a href="#" class="text-decoration-none text-dark">Strap Sandals</a></h5>
                            <div class="d-flex justify-content-center mb-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star-half-alt text-primary"></i>
                            </div>
                            <h4 class="text-center text-primary mb-3">Rs 2500</h4>
                        </div>
                    </div>
                    <!-- End Product -->

                    <!-- Start Product -->
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="clothing-item p-4 rounded bg-light h-100 d-flex flex-column">
                            <div class="text-center mb-3">
                                <img src="assets/images/classic white t-shirt.jpg" alt="Classic White T-Shirt" class="img-fluid">
                            </div>
                            <h5 class="text-center mb-2"><a href="#" class="text-decoration-none text-dark">Classic White T-Shirt</a></h5>
                            <div class="d-flex justify-content-center mb-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star-half-alt text-primary"></i>
                                <i class="fas fa-star-half-alt text-primary"></i>
                            </div>
                            <h4 class="text-center text-primary mb-3">Rs 3500</h4>
                        </div>
                    </div>
                    <!-- End Product -->

                </div>
            </div>
        </div>
        <!-- Bestseller Products End -->


        <!-- New Products Section -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <h1 class="mb-0">New Products</h1>
                <div class="row g-4 mt-4">

                    <?php if (!empty($newProducts)) {
                        foreach ($newProducts as $product) { ?>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="rounded position-relative clothing-item">

                                    <!-- Product Image -->
                                    <a href="user/product_details.php?id=<?php echo $product['id']; ?>">
                                        <img src="assets/images/<?php echo $product['image']; ?>" 
                                            class="img-fluid w-100 rounded-top" 
                                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </a>

                                    <!-- Category Name -->
                                    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" 
                                        style="top: 10px; left: 10px;">
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                    </div>

                                    <!-- New Badge -->
                                    <div class="text-white bg-danger px-3 py-1 rounded position-absolute" 
                                        style="top: 10px; right: 10px;">
                                        New
                                    </div>

                                    <!-- Product Details -->
                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                        <!-- Name -->
                                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>

                                        <!-- Description -->
                                        <p><?php echo htmlspecialchars($product['description']); ?></p>

                                        <!-- Price and Add to Cart -->
                                        <div class="d-flex justify-content-between flex-lg-wrap">
                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                Rs <?php echo number_format($product['price'], 2); ?>
                                            </p>

                                            <?php if ($product['quantity'] > 0) { ?>
                                                <!-- Add to Cart Button Active -->
                                                <a href="user/add_to_cart.php?id=<?php echo $product['id']; ?>" 
                                                class="btn border border-secondary rounded-pill px-3 text-primary">
                                                    <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                </a>
                                            <?php } else { ?>
                                                <!-- Disabled Button when Out of Stock -->
                                                <button class="btn border border-secondary rounded-pill px-3 text-danger" disabled>
                                                    Out of Stock
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php }
                    } else {
                        echo "<p class='text-center'>No new products available.</p>";
                    } ?>

                </div>
            </div>
        </div>
        <!-- New Products Section End -->


        <!-- Fact Section Start -->
        <style>
            .counter {
                transition: transform 0.3s ease, border 0.3s ease, color 0.3s ease;
                cursor: pointer;
                background-color: #fff;
                border-radius: 0.375rem; /* rounded */
                padding: 3rem 2rem;
                text-align: center;
                color: #212529;
                border: 2px solid transparent;
            }

            .counter:hover {
                transform: scale(1.05);
                border: 2px solid #198754; /* Bootstrap success green border */
                color: #198754 !important;
            }

            .counter i {
                font-size: 3rem;
                margin-bottom: 1rem;
                color: #6c757d;
                transition: color 0.3s ease;
            }

            .counter:hover i {
                color: #198754 !important; /* Icon turns green on hover */
            }

            .counter h4 {
                font-weight: 600;
                font-size: 1.25rem;
                margin-bottom: 0.5rem;
            }

            .counter h1 {
                font-weight: 700;
                font-size: 2.5rem;
                margin: 0;
            }
        </style>

        <div class="container-fluid py-5">
            <div class="container">
                <div class="bg-light p-5 rounded">
                    <div class="row g-4 justify-content-center">
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter">
                                <i class="fa fa-users fa-3x text-secondary mb-3"></i>
                                <h4>Satisfied Customers</h4>
                                <h1>1963</h1>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter">
                                <i class="fa fa-thumbs-up fa-3x text-secondary mb-3"></i>
                                <h4>Quality of Service</h4>
                                <h1>99%</h1>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter">
                                <i class="fa fa-certificate fa-3x text-secondary mb-3"></i>
                                <h4>Quality Certificates</h4>
                                <h1>33</h1>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter">
                                <i class="fa fa-box fa-3x text-secondary mb-3"></i>
                                <h4>Available Products</h4>
                                <h1><?php echo count($allProducts); ?></h1> <!-- Dynamic Count -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fact Section End -->



        <!-- Testimonial Start -->
        <div class="container-fluid testimonial py-5">
            <div class="container py-5">
                <div class="testimonial-header text-center">
                    <h4 class="text-primary">Our Testimonial</h4>
                    <h1 class="display-5 mb-5 text-dark">What Our Clients Are Saying!</h1>
                </div>
                <div class="owl-carousel testimonial-carousel">
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute" style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">The E-Clothing Store has completely transformed my shopping experience. The product quality is outstanding and the customer service is top-notch!</p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="design-assets/img/misspabi.jpeg" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">Miss Pabi</h4>
                                    <p class="m-0 pb-3">Fashion Blogger</p>
                                    <div class="d-flex pe-5">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute" style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">I love how easy it is to find trendy clothes on the E-Clothing Store. The prices are fair and delivery is always quick and reliable!</p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="design-assets/img/kabendra.jpeg" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">Kabindra Khadka</h4>
                                    <p class="m-0 pb-3">Entrepreneur</p>
                                    <div class="d-flex pe-5">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute" style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">E-Clothing Store always offers the latest styles and great deals. I am very satisfied with their service and highly recommend them!</p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="design-assets/img/mukesh.jpeg" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">Mukesh Shahu</h4>
                                    <p class="m-0 pb-3">Student</p>
                                    <div class="d-flex pe-5">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Testimonial End -->

        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
            <div class="container py-5">
                <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5);">
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <a href="#">
                                <h1 class="text-primary mb-0">E-Clothing Store</h1>
                                <p class="text-secondary mb-0">Quality & Stylish Wear</p>
                            </a>
                        </div>
                        <div class="col-lg-6">
                            <div class="position-relative mx-auto">
                                <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="email" placeholder="Your Email">
                                <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">Subscribe Now</button>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="d-flex justify-content-end pt-3">
                                    <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="https://www.facebook.com/subash.chand.202853"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-outline-secondary btn-md-square rounded-circle" href="mailto: subashchan31@gmail.com"><i class="fa fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Why People Love Us!</h4>
                            <p class="mb-4">At E-Clothing Store, we provide high-quality, trendy, and affordable clothing with excellent customer service. Experience the best online shopping from Dhangadhi.</p>
                            <a href="#" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Shop Info</h4>
                            <a class="btn-link" href="">About Us</a>
                            <a class="btn-link" href="">Contact Us</a>
                            <a class="btn-link" href="">Privacy Policy</a>
                            <a class="btn-link" href="">Terms & Conditions</a>
                            <a class="btn-link" href="">Return Policy</a>
                            <a class="btn-link" href="">FAQs & Help</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Account</h4>
                            <a class="btn-link" href="">My Account</a>
                            <a class="btn-link" href="">Shop Details</a>
                            <a class="btn-link" href="">Shopping Cart</a>
                            <a class="btn-link" href="">Wishlist</a>
                            <a class="btn-link" href="">Order History</a>
                            <a class="btn-link" href="">International Orders</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Contact</h4>
                            <p>Address: Dhangadhi, Kailali, Nepal</p>
                            <p>Email: eclothingstore@gmail.com</p>
                            <p>Phone: +977 9844351869</p>
                            <p>SMS: +977 9806463417</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        <!-- Copyright Start -->
        <div class="container-fluid copyright bg-dark py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>E-CLothing Store</a>, All right reserved.</span>
                    </div>
                    <div class="col-md-6 my-auto text-center text-md-end text-white">
                        Designed By <a class="border-bottom" href="#">DLMS Group</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->



        <!-- Back to Top -->
        <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
        <!-- JavaScript Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="design-assets/lib/easing/easing.min.js"></script>
        <script src="design-assets/lib/waypoints/waypoints.min.js"></script>
        <script src="design-assets/lib/lightbox/js/lightbox.min.js"></script>
        <script src="design-assets/lib/owlcarousel/owl.carousel.min.js"></script>

        <!-- Template Javascript -->
        <script src="design-assets/js/main.js"></script>
    </body>
</html>