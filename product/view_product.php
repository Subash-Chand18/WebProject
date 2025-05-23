<?php
// DB connection
$con = mysqli_connect("localhost", "root", "", "EClothingStore");

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM product WHERE deleted_at IS NULL ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Products</title>
    <link rel="stylesheet" href="../assets/css/view_product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<h1 class="page-title"><i class="fas fa-boxes"></i> All Products</h1>

<div class="product-grid">
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="product-card" onclick="showModal('<?php echo htmlspecialchars($row['image']); ?>', '<?php echo htmlspecialchars(addslashes($row['name'])); ?>', '<?php echo htmlspecialchars(addslashes($row['description'])); ?>', '<?php echo $row['price']; ?>', '<?php echo $row['quantity']; ?>', '<?php echo $row['sku']; ?>')">
            <img src="../assets/images/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p><strong>$<?php echo $row['price']; ?></strong></p>
        </div>
    <?php } ?>
</div>

<!-- Product Detail Modal -->
<div class="modal" id="productModal">
    <div class="modal-box">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-body">
            <img id="modalImage" src="" alt="Product" onclick="showImageOnly(this.src)">
            <div class="modal-details">
                <h2 id="modalName"></h2>
                <p id="modalDesc"></p>
                <p><strong>Price:</strong> $<span id="modalPrice"></span></p>
                <p><strong>Quantity:</strong> <span id="modalQty"></span></p>
                <p><strong>SKU:</strong> <span id="modalSKU"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div class="image-modal" id="imageModal">
    <span class="close-image" onclick="closeImageModal()">&times;</span>
    <img id="modalImageViewer" src="" alt="Enlarged Product">
</div>

<script>
function showModal(image, name, desc, price, qty, sku) {
    document.getElementById('modalImage').src = '../assets/images/' + image;
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalDesc').innerText = desc;
    document.getElementById('modalPrice').innerText = price;
    document.getElementById('modalQty').innerText = qty;
    document.getElementById('modalSKU').innerText = sku;
    document.getElementById('productModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

// Full image modal
function showImageOnly(src) {
    document.getElementById('modalImageViewer').src = src;
    document.getElementById('imageModal').style.display = 'flex';
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}
</script>

</body>
</html>
