<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) die("Connection failed: " . mysqli_connect_error());

// Get product ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: view.php");
    exit;
}

// Fetch product details
$query = mysqli_query($con, "SELECT * FROM product WHERE id = '$id'");
$product = mysqli_fetch_assoc($query);

if (!$product) {
    echo "Product not found.";
    exit;
}

// Fetch categories
$categories = mysqli_query($con, "SELECT * FROM category");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $desc = mysqli_real_escape_string($con, $_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $sku = mysqli_real_escape_string($con, $_POST['sku']);
    $category_id = intval($_POST['category_id']);

    mysqli_query($con, "UPDATE product SET 
        name = '$name', 
        description = '$desc', 
        price = '$price', 
        quantity = '$quantity', 
        sku = '$sku',
        category_id = '$category_id'
        WHERE id = '$id'");

    header("Location: view.php");
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<section class="add-product-container">
    <form id="addProductForm" method="POST">
        <!-- Close button -->
        <button type="button" class="close-btn" onclick="window.location.href='view.php'">&times;</button>

        <h2><i class="fas fa-edit"></i> Edit Product</h2>

        <div class="inline-group">
            <div class="form-group">
                <input type="text" name="name" placeholder=" " value="<?= htmlspecialchars($product['name']) ?>" required>
                <label><i class="fas fa-tag"></i> Product Name</label>
            </div>
            <div class="form-group">
                <input type="number" name="price" step="0.01" min="0" placeholder=" " value="<?= $product['price'] ?>" required>
                <label><i class="fas fa-dollar-sign"></i> Price</label>
            </div>
            <div class="form-group">
                <input type="number" name="quantity" placeholder=" " value="<?= $product['quantity'] ?>" required>
                <label><i class="fas fa-boxes"></i> Quantity</label>
            </div>
        </div>

        <div class="form-group">
            <input type="text" name="sku" placeholder=" " value="<?= htmlspecialchars($product['sku']) ?>" required>
            <label><i class="fas fa-barcode"></i> SKU</label>
        </div>

        <div class="form-group">
            <select name="category_id" required>
                <option value="" disabled>Select Category</option>
                <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label><i class="fas fa-list"></i> Category</label>
        </div>

        <div class="form-group">
            <textarea name="description" placeholder=" " required><?= htmlspecialchars($product['description']) ?></textarea>
            <label><i class="fas fa-align-left"></i> Description</label>
        </div>

        <div class="button-group">
            <button type="submit" name="submit"><i class="fas fa-save"></i> Update Product</button>
        </div>
    </form>
</section>

<?php include '../includes/footer.php'; ?>
