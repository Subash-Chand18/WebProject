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

// ✅ Get product ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: view.php");
    exit;
}

// ✅ Fetch product
$query = "SELECT * FROM product WHERE id = $id AND deleted_at IS NULL";
$result = mysqli_query($con, $query);
$product = $result ? mysqli_fetch_assoc($result) : null;

if (!$product) {
    echo "<script>alert('Product not found.'); window.location.href='view.php';</script>";
    exit;
}

// ✅ Fetch categories
$categories = [];
$catResult = mysqli_query($con, "SELECT id, name FROM category WHERE deleted_at IS NULL");
while ($cat = mysqli_fetch_assoc($catResult)) {
    $categories[] = $cat;
}

$upload_dir = "../assets/images/";
$image = $product['image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $desc = mysqli_real_escape_string($con, $_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $sku = mysqli_real_escape_string($con, $_POST['sku']);
    $category_id = intval($_POST['category_id']);

    // ✅ Image handling
    if (!empty($_FILES['userfile']['name'])) {
        $image = basename($_FILES['userfile']['name']);
        $upload_file = $upload_dir . $image;

        if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK) {
            move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file);
        } else {
            echo "<script>alert('File upload error: " . $_FILES['userfile']['error'] . "');</script>";
        }
    }

    if ($price < 0) {
        echo "<script>alert('Price cannot be negative!');</script>";
    } else {
        $sql_update = "UPDATE product SET 
                        name = '$name', 
                        description = '$desc', 
                        image = '$image', 
                        price = $price, 
                        quantity = $quantity, 
                        sku = '$sku', 
                        category_id = $category_id 
                        WHERE id = $id";

        if (mysqli_query($con, $sql_update)) {
            echo "<script>alert('Product updated successfully.'); window.location.href='view.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error updating product: " . mysqli_error($con) . "');</script>";
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
    <title>Edit Product - E-Clothing Store Admin</title>
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

        <li class="dropdown">
            <a href="#" class="sidebar-link dropdown-toggle">
                <i class="fas fa-box-open"></i> Products <i class="fas fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a href="../product/add.php" class="sidebar-sublink">Add Product</a></li>
                <li><a href="../product/view.php" class="sidebar-sublink">View Products</a></li>
            </ul>
        </li>

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
    <section class="add-product-container">
        <form id="editProductForm" method="POST" enctype="multipart/form-data">
            <button type="button" class="close-btn" onclick="window.location.href='view.php'">&times;</button>

            <h2><i class="fas fa-edit"></i> Edit Product</h2>

            <div class="inline-group">
                <div class="form-group">
                    <input type="text" name="name" placeholder=" " value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    <label><i class="fas fa-tag"></i> Product Name</label>
                </div>
                <div class="form-group">
                    <input type="number" id="price" name="price" step="0.01" min="0" placeholder=" " value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    <label><span class="rs-symbol">Rs</span> Price</label>
                </div>
                <div class="form-group">
                    <input type="number" name="quantity" min="0" placeholder=" " value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
                    <label><i class="fas fa-boxes"></i> Quantity</label>
                </div>
            </div>

            <div class="form-group">
                <input type="text" name="sku" placeholder=" " value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                <label><i class="fas fa-barcode"></i> SKU</label>
            </div>

            <div class="form-group">
                <select name="category_id" required>
                    <option value="" disabled>Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $product['category_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label><i class="fas fa-list"></i> Category</label>
            </div>

            <div class="form-group">
                <textarea name="description" placeholder=" " required><?php echo htmlspecialchars($product['description']); ?></textarea>
                <label><i class="fas fa-align-left"></i> Description</label>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Current Image</label><br>
                <?php if (!empty($image) && file_exists($upload_dir . $image)) : ?>
                    <img src="<?php echo $upload_dir . htmlspecialchars($image); ?>" alt="Product Image" style="max-width:180px; margin-bottom:10px;">
                <?php else: ?>
                    <p>No image uploaded.</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="userfile"><i class="fas fa-upload"></i> Change Image (optional)</label><br>
                <input type="file" name="userfile" id="userfile" accept="image/*">
                <small>Leave empty to keep existing image.</small>
            </div>

            <div class="button-group" style="margin-top: 25px;">
                <button type="submit" name="submit"><i class="fas fa-save"></i> Update Product</button>
                <button type="reset" class="cancel-btn"><i class="fas fa-eraser"></i> Clear Form</button>
            </div>
        </form>
    </section>

    <script>
        document.getElementById('editProductForm').addEventListener('submit', function (e) {
            const priceInput = document.getElementById('price');
            if (parseFloat(priceInput.value) < 0) {
                alert("Price cannot be negative!");
                priceInput.focus();
                e.preventDefault();
            }
        });
    </script>
</main>

<!-- Footer -->
<footer class="footer">
    <p>&copy; 2025 E-Clothing Store. All Rights Reserved.</p>
</footer>

<!-- Scripts -->
<script>
    document.querySelectorAll('.dropdown-toggle').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentElement.classList.toggle('open');
        });
    });

    document.querySelectorAll('.sidebar-link').forEach(function(link) {
        link.addEventListener('click', function() {
            document.querySelectorAll('.sidebar-link').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
    });

    document.querySelectorAll('.topnav-menu .nav-link').forEach(function(link) {
        link.addEventListener('click', function() {
            document.querySelectorAll('.topnav-menu .nav-link').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>
</body>
</html>
