<?php
include("includes/header.php");
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");

$sql = "SELECT p.*, c.name AS category_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.id 
        WHERE p.deleted_at IS NULL 
        ORDER BY p.id DESC";
$result = mysqli_query($con, $sql);
?>
<br><br><br><br><br>

<div class="container-fluid clothing py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="mb-4">All Products</h1>
            <div class="input-group mb-3 w-50 mx-auto">
                <input type="text" id="searchBox"
                    placeholder="Search by name, ID, price, category, or price range like 100-500,.."
                    class="form-control">
                <button class="btn btn-outline-secondary" type="button" id="clearBtn">X</button>
            </div>
        </div>

        <div class="row g-4" id="productList">
            <?php while ($product = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 col-lg-4 col-xl-3 product-card" data-id="<?= $product['id']; ?>"
                    data-name="<?= strtolower($product['name']); ?>" data-sku="<?= strtolower($product['sku']); ?>"
                    data-description="<?= strtolower($product['description']); ?>" data-price="<?= $product['price']; ?>"
                    data-category="<?= strtolower($product['category_name']); ?>">

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

                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <span class="text-dark fs-5 fw-bold">
                                    Rs <?= number_format($product['price'], 2); ?>
                                </span>

                                <?php if ($product['quantity'] > 0): ?>
                                    <a href="add_to_cart.php?id=<?= $product['id']; ?>&quantity=1"
                                        class="btn border border-secondary rounded-pill px-3 text-primary">
                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                    </a>
                                <?php else: ?>
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
    </div>
</div>

<?php include("includes/footer.php"); ?>
<script>
    document.getElementById('searchBox').addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        const productCards = document.querySelectorAll('.product-card');

        let minPrice = null, maxPrice = null;
        const priceRangeMatch = query.match(/^(\d+)\s*-\s*(\d+)$/);

        if (priceRangeMatch) {
            minPrice = parseFloat(priceRangeMatch[1]);
            maxPrice = parseFloat(priceRangeMatch[2]);
        }

        productCards.forEach(card => {
            const id = card.dataset.id;
            const name = card.dataset.name;
            const sku = card.dataset.sku;
            const description = card.dataset.description;
            const price = parseFloat(card.dataset.price);
            const category = card.dataset.category;

            let matches = false;

            if (priceRangeMatch) {
                if (price >= minPrice && price <= maxPrice) {
                    matches = true;
                }
            } else {
                if (
                    id.includes(query) ||
                    name.includes(query) ||
                    sku.includes(query) ||
                    description.includes(query) ||
                    category.includes(query) ||
                    price.toString().includes(query)
                ) {
                    matches = true;
                }
            }

            card.style.display = matches ? '' : 'none';
        });
    });
    // Clear Button functionality
    document.getElementById('clearBtn').addEventListener('click', function () {
        document.getElementById('searchBox').value = '';
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.style.display = '';
        });
    });

</script>