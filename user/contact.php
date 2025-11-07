<?php
session_start();

$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

/* ---------- Store Settings ---------- */
$storeName    = "E-Clothing Store";
$storeAddress = "Dhangadhi, Kailali Nepal";
$storeEmail   = "eclothingstore@dlms.dev.np";
$storeContact = "+9779844351869";
$storeLogo    = "";

$sql = "SELECT * FROM store_settings LIMIT 1";
$res = mysqli_query($con, $sql);
if ($row = mysqli_fetch_assoc($res)) {
    $storeName    = $row['store_name'] ?: $storeName;
    $storeAddress = $row['store_address'] ?: $storeAddress;
    $storeEmail   = $row['store_email'] ?: $storeEmail;
    $storeContact = $row['contact_number'] ?: $storeContact;
    $storeLogo    = $row['store_logo'] ?: $storeLogo;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?php echo htmlspecialchars($storeName); ?> — Contact</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Fonts / Icons / Bootstrap -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="../design-assets/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Internal CSS -->
  <style>
    :root{
      --bs-primary:#0d6efd;
      --bs-secondary:#ffc107;
      --bs-white:#ffffff;
      --bs-light:#f8f9fa;
      --bs-dark:#212529;
    }
    body{ font-family:"Open Sans", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial; }

    /*** Spinner Start ***/
    #spinner {
      opacity: 0;
      visibility: hidden;
      transition: opacity .8s ease-out, visibility 0s linear .5s;
      z-index: 99999;
    }
    #spinner.show {
      transition: opacity .8s ease-out, visibility 0s linear .0s;
      visibility: visible;
      opacity: 1;
    }
    .back-to-top {
      position: fixed;
      right: 30px;
      bottom: 30px;
      display: flex;
      width: 45px;
      height: 45px;
      align-items: center;
      justify-content: center;
      transition: 0.5s;
      z-index: 99;
      display:none; /* hidden until scrolled */
    }
    /*** Spinner End ***/

    /*** Button Start ***/
    .btn { font-weight: 600; transition: .5s; }
    .btn-square { width: 32px; height: 32px; }
    .btn-sm-square { width: 34px; height: 34px; }
    .btn-md-square { width: 44px; height: 44px; }
    .btn-lg-square { width: 56px; height: 56px; }
    .btn-square, .btn-sm-square, .btn-md-square, .btn-lg-square {
      padding: 0; display:flex; align-items:center; justify-content:center; font-weight: normal;
    }
    .btn.border-secondary { transition: 0.5s; }
    .btn.border-secondary:hover { background: var(--bs-secondary) !important; color: var(--bs-white) !important; }
    /*** Button End ***/

    /*** Topbar Start ***/
    .fixed-top { transition: 0.5s; background: var(--bs-white); border: 0; }
    .topbar { padding: 20px; border-radius: 230px 100px; }
    .topbar .top-info,
    .topbar .top-link {
      font-size: 15px; line-height: 0; letter-spacing: 1px; display:flex; align-items:center;
    }
    .topbar .top-link a { letter-spacing: 1px; }
    .topbar .top-link a small:hover { color: var(--bs-secondary) !important; transition: 0.5s; }
    .topbar .top-link a small:hover i { color: var(--bs-primary) !important; }
    /*** Topbar End ***/

    /*** Navbar Start ***/
    .navbar { height:100px; border-bottom:1px solid rgba(255,255,255,.1); }
    .navbar .navbar-nav .nav-link { padding: 10px 15px; font-size:16px; transition:.5s; }
    .navbar .navbar-nav .nav-link:hover,
    .navbar .navbar-nav .nav-link.active,
    .fixed-top.bg-white .navbar .navbar-nav .nav-link:hover,
    .fixed-top.bg-white .navbar .navbar-nav .nav-link.active {
      color: var(--bs-primary);
    }
    .navbar .dropdown-toggle::after {
      border:none; content:"\f107"; font-family:"Font Awesome 5 Free"; font-weight:700; vertical-align:middle; margin-left:8px;
    }
    @media (min-width: 1200px) {
      .navbar .nav-item .dropdown-menu {
        display:block; visibility:hidden; top:100%;
        transform: rotateX(-75deg);
        transform-origin: 0% 0%;
        border:0; transition:.5s; opacity:0;
      }
      .navbar .nav-item:hover .dropdown-menu {
        transform: rotateX(0deg);
        visibility: visible;
        background: var(--bs-light) !important;
        border-radius: 10px !important;
        transition: .5s;
        opacity: 1;
      }
    }
    .dropdown .dropdown-menu a:hover { background: var(--bs-secondary); color: var(--bs-primary); }
    #searchModal .modal-content { background: rgba(255,255,255,.8); }
    /*** Navbar End ***/

    /* Brand image size for header */
    .navbar-brand img { height: 100px; margin-right: 10px; border-radius: 50%; }

    /* Page header (breadcrumb area) */
    .page-header-contact{ background: linear-gradient(135deg, #e3f2fd, #f3e5f5); }
    .page-header-contact .breadcrumb a { color:#6c63ff; text-decoration:none; }
    .page-header-contact .breadcrumb a:hover { text-decoration:underline; }

    /* Contact page cards/map/form */
    .contact-card, .contact-form, .contact-side {
      background:#fff; border:1px solid #eee; border-radius:16px;
      box-shadow: 0 10px 30px rgba(0,0,0,.06);
    }
    .contact-icon {
      width:46px; height:46px; min-width:46px;
      background:#f1f2fe; color:#5a67d8; font-size:18px;
    }
    .map-wrapper {
      height:380px; border-radius:16px; overflow:hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,.08); border:1px solid #eee;
    }
    .contact-form .form-control {
      border-radius:12px; border:2px solid #e6e6f7; padding:12px 14px; box-shadow:none;
    }
    .contact-form .form-control:focus {
      border-color:#6a1b9a;
      box-shadow:0 0 0 3px rgba(106,27,154,.12);
    }
    @media (max-width: 575.98px){ .map-wrapper{height:280px;} .navbar-brand img{height:72px;} }
  </style>
</head>
<body>

  <!-- Spinner Start -->
  <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-grow text-primary" role="status"></div>
  </div>
  <!-- Spinner End -->

  <!-- Navbar start -->
  <div class="container-fluid fixed-top">
    <div class="container topbar bg-primary d-none d-lg-block">
      <div class="d-flex justify-content-between">
        <div class="top-info ps-2">
          <small class="me-3">
            <i class="fas fa-map-marker-alt me-2 text-secondary"></i>
            <a href="#" class="text-white"><?php echo htmlspecialchars($storeAddress); ?></a>
          </small>
          <small class="me-3">
            <i class="fas fa-envelope me-2 text-secondary"></i>
            <a href="#" class="text-white"><?php echo htmlspecialchars($storeEmail); ?></a>
          </small>
        </div>
      </div>
    </div>
    <br />
    <div class="container px-0">
      <nav class="navbar navbar-light bg-white navbar-expand-xl">
        <a href="./../index.php" class="navbar-brand d-flex align-items-center">
          <?php if (!empty($storeLogo)): ?>
            <img src="../assets/images/<?php echo htmlspecialchars($storeLogo); ?>" alt="Logo" />
          <?php endif; ?>
          <div><h2 class="text-primary display-6 mb-0"><?php echo htmlspecialchars($storeName); ?></h2></div>
        </a>
        <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
          <span class="fa fa-bars text-primary"></span>
        </button>
        <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
          <div class="navbar-nav mx-auto">
            <a href="./../index.php" class="nav-item nav-link">Home</a>
            <a href="./our_shop.php" class="nav-item nav-link">Shop</a>
            <a href="./contact.php" class="nav-item nav-link active">Contact</a>
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="./myorders.php" class="nav-item nav-link">My Orders</a>
            <?php endif; ?>
          </div>
          <div class="d-flex align-items-center gap-3">
            <?php if (!isset($_SESSION['user_id'])): ?>
              <a href="./Userlogin.php" class="btn btn-outline-dark">Login</a>
            <?php else: ?>
              <span class="text-dark fw-bold">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
              <a href="./logout.php" class="btn btn-danger">Logout</a>
            <?php endif; ?>
          </div>
          <div class="d-flex m-3 me-0">
            <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal">
              <i class="fas fa-search text-primary"></i>
            </button>
            <a href="./cart.php" class="position-relative me-4 my-auto">
              <i class="fa fa-shopping-bag fa-2x"></i>
              <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                    style="top:-5px; left:15px; height:20px; min-width:20px;"><?php echo $cartCount; ?></span>
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="./myaccount.php" class="my-auto"><i class="fas fa-user fa-2x"></i></a>
            <?php endif; ?>
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
          <h5 class="modal-title" id="exampleModalLabel">Search for products</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body d-flex align-items-center">
          <form action="our_shop.php" method="GET" class="input-group w-75 mx-auto d-flex">
            <input type="search" name="search_query" class="form-control p-3" placeholder="Search by product name..." aria-describedby="search-icon-1" />
            <button type="submit" id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal Search End -->

  <!-- Page Header / Breadcrumb -->
  <section class="page-header-contact py-5">
    <div class="container py-4">
      <h1 class="display-5 text-primary mb-2">Contact Us</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="./../index.php">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Contact</li>
        </ol>
      </nav>
    </div>
  </section>

  <!-- Contact Info + Map + Form -->
  <section class="contact-section py-5">
    <div class="container">
      <div class="row g-4">
        <!-- Contact Cards -->
        <div class="col-lg-4">
          <div class="contact-card p-4 rounded h-100">
            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="contact-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div>
                <h5 class="mb-1">Address</h5>
                <p class="mb-0"><?php echo htmlspecialchars($storeAddress); ?></p>
              </div>
            </div>
            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="contact-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="fas fa-envelope"></i>
              </div>
              <div>
                <h5 class="mb-1">Email</h5>
                <p class="mb-0"><?php echo htmlspecialchars($storeEmail); ?></p>
              </div>
            </div>
            <div class="d-flex align-items-start gap-3">
              <div class="contact-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="fas fa-phone-alt"></i>
              </div>
              <div>
                <h5 class="mb-1">Phone</h5>
                <p class="mb-0"><?php echo htmlspecialchars($storeContact); ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Map -->
        <div class="col-lg-8">
          <div class="map-wrapper rounded">
            <iframe
              class="w-100 h-100 rounded"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              src="https://www.google.com/maps?q=Dhangadhi,+Kailali,+Nepal&output=embed">
            </iframe>
          </div>
        </div>
      </div>

      <!-- Contact Form (front-end only) -->
      <div class="row g-4 mt-4">
        <div class="col-lg-8">
          <div class="contact-form p-4 rounded">
            <h4 class="mb-3 text-primary">Send us a message</h4>
            <form action="#" method="post" novalidate>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="name" class="form-label fw-semibold">Your Name</label>
                  <input id="name" name="name" type="text" class="form-control" placeholder="Enter your name" required />
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label fw-semibold">Your Email</label>
                  <input id="email" name="email" type="email" class="form-control" placeholder="Enter your email" required />
                </div>
                <div class="col-12">
                  <label for="subject" class="form-label fw-semibold">Subject</label>
                  <input id="subject" name="subject" type="text" class="form-control" placeholder="Subject" required />
                </div>
                <div class="col-12">
                  <label for="message" class="form-label fw-semibold">Message</label>
                  <textarea id="message" name="message" rows="6" class="form-control" placeholder="Write your message..." required></textarea>
                </div>
                <div class="col-12">
                  <button type="button" class="btn btn-primary rounded-pill px-4">Submit</button>
                  <p class="text-muted small mt-2 mb-0">* Demo only — not sending email yet.</p>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Quick Info -->
        <div class="col-lg-4">
          <div class="contact-side p-4 rounded h-100">
            <h5 class="mb-3">Store Info</h5>
            <ul class="list-unstyled mb-0">
              <li class="mb-2"><i class="bi bi-check2-circle me-2 text-primary"></i>Best quality clothes</li>
              <li class="mb-2"><i class="bi bi-check2-circle me-2 text-primary"></i>Fast customer support</li>
              <li class="mb-2"><i class="bi bi-check2-circle me-2 text-primary"></i>Dhangadhi based store</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer (same style you use) -->
  <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
    <div class="container py-5">
      <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226,175,24,.5);">
        <div class="row g-4">
          <div class="col-lg-3">
            <a href="#"><h1 class="text-primary mb-0">Clothes</h1><p class="text-secondary mb-0">Brand New products</p></a>
          </div>
          <div class="col-lg-6">
            <div class="position-relative mx-auto">
              <input class="form-control border-0 w-100 py-3 px-4 rounded-pill text-center fw-semibold"
                     type="text" value="Customer are the GOD for Us ! Thank you for your Love and Support !!"
                     readonly style="background-color: white; cursor: default;">
            </div>
          </div>
          <div class="col-lg-3">
            <div class="d-flex justify-content-end pt-3">
              <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="#"><i class="fab fa-twitter"></i></a>
              <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="#"><i class="fab fa-facebook-f"></i></a>
              <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="#"><i class="fab fa-youtube"></i></a>
              <a class="btn btn-outline-secondary btn-md-square rounded-circle" href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="row g-5">
        <div class="col-lg-3 col-md-6">
          <div class="footer-item">
            <h4 class="text-light mb-3">Why People Like us!</h4>
            <p class="mb-4">One and Only store where customer can find their dream clothes in their comfort zone.</p>
            <a href="#" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="d-flex flex-column text-start footer-item">
            <h4 class="text-light mb-3">Shop Info</h4>
            <a class="btn-link" href="contact.php">Contact Us</a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="d-flex flex-column text-start footer-item">
            <h4 class="text-light mb-3">Account</h4>
            <a class="btn-link" href="./myaccount.php">My Account</a>
            <a class="btn-link" href="./cart.php">Shopping Cart</a>
            <a class="btn-link" href="./myorders.php">Order History</a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="footer-item">
            <h4 class="text-light mb-3">Contact</h4>
            <p>Address: <?php echo htmlspecialchars($storeAddress); ?></p>
            <p>Email: <?php echo htmlspecialchars($storeEmail); ?></p>
            <p>Phone: <?php echo htmlspecialchars($storeContact); ?></p>
            <p>Payment Accepted</p>
            <img src="../assets/images/payment.png" class="img-fluid" alt="Payment Methods">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Copyright -->
  <div class="container-fluid copyright bg-dark py-4">
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
          <span class="text-light">
            <a href="#"><i class="fas fa-copyright text-light me-2"></i>
              <?php echo htmlspecialchars($storeName); ?></a>, All rights reserved.
          </span>
        </div>
        <div class="col-md-6 my-auto text-center text-md-end text-white">
          Designed By <a class="border-bottom" href="#">DLMS Group</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Back to Top -->
  <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top">
    <i class="fa fa-arrow-up"></i>
  </a>

  <!-- JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Hide spinner on load
    window.addEventListener('load', function(){
      const sp = document.getElementById('spinner');
      if (sp){ sp.classList.remove('show'); sp.style.opacity='0'; sp.style.visibility='hidden'; }
    });

    // Back-to-top visibility
    const backToTop = document.querySelector('.back-to-top');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 250) { backToTop.style.display = 'inline-flex'; }
      else { backToTop.style.display = 'none'; }
    });
  </script>
</body>
</html>
