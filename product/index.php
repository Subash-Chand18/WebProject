<?php

// db connection
$con = mysqli_connect("localhost", "root", "", "EClothingStore");

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["submit"])) {
    $upload_dir = "../assets/images/";
    $upload_file = $upload_dir . basename($_FILES["userfile"]["name"]);
    $image = "";

    if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $upload_file)) {
        $image = $_FILES["userfile"]["name"];
    }

    $name = $_POST["name"];
    $desc = $_POST["description"];
    $price = $_POST["price"];
    $qty = $_POST["quantity"];
    $sku = $_POST["sku"];
    $c_id = intval($_POST["category_id"]);

    $sql = "INSERT INTO product (name, description, price, sku, quantity, category_id, image)
            VALUES ('$name', '$desc', $price, '$sku', '$qty', $c_id, '$image')";

    $result = mysqli_query($con, $sql);

    if ($result) {
        echo "<script>alert('Product added successfully.');</script>";
    } else {
        echo "<script>alert('Error inserting data: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/add_product.css" />
    <script>
        function goToDashboard() {
            window.location.href = '../admin/Admindashboard.php';
        }
    </script>
</head>
<body>
     
    <!-- Add Product Form -->
    <form id="addProductForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
        <button class="close-btn" onclick="goToDashboard();" type="button" title="Close">
            <i class="fas fa-times"></i>
        </button>
        <h2><i class="fas fa-plus-circle"></i> Add Product</h2>

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
                <input type="number" id="quantity" name="quantity" placeholder=" " required>
                <label for="quantity"><i class="fas fa-boxes"></i> Quantity</label>
            </div>
        </div>

        <div class="form-group">
            <input type="text" id="sku" name="sku" placeholder=" " required>
            <label for="sku"><i class="fas fa-barcode"></i> SKU</label>
        </div>

        <div class="form-group">
            <select id="category_id" name="category_id" required>
                <option value="" disabled selected></option>
                <option value="1">Men</option>
                <option value="2">Women</option>
                <option value="3">Babies</option>
            </select>
            <label for="category_id"><i class="fas fa-list"></i> Category</label>
        </div>

        <div class="form-group">
            <textarea id="description" name="description" placeholder=" " required></textarea>
            <label for="description"><i class="fas fa-align-left"></i> Description</label>
        </div>

        <div class="form-group">
            <input type="file" id="userfile" name="userfile" required>
            <label for="userfile"><i class="fas fa-image"></i> Upload Image</label>
        </div>

        <div class="button-group">
            <button type="submit" name="submit" id="submitBtn">
                <i class="fas fa-upload"></i> Add Products
            </button>
            <button type="button" class="cancel-btn" onclick="document.getElementById('addProductForm').reset();">
                <i class="fas fa-eraser"></i> Clear Form
            </button>
            <button type="button" class="view-btn" onclick="window.location.href='view_product.php';">
                 <i class="fas fa-eye"></i> View Products
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
