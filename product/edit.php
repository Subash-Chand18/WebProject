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

// Initialize image path
$image = $product['image'] ?? "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["submit"])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $desc = mysqli_real_escape_string($con, $_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $sku = mysqli_real_escape_string($con, $_POST['sku']);
    $category_id = intval($_POST['category_id']);

    // Image upload handling
    $upload_dir = "../assets/images/";

    // Check if a new file is uploaded
    if (!empty($_FILES["userfile"]["name"]) && is_uploaded_file($_FILES["userfile"]["tmp_name"])) {
        $target_file = $upload_dir . basename($_FILES["userfile"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            echo "<p style='color:red; text-align:center;'>Only JPG, JPEG, PNG & GIF files are allowed.</p>";
        } else {
            // Optionally rename file to avoid conflicts
            $new_filename = uniqid("img_") . "." . $imageFileType;
            $target_file = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_file)) {
                // Delete old image file if exists and different from default
                if (!empty($image) && file_exists($upload_dir . $image)) {
                    unlink($upload_dir . $image);
                }
                $image = $new_filename;
            } else {
                echo "<p style='color:red; text-align:center;'>Sorry, there was an error uploading your file.</p>";
            }
        }
    }

    // Update query with new or existing image
    $update_sql = "UPDATE product SET 
        name = '$name',
        description = '$desc',
        price = '$price',
        quantity = '$quantity',
        sku = '$sku',
        category_id = '$category_id',
        image = '$image'
        WHERE id = '$id'";

    if (mysqli_query($con, $update_sql)) {
        header("Location: view.php");
        exit;
    } else {
        echo "Error updating product: " . mysqli_error($con);
    }
}
?>

<?php include '../includes/header.php'; ?>

<section class="add-product-container" style="max-width: 600px; margin: 40px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
    <form id="editProductForm" method="POST" enctype="multipart/form-data">
        <!-- Close button -->
        <button type="button" class="close-btn" onclick="window.location.href='view.php'">&times;</button>

        <h2><i class="fas fa-edit"></i> Edit Product</h2>

        <div class="inline-group" style="display:flex; gap:16px; flex-wrap:wrap;">
            <div class="form-group" style="flex:1 1 45%;">
                <input type="text" name="name" placeholder=" " value="<?= htmlspecialchars($product['name']) ?>" required>
                <label><i class="fas fa-tag"></i> Product Name</label>
            </div>

            <div class="form-group" style="flex:1 1 45%;">
                <input type="number" name="price" step="0.01" min="0" placeholder=" " value="<?= htmlspecialchars($product['price']) ?>" required>
                <label><i class="fas fa-dollar-sign"></i> Price</label>
            </div>

            <div class="form-group" style="flex:1 1 45%;">
                <input type="number" name="quantity" min="0" placeholder=" " value="<?= htmlspecialchars($product['quantity']) ?>" required>
                <label><i class="fas fa-boxes"></i> Quantity</label>
            </div>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <input type="text" name="sku" placeholder=" " value="<?= htmlspecialchars($product['sku']) ?>" required>
            <label><i class="fas fa-barcode"></i> SKU</label>
        </div>

        <div class="form-group" style="margin-top: 15px;">
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

        <div class="form-group" style="margin-top: 15px;">
            <textarea name="description" placeholder=" " required><?= htmlspecialchars($product['description']) ?></textarea>
            <label><i class="fas fa-align-left"></i> Description</label>
        </div>

        <!-- Current image preview -->
        <div class="form-group" style="margin-top: 15px;">
            <label><i class="fas fa-image"></i> Current Image</label><br>
            <?php if (!empty($product['image']) && file_exists("../assets/images/" . $product['image'])) : ?>
                <img src="<?= "../assets/images/" . htmlspecialchars($product['image']) ?>" alt="Product Image" style="max-width: 180px; border-radius: 6px; border: 1px solid #ccc;">
            <?php else: ?>
                <p>No image uploaded.</p>
            <?php endif; ?>
        </div>

        <!-- Image upload -->
        <div class="form-group" style="margin-top: 10px;">
            <label for="userfile"><i class="fas fa-upload"></i> Change Image</label><br>
            <input type="file" name="userfile" id="userfile" accept="image/*">
            <small style="color: #666;">Leave empty to keep existing image.</small>
        </div>

        <div class="button-group" style="margin-top: 25px;">
            <button type="submit" name="submit"><i class="fas fa-save"></i> Update Product</button>
        </div>
    </form>
</section>

<?php include '../includes/footer.php'; ?>
