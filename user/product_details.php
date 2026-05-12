<?php
session_start();
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

        // Get average rating and total reviews
        $rating_sql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews 
                       FROM product_ratings 
                       WHERE product_id = $id";
        $rating_result = mysqli_query($con, $rating_sql);
        $rating_data = mysqli_fetch_assoc($rating_result);
        $avg_rating = round($rating_data['avg_rating'], 1);
        $total_reviews = $rating_data['total_reviews'];

        // Fetch all reviews
        $review_sql = "SELECT pr.rating, pr.review, u.name, pr.created_at 
                       FROM product_ratings pr 
                       JOIN users u ON pr.user_id = u.id 
                       WHERE pr.product_id = $id 
                       ORDER BY pr.created_at DESC";
        $reviews_result = mysqli_query($con, $review_sql);
    }
}

mysqli_close($con);
?>

<?php include("includes/header.php"); ?> 

<style>
    .star-rating {
        direction: rtl;
        unicode-bidi: bidi-override;
        display: inline-flex;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        font-size: 1.5rem;
        color: #ccc;
        cursor: pointer;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: gold;
    }
</style>

<br><br><br><br><br>
<div class="container product-container p-4">
    <?php if ($product): ?>
        <div class="row">
            <div class="col-md-6 text-center">
                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid product-image rounded">
            </div>
            <div class="col-md-6">
                <span class="category-badge"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h2 class="mt-3"><?php echo htmlspecialchars($product['name']); ?></h2>

                <!-- Rating Display -->
                <p class="mt-2">
                    Average Rating:
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?= $i <= round($avg_rating) ? 'text-warning' : 'text-secondary'; ?>"></i>
                    <?php endfor; ?>
                    (<?= $avg_rating; ?>/5 from <?= $total_reviews; ?> reviews)
                </p>

                <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
                <p><strong>Quantity Available:</strong> <?php echo htmlspecialchars($product['quantity']); ?></p>
                <p class="price-tag">RS <?php echo htmlspecialchars($product['price']); ?></p>
                <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-style">
                    <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                </a>
                <a href="../index.php" class="btn btn-outline-secondary btn-style ms-2">Back to Products</a>

                <!-- Rating Submission -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <hr>
                    <h5>Rate this product</h5>
                    <form action="submit_rating.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <div class="star-rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" id="star<?= $i; ?>" value="<?= $i; ?>" required>
                                <label for="star<?= $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                        <textarea name="review" rows="3" class="form-control mt-2" placeholder="Write your review (optional)"></textarea>
                        <button type="submit" class="btn btn-primary mt-2">Submit Rating</button>
                    </form>
                <?php else: ?>
                    <p class="mt-3 text-muted">Login to rate this product.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Show Reviews -->
        <?php if (mysqli_num_rows($reviews_result) > 0): ?>
            <hr>
            <h5>User Reviews</h5>
            <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                <div class="border p-2 rounded mb-2">
                    <strong><?= htmlspecialchars($review['name']); ?></strong>
                    <small class="text-muted"><?= $review['created_at']; ?></small><br>
                    <?= str_repeat("⭐", $review['rating']); ?><br>
                    <em><?= nl2br(htmlspecialchars($review['review'])); ?></em>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center">
            <h3>Product not found</h3>
            <a href="../index.php" class="btn btn-primary mt-3">Back to Home</a>
        </div>
    <?php endif; ?>
</div>

<?php include("includes/footer.php"); ?>
