<?php
include '../includes/header.php';

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: view.php");
    exit;
}

$query = "SELECT * FROM product WHERE id = $id AND deleted_at IS NULL";
$result = mysqli_query($con, $query);
$product = $result ? mysqli_fetch_assoc($result) : null;

if (!$product) {
    echo "<p>Product not found.</p>";
    exit;
}

$categories = [];
$catResult = mysqli_query($con, "SELECT * FROM category WHERE deleted_at IS NULL");
while ($cat = mysqli_fetch_assoc($catResult)) {
    $categories[] = $cat;
}

$upload_dir = "../assets/images/";
$image = $product['image']; // current image filename

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $desc = str_replace("'", "", $_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $sku = $_POST['sku'];
    $category_id = $_POST['category_id'];

    if (!empty($_FILES['userfile']['name'])) {
        $image = basename($_FILES['userfile']['name']);
        $upload_file = $upload_dir . $image;
        move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file);
    }

    // Update query
    $sql_update = "UPDATE product SET 
        name = '$name', 
        description = '$desc', 
        image = '$image', 
        price = $price, 
        quantity = $quantity, 
        sku = '$sku', 
        category_id = $category_id
        WHERE id = $id";

    $res_update = mysqli_query($con, $sql_update);

    if ($res_update) {
        header("Location: view.php");
        exit;
    } else {
        echo "<p style='color:red;'>Error updating product: " . mysqli_error($con) . "</p>";
    }
}
?>

<section class="add-product-container" style="max-width: 600px; margin: 40px auto;">
    <form method="POST" enctype="multipart/form-data" novalidate>
        <button type="button" class="close-btn" onclick="window.location.href='view.php'">&times;</button>
        <h2><i class="fas fa-edit"></i> Edit Product</h2>

        <div class="form-group">
            <input type="text" name="name" placeholder=" " value="<?= htmlspecialchars($product['name']) ?>" required>
            <label><i class="fas fa-tag"></i> Product Name</label>
        </div>

        <div class="form-group">
            <input type="number" name="price" step="0.01" min="0" placeholder=" " value="<?= htmlspecialchars($product['price']) ?>" required>
            <label><i class="fas fa-dollar-sign"></i> Price</label>
        </div>

        <div class="form-group">
            <input type="number" name="quantity" min="0" placeholder=" " value="<?= htmlspecialchars($product['quantity']) ?>" required>
            <label><i class="fas fa-boxes"></i> Quantity</label>
        </div>

        <div class="form-group">
            <input type="text" name="sku" placeholder=" " value="<?= htmlspecialchars($product['sku']) ?>" required>
            <label><i class="fas fa-barcode"></i> SKU</label>
        </div>

        <div class="form-group">
            <select name="category_id" required>
                <option value="" disabled>Select category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label><i class="fas fa-list"></i> Category</label>
        </div>

        <div class="form-group">
            <textarea name="description" placeholder=" " required><?= htmlspecialchars($product['description']) ?></textarea>
            <label><i class="fas fa-align-left"></i> Description</label>
        </div>

        <div class="form-group">
            <label><i class="fas fa-image"></i> Current Image</label><br>
            <?php if (!empty($image) && file_exists($upload_dir . $image)) : ?>
                <img src="<?= $upload_dir . htmlspecialchars($image) ?>" alt="Product Image" style="max-width:180px; margin-bottom:10px;">
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
        </div>
    </form>
</section>

<?php include '../includes/footer.php'; ?>