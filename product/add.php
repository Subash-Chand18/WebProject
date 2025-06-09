<?php
include '../includes/header.php';

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch active categories (where deleted_at IS NULL)
$categoryResult = mysqli_query($con, "SELECT id, name FROM category WHERE deleted_at IS NULL");
$categories = [];
if ($categoryResult) {
    while ($row = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["submit"])) {
    $upload_dir = "../assets/images/";
    $image = "";

    // Check if file was uploaded
    if (!empty($_FILES["userfile"]["name"]) && is_uploaded_file($_FILES["userfile"]["tmp_name"])) {
        $upload_file = $upload_dir . basename($_FILES["userfile"]["name"]);

        // Optionally, to avoid filename collisions, add timestamp prefix
        $upload_file = $upload_dir . time() . "_" . basename($_FILES["userfile"]["name"]);

        if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $upload_file)) {
            $image = basename($upload_file);
        }
    }

    // Escape and sanitize inputs
    $name = mysqli_real_escape_string($con, $_POST["name"]);
    $desc = mysqli_real_escape_string($con, $_POST["description"]);
    $price = floatval($_POST["price"]);
    $qty = intval($_POST["quantity"]);
    $sku = mysqli_real_escape_string($con, $_POST["sku"]);
    $c_id = intval($_POST["category_id"]);

    // Insert product into product table
    $sql = "INSERT INTO product (name, description, price, sku, quantity, category_id, image)
            VALUES ('$name', '$desc', $price, '$sku', $qty, $c_id, '$image')";

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Product added successfully.');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}
?>

<section class="add-product-container">
    <form id="addProductForm" method="POST" enctype="multipart/form-data">
        <!-- Close button -->
        <button type="button" class="close-btn" onclick="window.location.href='../admin/Admindashboard.php'">&times;</button>

        <h2><i class="fas fa-plus-circle"></i> Add Product</h2>

        <div class="inline-group">
            <div class="form-group">
                <input type="text" name="name" placeholder=" " required>
                <label><i class="fas fa-tag"></i> Product Name</label>
            </div>
            <div class="form-group">
                <input type="number" name="price" step="0.01" min="0" placeholder=" " required>
                <label><i class="fas fa-dollar-sign"></i> Price</label>
            </div>
            <div class="form-group">
                <input type="number" name="quantity" placeholder=" " required>
                <label><i class="fas fa-boxes"></i> Quantity</label>
            </div>
        </div>

        <div class="form-group">
            <input type="text" name="sku" placeholder=" " required>
            <label><i class="fas fa-barcode"></i> SKU</label>
        </div>

        <div class="form-group">
            <select name="category_id" required>
                <option value="" disabled selected></option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['id']); ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label><i class="fas fa-list"></i> Category</label>
        </div>

        <div class="form-group">
            <textarea name="description" placeholder=" " required></textarea>
            <label><i class="fas fa-align-left"></i> Description</label>
        </div>

        <div class="form-group">
            <input type="file" name="userfile" required>
            <label><i class="fas fa-image"></i> Upload Image</label>
        </div>

        <div class="button-group">
            <button type="submit" name="submit"><i class="fas fa-upload"></i> Add Product</button>
            <button type="reset" class="cancel-btn"><i class="fas fa-eraser"></i> Clear Form</button>
        </div>
    </form>
</section>

<?php include '../includes/footer.php'; ?>
