<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "E_Clothing_Store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];
    $sku = $_POST['sku'];
    $category_id = $_POST['category_id'];

    // Image upload
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $uploadPath = "images/" . $imageName;

    if (move_uploaded_file($imageTmp, $uploadPath)) {
        $sql = "INSERT INTO product (name, description, price, quantity, sku, category_id, image)
                VALUES ('$name', '$desc', $price, $qty, '$sku', $category_id, '$imageName')";

        if ($conn->query($sql) === TRUE) {
            $msg = "Product added successfully!";
        } else {
            $msg = "Error: " . $conn->error;
        }
    } else {
        $msg = "Image upload failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" href="../product/addproduct.css"/>
</head>
<body>

<form method="POST" enctype="multipart/form-data">
    <h2>Add Product</h2>

    <?php if ($msg): ?>
        <p class="msg"><?= $msg ?></p>
    <?php endif; ?>

    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Description:</label>
    <textarea name="description" required></textarea>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" required>

    <label>Quantity:</label>
    <input type="number" name="quantity" required>

    <label>SKU:</label>
    <input type="text" name="sku" required>

    <label>Category:</label>
    <select name="category_id" required>
        <option value="1">Men</option>
        <option value="2">Women</option>
        <option value="3">Babies</option>
        
    </select>

    <label>Product Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit">Add Product</button>
</form>

</body>
</html>
