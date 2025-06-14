<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if user is not logged in
    header("Location: Userlogin.php?redirect=checkout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>E Clothing Store</title>
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

        <!-- Libraries Stylesheet -->
        <link href="../design-assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
        <link href="../design-assets/lib/lightbox/css/lightbox.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="../design-assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
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
                            <!-- Show this link only when the user is logged in -->
                                <a href="myorders.php" class="nav-item nav-link">My Orders</a>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <a href="Userlogin.php" class="btn btn-outline-dark">Login</a>
                            <?php else: ?>
                                <span class="text-dark fw-bold">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></span>
                                <a href="logout.php" class="btn btn-danger">Logout</a>
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


        <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Checkout</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item active text-white">Checkout</li>
            </ol>
        </div>
        <!-- Single Page Header End -->

        <!-- Checkout Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <h1 class="mb-4">Billing Details</h1>
                <form action="order.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-5">
                        <!-- Billing Details Form -->
                        <div class="col-md-12 col-lg-6 col-xl-7">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3" for="first_name">First Name<sup>*</sup></label>
                                        <input type="text" id="first_name" name="first_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3" for="last_name">Last Name<sup>*</sup></label>
                                        <input type="text" id="last_name" name="last_name" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-item">
                                <label class="form-label my-3" for="billing_address">Billing Address<sup>*</sup></label>
                                <input type="text" id="billing_address" name="billing_address" class="form-control" required>
                            </div>

                            <div class="form-item">
                                <label class="form-label my-3" for="shipping_address">Shipping Address<sup>*</sup></label>
                                <input type="text" id="shipping_address" name="shipping_address" class="form-control" required>
                            </div>

                            <div class="form-item">
                                <label class="form-label my-3" for="country">Country<sup>*</sup></label>
                                <input type="text" id="country" name="country" class="form-control" required>
                            </div>

                            <div class="form-item">
                                <label class="form-label my-3" for="mobile">Phone Number<sup>*</sup></label>
                                <input type="tel" id="mobile" name="mobile" class="form-control" required>
                            </div>

                            <div class="form-item">
                                <label class="form-label my-3" for="email">Email Address<sup>*</sup></label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>

                            <hr>
                        </div>

                        <!-- Order Summary -->
                        <div class="col-md-12 col-lg-6 col-xl-5">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Product</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])):
                                            $grandTotal = 0;
                                            foreach ($_SESSION['cart'] as $item):
                                                $itemTotal = $item['price'] * $item['quantity'];
                                                $grandTotal += $itemTotal;
                                        ?>
                                        <tr>
                                            <th scope="row" class="align-middle">
                                                <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" class="img-fluid rounded-circle" style="width: 90px; height: 90px;" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            </th>
                                            <td class="py-5 align-middle"><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td class="py-5 align-middle">Rs <?php echo number_format($item['price'], 2); ?></td>
                                            <td class="py-5 align-middle"><?php echo (int)$item['quantity']; ?></td>
                                            <td class="py-5 align-middle">Rs <?php echo number_format($itemTotal, 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>

                                        <!-- Subtotal -->
                                        <tr>
                                            <td colspan="3"></td>
                                            <td class="py-5">
                                                <p class="mb-0 text-dark py-3">Subtotal: </p>
                                            </td>
                                            <td class="py-5">
                                                <div class="py-3 border-bottom border-top">
                                                    <p class="mb-0 text-dark" id="subtotal">Rs <?php echo number_format($grandTotal, 2); ?></p>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Shipping Options -->
                                        <tr>
                                            <td colspan="2" class="py-5">
                                                <p class="mb-0 text-dark py-4">Shipping</p>
                                            </td>
                                            <td colspan="3" class="py-5">
                                                <div class="form-check text-start mb-2">
                                                    <input type="radio" class="form-check-input bg-primary border-0" name="shipping_charge" id="Shipping-500" value="500" required onchange="updateGrandTotal(500)">
                                                    <label class="form-check-label" for="Shipping-500">Flat rate: Rs 500</label>
                                                </div>
                                                <div class="form-check text-start">
                                                    <input type="radio" class="form-check-input bg-primary border-0" name="shipping_charge" id="Shipping-300" value="300" required onchange="updateGrandTotal(300)">
                                                    <label class="form-check-label" for="Shipping-300">Local Pickup: Rs 300</label>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Grand Total -->
                                        <tr>
                                            <td colspan="3"></td>
                                            <td class="py-5">
                                                <p class="mb-0 text-dark text-uppercase py-3">Grand Total: </p>
                                            </td>
                                            <td class="py-5">
                                                <div class="py-3 border-bottom border-top">
                                                    <p class="mb-0 text-dark fw-bold" id="grand-total">Rs <?php echo number_format($grandTotal, 2); ?></p>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                                <?php endif; ?>
                            </div>

                            <!-- Payment Method -->
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="radio" class="form-check-input bg-primary border-0" id="PaymentCOD" name="payment_method" value="Cash on Delivery" required>
                                        <label class="form-check-label" for="PaymentCOD">Cash On Delivery</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Place Order Button -->
                            <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                                <button type="submit" class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">Place Order</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Checkout Page End -->

        <!-- JavaScript to update Grand Total -->
        <script>
            let baseTotal = <?php echo $grandTotal; ?>;

            function updateGrandTotal(shippingCharge) {
                let grandTotal = baseTotal + shippingCharge;
                document.getElementById('grand-total').innerText = 'Rs ' + grandTotal.toFixed(2);
            }
        </script>




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
        <script src="../design-assets/lib/easing/easing.min.js"></script>
        <script src="../design-assets/lib/waypoints/waypoints.min.js"></script>
        <script src="../design-assets/lib/lightbox/js/lightbox.min.js"></script>
        <script src="../design-assets/lib/owlcarousel/owl.carousel.min.js"></script>

        <!-- Template Javascript -->
        <script src="../design-assets/js/main.js"></script>
        <script>
            document.querySelectorAll('input[name="shipping"]').forEach(function(input) {
                input.addEventListener('change', function() {
                    const shippingCost = parseFloat(this.value);
                    const baseTotal = <?php echo $grandTotal; ?>;
                    const finalTotal = (baseTotal + shippingCost).toFixed(2);
                    document.getElementById('grand-total').innerText = '$' + finalTotal;
                });
            });
        </script>
    </body>
</html>