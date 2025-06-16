<?php
session_start();
if (!isset($_SESSION['admin_email']) || $_SESSION['admin_type'] !== 'admin') {
    header("Location: Adminlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("DB connection failed: " . mysqli_connect_error());
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view.php");
    exit;
}

$id = (int)$_GET['id'];

// Fetch existing product data
$query = "SELECT * FROM product WHERE id = ? AND deleted_at IS NULL LIMIT 1";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    // Product not found or deleted
    header("Location: view.php");
    exit;
}

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize and validate inputs
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $sku = trim($_POST['sku']);

    if ($name === '') {
        $errors[] = "Product name is required.";
    }
    if ($price === '' || !is_numeric($price) || $price < 0) {
        $errors[] = "Valid price is required.";
    }
    if ($quantity === '' || !ctype_digit($quantity) || (int)$quantity < 0) {
        $errors[] = "Valid quantity is required.";
    }
    if ($sku === '') {
        $errors[] = "SKU is required.";
    }

    // Handle image upload (optional)
    $imageFileName = $product['image']; // Keep existing by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageFileName = uniqid('prod_') . '.' . $ext;
                $targetPath = "../assets/images/" . $imageFileName;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $errors[] = "Failed to upload image.";
                } else {
                    // Optionally delete old image file here if you want
                    if ($product['image'] && file_exists("../assets/images/" . $product['image'])) {
                        @unlink("../assets/images/" . $product['image']);
                    }
                }
            } else {
                $errors[] = "Invalid image type. Allowed: JPG, PNG, GIF.";
            }
        } else {
            $errors[] = "Error uploading image.";
        }
    }

    if (empty($errors)) {
        $updateQuery = "UPDATE product SET name = ?, description = ?, price = ?, quantity = ?, sku = ?, image = ?, updated_at = NOW() WHERE id = ?";
        $stmt2 = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($stmt2, "ssdisii", $name, $description, $price, $quantity, $sku, $imageFileName, $id);

        if (mysqli_stmt_execute($stmt2)) {
            $success = "Product updated successfully.";
            // Refresh product data after update
            $product['name'] = $name;
            $product['description'] = $description;
            $product['price'] = $price;
            $product['quantity'] = $quantity;
            $product['sku'] = $sku;
            $product['image'] = $imageFileName;
        } else {
            $errors[] = "Database update failed: " . mysqli_error($con);
        }
    }
}
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Clothing Store Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/Admindashboard.css" />
    <link rel="stylesheet" href="../assets/css/add_product.css" />
    <link rel="stylesheet" href="../assets/css/view_product_table.css" />
    <link rel="stylesheet" href="../assets/css/edit_product.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <!-- Top Navigation -->
    <header class="topnav">
        <div class="logo">
            <i class="fas fa-tshirt"></i> E-Clothing Store
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
            <li><a href="../admin/Admindashboard.php" class="sidebar-link active"><i class="fas fa-chart-line"></i> Dashboard</a></li>

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

            <li><a href="../admin/customer.php" class="sidebar-link"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="../admin/admin_orders.php" class="sidebar-link"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="../admin/orderdetail.php" class="sidebar-link"><i class="fas fa-clipboard-list"></i> Order Details</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="dashboard-content">
        <header class="page-header center-content text-center">
            <h1><i class="fas fa-edit"></i> Edit Product</h1>
        </header>

        <div class="form-wrapper">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" aria-label="Edit product form">
            <div class="form-group">
                <label for="name">Name<span aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required />
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price (Rs)<span aria-hidden="true">*</span></label>
                <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" min="0" step="0.01" required />
            </div>

            <div class="form-group">
                <label for="quantity">Quantity<span aria-hidden="true">*</span></label>
                <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" min="0" step="1" required />
            </div>

            <div class="form-group">
                <label for="sku">SKU<span aria-hidden="true">*</span></label>
                <input type="text" id="sku" name="sku" value="<?= htmlspecialchars($product['sku']) ?>" required />
            </div>

            <div class="form-group">
                <label>Current Image</label><br />
                <?php if ($product['image'] && file_exists("../assets/images/" . $product['image'])): ?>
                    <img src="<?= "../assets/images/" . htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="form-img" />
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="image">Change Image (optional)</label>
                <input type="file" id="image" name="image" accept="image/*" aria-describedby="imageHelp" />
                <small id="imageHelp">Allowed types: JPG, PNG, GIF. Leave empty to keep current image.</small>
            </div>

            <div class="form-actions">
                <button type="submit" name="submit" class="btn btn-primary">Update Product</button>
                <a href="view.php" class="btn btn-secondary" role="button">Cancel</a>
            </div>
        </form>
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
</body>
</html>

