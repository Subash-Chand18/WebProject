<?php
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$product = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT p.*, c.name AS category_name 
            FROM product p
            LEFT JOIN category c ON p.category_id = c.id
            WHERE p.id = $id";

    $result = mysqli_query($con, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $product = $row;
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }
        .product-container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .product-image {
            max-height: 100%;
            object-fit: contain;
        }
        .category-badge {
            background-color: #ffc107;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .price-tag {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
        }
        .btn-style {
            padding: 10px 20px;
            border-radius: 50px;
        }
    </style>
</head>
<body>

<div class="container product-container p-4">
    <?php if ($product): ?>
        <div class="row">
            <div class="col-md-6 text-center">
                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid product-image rounded">
            </div>
            <div class="col-md-6">
                <span class="category-badge"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h2 class="mt-3"><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
                <p><strong>Quantity Available:</strong> <?php echo htmlspecialchars($product['quantity']); ?></p>
                <p class="price-tag">$<?php echo htmlspecialchars($product['price']); ?></p>
                <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn btn-success">
                <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                </a>
                <a href="../index.php" class="btn btn-outline-secondary btn-style ms-2">Back to Products</a>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center">
            <h3>Product not found</h3>
            <a href="../index.php" class="btn btn-primary mt-3">Back to Home</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
