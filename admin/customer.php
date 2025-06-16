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

// ✅ Fetch all non-admin users who are not soft deleted (deleted_at IS NULL)
$sql = "SELECT id, name, email, image, created_at FROM user WHERE user_type != 'admin' AND deleted_at IS NULL ORDER BY created_at ASC";
$res = mysqli_query($con, $sql);

$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Customer Details</title>
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
        img { width: 60px; height: 60px; object-fit: cover; border-radius: 50%; }
        .customers-container { padding: 20px; }
        h2 { margin-bottom: 20px; }
    </style>
</head>

<body>
    <!-- Top Navigation -->
    <header class="topnav">
        <div class="logo">
            <i class="fas fa-tshirt"></i> E-Clothing Store
        </div>
        <nav class="topnav-menu">
            <a href="#" class="nav-link active">Home</a>
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

            <li><a href="customer.php" class="sidebar-link active"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="admin_orders.php" class="sidebar-link"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="orderdetail.php" class="sidebar-link"><i class="fas fa-clipboard-list"></i> Order Details</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <section class="customers-container">
            <h2><i class="fas fa-users"></i> Customer Details</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>#User ID</th>
                            <th>Profile Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($res && mysqli_num_rows($res) > 0): 
                            $sn = 1;
                            while ($user = mysqli_fetch_assoc($res)): 
                        ?>
                            <tr>
                                <td><?= $sn++ ?></td>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td>
                                    <?php
                                    $imagePath = __DIR__ . '/../design-assets/img/' . $user['image'];
                                    $imageUrl = '../design-assets/img/' . htmlspecialchars($user['image']);
                                    if (!empty($user['image']) && file_exists($imagePath)) {
                                        $imgSrc = $imageUrl;
                                    } else {
                                        $imgSrc = '../design-assets/img/default-profile.png';
                                    }
                                    ?>
                                    <img src="<?= $imgSrc ?>" alt="Profile Image">
                                </td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= date('Y-m-d h:i A', strtotime($user['created_at'])) ?></td>
                            </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No customers found.</td>
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
