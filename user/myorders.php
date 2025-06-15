<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Userlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the logged-in user
$orderQuery = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$orderResult = mysqli_query($con, $orderQuery);
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
        <link href="../design-assets/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="../design-assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="../design-assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="../design-assets/css/style.css" rel="stylesheet">


        <style>
            body {
                background-color: #f9fbff;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                padding: 40px 20px;
            }

            h1 {
                font-weight: 700;
                color: #334155;
                margin-bottom: 2rem;
                text-align: center;
            }

            .order-card {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgb(100 116 139 / 0.1);
                margin-bottom: 2rem;
                overflow: hidden;
                transition: box-shadow 0.3s ease;
            }

            .order-card:hover {
                box-shadow: 0 20px 40px rgb(100 116 139 / 0.15);
            }

            .order-header {
                background: linear-gradient(135deg, #4f46e5, #3b82f6);
                color: white;
                padding: 20px 30px;
                font-weight: 600;
                font-size: 1.1rem;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                cursor: pointer;
                user-select: none;
                border: none;
                width: 100%;
                text-align: left;
                border-radius: 12px 12px 0 0;
            }

            .order-info {
                display: flex;
                gap: 1rem;
                align-items: center;
                flex-wrap: wrap;
            }

            .order-header::after {
                content: '▼';
                font-size: 1.2rem;
                margin-left: auto;
                transition: transform 0.3s ease;
            }

            .order-header[aria-expanded="true"]::after {
                content: '▲';
            }

            .badge-status {
                padding: 6px 16px;
                border-radius: 9999px;
                font-weight: 600;
                font-size: 0.9rem;
                box-shadow: 0 2px 8px rgb(0 0 0 / 0.12);
                user-select: none;
            }

            .badge-status.pending {
                background-color: #facc15;
                color: #78350f;
            }

            .badge-status.completed {
                background-color: #22c55e;
                color: #14532d;
            }

            .order-body {
                padding: 25px 30px;
                background: #fefefe;
                border-top: 3px solid #4f46e5;
                border-radius: 0 0 12px 12px;
            }

            .order-table-wrapper {
                max-height: 300px;
                overflow-y: auto;
                margin-bottom: 20px;
                border-radius: 10px;
                box-shadow: inset 0 0 10px rgb(0 0 0 / 0.05);
            }

            table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0 12px;
            }

            thead tr {
                background: #4f46e5;
                color: white;
                text-transform: uppercase;
                font-size: 0.85rem;
                font-weight: 700;
                border-radius: 12px;
            }

            thead th {
                padding: 14px 12px;
                position: sticky;
                top: 0;
                background: #4f46e5;
                z-index: 1;
            }

            tbody tr {
                background: #eef2ff;
                box-shadow: 0 4px 8px rgb(79 70 229 / 0.1);
                transition: background-color 0.3s ease;
                border-radius: 10px;
            }

            tbody tr:hover {
                background-color: #dbeafe;
            }

            tbody td {
                padding: 14px 12px;
                vertical-align: middle;
                font-weight: 600;
                color: #334155;
                border: none;
            }

            tbody td img {
                border-radius: 10px;
                box-shadow: 0 3px 10px rgb(0 0 0 / 0.15);
                width: 70px;
                height: 70px;
                object-fit: cover;
            }

            .summary-container {
                margin-top: 25px;
                background: #e0e7ff;
                border-radius: 12px;
                padding: 20px 30px;
                display: flex;
                justify-content: flex-end;
                gap: 40px;
                font-weight: 700;
                font-size: 1.15rem;
                color: #3730a3;
                box-shadow: 0 8px 20px rgb(99 102 241 / 0.2);
                user-select: none;
            }

            .summary-item {
                background: #4338ca;
                color: white;
                padding: 14px 30px;
                border-radius: 50px;
                box-shadow: 0 5px 20px rgb(67 56 202 / 0.5);
                transition: background-color 0.3s ease;
            }

            .summary-item:hover {
                background-color: #3730a3;
            }

            @media (max-width: 576px) {
                .order-header {
                    flex-direction: column;
                    gap: 10px;
                    font-size: 1rem;
                }

                .summary-container {
                    flex-direction: column;
                    align-items: flex-end;
                    gap: 15px;
                    font-size: 1.05rem;
                }

                .order-info {
                    flex-direction: column;
                    gap: 6px;
                }
            }

            /* Centering the order details */
            .order-details {
                text-align: center;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>

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
                            <a href="#" class="my-auto"><i class="fas fa-user fa-2x"></i>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Navbar End -->
        
        <!-- Order table start -->
        <div class="container">
            <h1>My Orders</h1>

            <div class="order-details">
                <h2>Order Details</h2>
            </div>

            <?php if (mysqli_num_rows($orderResult) > 0): ?>
                <?php $count = 1; ?>
                <?php while ($order = mysqli_fetch_assoc($orderResult)): ?>
                    <div class="order-card">
                        <button
                            class="order-header"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#orderDetails<?= $count ?>"
                            aria-expanded="false"
                            aria-controls="orderDetails<?= $count ?>"
                        >
                            <div class="order-info">
                                <span>Order ID: <strong><?= $order['id'] ?></strong></span>
                                <span>Date: <strong><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></strong></span>
                                <span class="badge-status <?= strtolower($order['order_status']) === 'pending' ? 'pending' : 'completed' ?>">
                                    <?= htmlspecialchars($order['order_status']) ?>
                                </span>
                            </div>
                        </button>

                        <div class="collapse order-body" id="orderDetails<?= $count ?>">
                            <?php
                            $order_id = $order['id'];

                            $detailsQuery = "SELECT od.*, p.name, p.image FROM orderdetail od
                                            JOIN product p ON od.product_id = p.id
                                            WHERE od.order_id = '$order_id'";
                            $detailsResult = mysqli_query($con, $detailsQuery);
                            ?>

                            <div class="order-table-wrapper">
                                <table>
                                    <thead>
                                        <tr>
                                            <th scope="col">Product</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Unit Price</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $subtotal = 0;
                                        $totalShipping = 0;
                                        while ($detail = mysqli_fetch_assoc($detailsResult)):
                                            $itemTotal = $detail['unit_price'] * $detail['quantity'];
                                            $subtotal += $itemTotal;
                                            $totalShipping += $detail['shipping_charge'];
                                        ?>
                                            <tr>
                                                <td><img src="../assets/images/<?= htmlspecialchars($detail['image']) ?>" alt="<?= htmlspecialchars($detail['name']) ?>" /></td>
                                                <td><?= htmlspecialchars($detail['name']) ?></td>
                                                <td>Rs <?= number_format($detail['unit_price'], 2) ?></td>
                                                <td><?= (int)$detail['quantity'] ?></td>
                                                <td>Rs <?= number_format($itemTotal, 2) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="summary-container" role="region" aria-label="Order Summary">
                                <div class="summary-item" tabindex="0">Shipping Charge: Rs <?= number_format($totalShipping, 2) ?></div>
                                <div class="summary-item" tabindex="0">Grand Total: Rs <?= number_format($subtotal + $totalShipping, 2) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php $count++; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center my-5">
                    <h3 class="text-muted">😞 You have no orders yet.</h3>
                    <p>Start shopping now to place your first order!</p>
                    <a href="../index.php" class="btn btn-success btn-lg mt-3">Shop Now</a>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="../index.php" class="btn btn-primary btn-lg px-5">Continue Shopping</a>
            </div>
        </div>
        <!-- Order table end -->

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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
