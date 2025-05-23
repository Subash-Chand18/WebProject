<?php
// db connection
$conn = new mysqli("localhost", "root", "", "EClothingStore");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";

// Fetch categories for dropdown
$categories = [];
$catResult = $conn->query("SELECT id, name FROM category");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $qty = intval($_POST['quantity']);
    $sku = trim($_POST['sku']);
    $category_id = intval($_POST['category_id']);

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $uploadDir = "../assets/images/";
        $uploadPath = $uploadDir . $imageName;

        // Optional: validate file type here (e.g. jpg, png)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            $msg = "Only JPG, PNG, and GIF files are allowed.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Prepare statement to prevent SQL injection
                $stmt = $conn->prepare("INSERT INTO product (name, description, price, quantity, sku, category_id, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("ssdisss", $name, $desc, $price, $qty, $sku, $category_id, $imageName);

                if ($stmt->execute()) {
                    $msg = "Product added successfully!";
                } else {
                    $msg = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $msg = "Failed to upload image.";
            }
        }
    } else {
        $msg = "Please upload a product image.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Product - E-Clothing Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/add_product.css" />
    <script>
        function goToDashboard() {
            window.location.href = '../admin/Admindashboard.php';
        }
    </script>
</head>
<body>
    <form id="addProductForm" action="" method="POST" enctype="multipart/form-data" class="add-product-form" novalidate>
        <button type="button" class="close-btn" onclick="goToDashboard();" title="Close">
            <i class="fas fa-times"></i>
        </button>
        <h2><i class="fas fa-plus-circle"></i> Add Product</h2>

        <?php if ($msg): ?>
            <p class="message"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <div class="inline-group">
            <div class="form-group">
                <input type="text" id="name" name="name" placeholder=" " required>
                <label for="name"><i class="fas fa-tag"></i> Product Name</label>
            </div>
            <div class="form-group">
                <input type="number" id="price" name="price" step="0.01" min="0" placeholder=" " required>
                <label for="price"><i class="fas fa-dollar-sign"></i> Price</label>
            </div>
            <div class="form-group">
                <input type="number" id="quantity" name="quantity" min="0" placeholder=" " required>
                <label for="quantity"><i class="fas fa-boxes"></i> Quantity</label>
            </div>
        </div>

        <div class="form-group">
            <input type="text" id="sku" name="sku" placeholder=" " required>
            <label for="sku"><i class="fas fa-barcode"></i> SKU</label>
        </div>

        <div class="form-group">
            <select id="category_id" name="category_id" required>
                <option value="" disabled selected>Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="category_id"><i class="fas fa-list"></i> Category</label>
        </div>

        <div class="form-group">
            <textarea id="description" name="description" placeholder=" " required></textarea>
            <label for="description"><i class="fas fa-align-left"></i> Description</label>
        </div>

        <div class="form-group">
            <input type="file" id="image" name="image" accept="image/*" required>
            <label for="image"><i class="fas fa-image"></i> Upload Image</label>
        </div>

        <div class="button-group">
            <button type="submit" name="submit"><i class="fas fa-upload"></i> Add Product</button>
            <button type="button" class="cancel-btn" onclick="document.getElementById('addProductForm').reset();">
                <i class="fas fa-eraser"></i> Clear Form
            </button>
        </div>
    </form>

    <script>
        document.getElementById('addProductForm').addEventListener('submit', function (e) {
            const priceInput = document.getElementById('price');
            if (parseFloat(priceInput.value) < 0) {
                alert("Price cannot be negative!");
                priceInput.focus();
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
