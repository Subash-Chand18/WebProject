<?php
// DB connection
$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) die("DB connection failed: " . mysqli_connect_error());

// Fetch all products
$result = mysqli_query($con, "SELECT * FROM product WHERE deleted_at IS NULL ORDER BY created_at DESC");
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>View Products</title>
<link rel="stylesheet" href="../assets/css/view_product.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<script>
    function closePage() {
        window.location.href = '../product/index.php';
    }
</script>
</head>
<body>

<h1 class="page-title"><i class="fas fa-box-open"></i> Our Products</h1>

<div class="search-container">
    <input type="text" id="searchInput" placeholder="Search by name, ID or SKU..." autocomplete="off" />
    <button class="refresh-btn" type="button" title="Clear Search" onclick="clearSearch()">
        <i class="fas fa-sync-alt"></i>
    </button>
</div>

<!-- Close button -->
<button class="close-btn" type="button" title="Close" onclick="closePage();">
    <i class="fas fa-times"></i>
</button>

<div id="productGrid" class="product-grid">
    <?php foreach ($products as $p): ?>
    <div class="product-card" 
         data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
         data-sku="<?= htmlspecialchars(strtolower($p['sku'])) ?>"
         data-id="<?= $p['id'] ?>"
         onclick="openProductModal(<?= htmlspecialchars(json_encode($p)) ?>)">
        <img src="../assets/images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" />
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <p class="price">$<?= htmlspecialchars($p['price']) ?></p>
    </div>
    <?php endforeach; ?>
</div>

<!-- Product Detail Modal -->
<div id="productModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalName">
    <div class="modal-content">
        <button class="modal-close-btn" aria-label="Close Product Details" onclick="closeProductModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-body">
            <img id="modalImage" src="" alt="Product Image" onclick="openImageView()" />
            <div class="modal-details">
                <h2 id="modalName"></h2>
                <p id="modalDesc" class="desc-font"></p>
                <p><strong>Price:</strong> $<span id="modalPrice"></span></p>
                <p><strong>Quantity:</strong> <span id="modalQty"></span></p>
                <p><strong>SKU:</strong> <span id="modalSKU"></span></p>
                <div class="modal-actions">
                    <button class="edit-btn"><i class="fas fa-edit"></i> Edit</button>
                    <button class="delete-btn"><i class="fas fa-trash-alt"></i> Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Large Image View Modal -->
<div id="imageViewModal" class="modal" onclick="closeImageView(event)">
    <button class="modal-close-btn close-image" aria-label="Close Large Image" onclick="closeImageView(event)">
        <i class="fas fa-times"></i>
    </button>
    <img id="largeImage" src="" alt="Detailed Product Image" />
</div>

<script>
const searchInput = document.getElementById('searchInput');
const productGrid = document.getElementById('productGrid');
const productCards = productGrid.querySelectorAll('.product-card');

searchInput.addEventListener('input', () => {
    filterProducts(searchInput.value);
});

function clearSearch() {
    searchInput.value = '';
    filterProducts('');
}

function filterProducts(query) {
    const q = query.toLowerCase();
    productCards.forEach(card => {
        const name = card.getAttribute('data-name');
        const sku = card.getAttribute('data-sku');
        const id = card.getAttribute('data-id');
        if (name.includes(q) || sku.includes(q) || id.includes(q)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Product Modal Elements
const productModal = document.getElementById('productModal');
const modalImage = document.getElementById('modalImage');
const modalName = document.getElementById('modalName');
const modalDesc = document.getElementById('modalDesc');
const modalPrice = document.getElementById('modalPrice');
const modalQty = document.getElementById('modalQty');
const modalSKU = document.getElementById('modalSKU');

// Image View Modal Elements
const imageViewModal = document.getElementById('imageViewModal');
const largeImage = document.getElementById('largeImage');

function openProductModal(product) {
    modalImage.src = '../assets/images/' + product.image;
    modalName.textContent = product.name;
    modalDesc.textContent = product.description;
    modalPrice.textContent = product.price;
    modalQty.textContent = product.quantity;
    modalSKU.textContent = product.sku;

    productModal.style.display = 'flex';
}

function closeProductModal() {
    productModal.style.display = 'none';
}

function openImageView() {
    largeImage.src = modalImage.src;
    imageViewModal.style.display = 'flex';
}

function closeImageView(event) {
    if (!event || event.target === imageViewModal || event.target.classList.contains('close-image')) {
        imageViewModal.style.display = 'none';
    }
}

window.onclick = function(event) {
    if (event.target === productModal) {
        closeProductModal();
    }
    if (event.target === imageViewModal) {
        closeImageView();
    }
};
</script>

</body>
</html>
