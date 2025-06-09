<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("DB connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($con, "SELECT * FROM product WHERE deleted_at IS NULL ORDER BY created_at DESC");
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard-content">

    <header class="page-header center-content text-center">
        <h1><i class="fas fa-box-open"></i> Our Products</h1>
    </header>

    <div class="search-wrapper">
        <input type="search" id="searchInput" placeholder="Search by ID, Name or SKU..." autocomplete="off" aria-label="Search products" />
       
        <button type="button" class="page-close-btn" title="Back to Dashboard" aria-label="Close and return to dashboard" onclick="window.location.href='../admin/Admindashboard.php'">
            &times;
        </button>
    </div>

    <div class="table-container" role="region" aria-live="polite" aria-relevant="all">
        <table id="productTable" class="product-table" aria-label="List of products">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price ($)</th>
                    <th>Quantity</th>
                    <th>SKU</th>
                    <th>Created At</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0):
                    $sn = 1;
                    foreach ($products as $p):
                        $descShort = strlen($p['description']) > 80 ? substr($p['description'], 0, 80) . '...' : $p['description'];
                        $imgPath = "../assets/images/" . htmlspecialchars($p['image']);
                ?>
                <tr data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" data-sku="<?= htmlspecialchars(strtolower($p['sku'])) ?>">
                    <td><?= $sn++ ?></td>
                    <td><img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="table-img" /></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td title="<?= htmlspecialchars($p['description']) ?>"><?= htmlspecialchars($descShort) ?></td>
                    <td><?= number_format($p['price'], 2) ?></td>
                    <td><?= htmlspecialchars($p['quantity']) ?></td>
                    <td><?= htmlspecialchars($p['sku']) ?></td>
                    <td><?= date("Y-m-d H:i", strtotime($p['created_at'])) ?></td>
                    <td class="text-center">
                        <div class="actions">
                            <button class="btn view-btn" aria-label="View details of <?= htmlspecialchars($p['name']) ?>"
                                onclick='openProductModal(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn edit-btn" aria-label="Edit <?= htmlspecialchars($p['name']) ?>">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?= $p['id'] ?>" class="btn delete-btn" aria-label="Delete <?= htmlspecialchars($p['name']) ?>"
                                onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="9" class="no-data">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalName" style="display:none;">
        <div class="modal-content">
            <button class="modal-close-btn" aria-label="Close product details" onclick="closeProductModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-body">
                <img id="modalImage" src="" alt="Product Image" class="modal-image" tabindex="0" onclick="openImageView()" />
                <div class="modal-details">
                    <h2 id="modalName"></h2>
                    <p id="modalDesc" class="desc-font"></p>
                    <p><strong>Price:</strong> $<span id="modalPrice"></span></p>
                    <p><strong>Quantity:</strong> <span id="modalQty"></span></p>
                    <p><strong>SKU:</strong> <span id="modalSKU"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Image View Modal -->
    <div id="imageViewModal" class="modal" role="dialog" aria-modal="true" style="display:none;" onclick="closeImageView(event)">
        <button class="modal-close-btn close-image" aria-label="Close image view" onclick="closeImageView(event)">
            <i class="fas fa-times"></i>
        </button>
        <img id="largeImage" src="" alt="Large product image view" />
    </div>

</div>

<?php include '../includes/footer.php'; ?>

<script>
const searchInput = document.getElementById('searchInput');
const productTable = document.getElementById('productTable');
const tbodyRows = productTable.tBodies[0].rows;

searchInput.addEventListener('input', () => {
    filterTable(searchInput.value);
});


function filterTable(query) {
    const q = query.trim().toLowerCase();
    for (let row of tbodyRows) {
        const id = row.getAttribute('data-id');
        const name = row.getAttribute('data-name');
        const sku = row.getAttribute('data-sku');

        if (
            id.includes(q) ||
            name.includes(q) ||
            sku.includes(q)
        ) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// Modal logic
const productModal = document.getElementById('productModal');
const modalImage = document.getElementById('modalImage');
const modalName = document.getElementById('modalName');
const modalDesc = document.getElementById('modalDesc');
const modalPrice = document.getElementById('modalPrice');
const modalQty = document.getElementById('modalQty');
const modalSKU = document.getElementById('modalSKU');

const imageViewModal = document.getElementById('imageViewModal');
const largeImage = document.getElementById('largeImage');

function openProductModal(product) {
    modalImage.src = '../assets/images/' + product.image;
    modalImage.alt = product.name + " image";
    modalName.textContent = product.name;
    modalDesc.textContent = product.description;
    modalPrice.textContent = parseFloat(product.price).toFixed(2);
    modalQty.textContent = product.quantity;
    modalSKU.textContent = product.sku;
    productModal.style.display = 'flex';
    // Focus modal for accessibility
    modalName.focus();
}

function closeProductModal() {
    productModal.style.display = 'none';
}

function openImageView() {
    largeImage.src = modalImage.src;
    largeImage.alt = modalImage.alt;
    imageViewModal.style.display = 'flex';
}

function closeImageView(event) {
    if (!event || event.target === imageViewModal || event.target.classList.contains('close-image')) {
        imageViewModal.style.display = 'none';
    }
}
</script>
