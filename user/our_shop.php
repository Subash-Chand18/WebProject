<?php
include("includes/header.php");
$con = mysqli_connect("localhost", "root", "", "EClothingStore");

// Handle search query
$search = '';
$where = '';

if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    $where = "WHERE 
        p.id LIKE '%$search%' OR
        p.name LIKE '%$search%' OR
        p.sku LIKE '%$search%' OR
        p.price LIKE '%$search%'";
}

// Get all products
$sql = "SELECT p.*, c.name AS category_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.id 
        $where
        ORDER BY p.id DESC";
$result = mysqli_query($con, $sql);
?>
<br><br><br><br><br>
<!-- Shop Page Products Section -->
<div class="container-fluid clothing py-5">
    <div class="container py-5">

        <div class="text-center mb-5">
            <h1 class="mb-4">All Products</h1>
            <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                <input type="text" id="searchInput" placeholder="Search by ID, Name, SKU, or Price"
                    class="form-control w-50" style="max-width: 400px;">
                <button type="button" onclick="clearSearch()" class="btn btn-outline-secondary">Clear</button>
            </div>
        </div>

        <div class="row g-4" id="productList">
            <?php while ($product = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 col-lg-4 col-xl-3 product-card" data-id="<?= $product['id']; ?>"
                    data-name="<?= strtolower($product['name']); ?>" data-sku="<?= strtolower($product['sku']); ?>"
                    data-price="<?= $product['price']; ?>">

                    <div class="rounded position-relative clothing-item">
                        <a href="product_details.php?id=<?= $product['id']; ?>">
                            <img src="../assets/images/<?= htmlspecialchars($product['image']); ?>"
                                class="img-fluid w-100 rounded-top" alt="<?= htmlspecialchars($product['name']); ?>">
                        </a>

                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                            style="top: 10px; left: 10px;">
                            <?= htmlspecialchars($product['category_name']); ?>
                        </div>

                        <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                            <h5><?= htmlspecialchars($product['name']); ?></h5>
                            <p><?= htmlspecialchars($product['description']); ?></p>

                            <!-- Price and Add to Cart or Out of Stock -->
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <span class="text-dark fs-5 fw-bold">
                                    Rs <?= number_format($product['price'], 2); ?>
                                </span>

                                <?php if ($product['quantity'] > 0): ?>
                                    <!-- Active Add to Cart -->
                                    <a href="add_to_cart.php?id=<?= $product['id']; ?>&quantity=1"
                                        class="btn border border-secondary rounded-pill px-3 text-primary">
                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                    </a>
                                <?php else: ?>
                                    <!-- Disabled when out of stock -->
                                    <button class="btn border border-secondary rounded-pill px-3 text-danger" disabled>
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="text-center mt-4" id="noResults" style="display:none;">
            <p class="text-danger"> No matching products found.</p>
        </div>

    </div>
</div>

<?php include("includes/footer.php"); ?>
<script>
    const searchInput = document.getElementById("searchInput");
    const productCards = document.querySelectorAll(".product-card");
    const noResults = document.getElementById("noResults");

    searchInput.addEventListener("input", function () {
        const query = this.value.trim().toLowerCase();
        let matchCount = 0;

        productCards.forEach(card => {
            const id = card.dataset.id.toLowerCase();
            const name = card.dataset.name;
            const sku = card.dataset.sku;
            const price = card.dataset.price;

            const matches = id.includes(query) || name.includes(query) || sku.includes(query) || price.includes(query);

            if (matches) {
                card.style.display = "block";
                matchCount++;
            } else {
                card.style.display = "none";
            }
        });

        noResults.style.display = matchCount === 0 ? "block" : "none";
    });

    function clearSearch() {
        searchInput.value = "";
        productCards.forEach(card => card.style.display = "block");
        noResults.style.display = "none";
    }
</script>