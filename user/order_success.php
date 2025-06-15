<?php
session_start();
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Libraries Stylesheet -->
    <link href="../design-assets/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="../design-assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../design-assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../design-assets/css/style.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #fff; /* match navbar white */
            font-family: 'Arial', sans-serif;
            color: #212529; /* dark text */
            min-height: 100vh;
        }

        /* Container centers vertically & horizontally, large box */
        .success-container-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 100px;
            min-height: calc(100vh - 100px);
            background: #f8f9fa; /* very light gray background */
        }

        /* Big box with white bg, subtle shadow */
        .success-container {
            text-align: center;
            background: #fff; /* white background */
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            color: #212529;
            max-width: 700px;
            width: 90%;
            animation: fadeIn 0.8s ease-in-out;
        }

        .success-container i {
            font-size: 80px;
            color: #28a745; /* green success */
            margin-bottom: 20px;
        }

        .success-container h1 {
            color: #28a745; /* green success */
            margin-bottom: 20px;
            font-size: 36px;
            font-weight: 700;
        }

        .success-container p {
            font-size: 20px;
            margin-bottom: 35px;
            font-weight: 500;
            line-height: 1.4;
            color: #212529;
        }

        .success-container a {
            display: inline-block;
            margin: 0 15px;
            padding: 14px 28px;
            text-decoration: none;
            background-color: #007bff; /* bootstrap primary blue */
            color: white;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.2s;
            font-size: 18px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.5);
        }

        .success-container a:hover {
            background-color: #0056b3;
            transform: scale(1.08);
            box-shadow: 0 6px 15px rgba(0, 86, 179, 0.7);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

    <!-- Navbar Start -->
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
                <a href="../index.php" class="navbar-brand"><h2 class="text-primary display-6">E-Clothing Store</h2></a>
                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>

                <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                    <div class="navbar-nav mx-auto">
                        <a href="../index.php" class="nav-item nav-link active">Home</a>
                        <a href="#" class="nav-item nav-link">Shop</a>
                        <a href="#" class="nav-item nav-link">Contact</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="myorders.php" class="nav-item nav-link">My Orders</a>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="User login.php" class="btn btn-outline-dark">Login</a>
                        <?php else: ?>
                            <span class="text-dark fw-bold">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></span>
                            <a href="logout.php" class="btn btn-danger">Logout</a>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex m-3 me-0">
                        <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="fas fa-search text-primary"></i>
                        </button>
                        <a href="cart.php" class="position-relative me-4 my-auto">
                            <i class="fa fa-shopping-bag fa-2x"></i>
                            <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                                style="top: -5px; left: 15px; height: 20px; min-width: 20px;">
                                <?php echo isset($cartCount) ? $cartCount : 0; ?>
                            </span>
                        </a>
                        <a href="#" class="my-auto"><i class="fas fa-user fa-2x"></i></a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Success Message Start -->
    <div class="success-container-wrapper">
        <div class="success-container">
            <i class="fas fa-check-circle mb-3 animate__animated animate__bounceIn"></i>
            <h1 class="animate__animated animate__fadeInDown">Thank you! 🎉</h1>
            <p class="animate__animated animate__fadeInUp">Your order has been placed successfully.</p>
            <div class="mt-4">
                <a href="../index.php" class="animate__animated animate__fadeInLeft">Continue Shopping</a>
                <a href="myorders.php" class="animate__animated animate__fadeInRight">View My Orders</a>
            </div>
        </div>
    </div>
    <!-- Success Message End -->

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

    <!-- Copyright -->
    <div class="container-fluid copyright bg-dark py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>E-Clothing Store</a>, All rights reserved.</span>
                </div>
                <div class="col-md-6 my-auto text-center text-md-end text-white">
                    Designed By <a class="border-bottom" href="#">DLMS Group</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
