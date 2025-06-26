<?php
// File: cart.php
session_start();
include("includes/header.php");


$cart = $_SESSION['cart'] ?? [];

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$products = [];
if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $sql = "SELECT id, name, price, image, quantity AS stock FROM product WHERE id IN ($ids) AND deleted_at IS NULL";
    $res = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $products[$row['id']] = $row;
    }
}
mysqli_close($con);
?>
<br><br><br><br><br>
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h2>
    <?php if (empty($cart)): ?>
        <p class="text-muted">Your cart is empty. Redirecting to homepage...</p>
        <script>
            setTimeout(() => window.location.href = '../index.php', 1000);
        </script>
    <?php else: ?>
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php $grandTotal = 0; ?>
                <?php foreach ($cart as $id => $item): ?>
                    <?php
                    if (!isset($products[$id])) continue;
                    $product = $products[$id];
                    $price = (float) $product['price'];
                    $quantity = (int) $item['quantity'];
                    $stock = (int) $product['stock'];

                    if ($quantity > $stock) {
                        $_SESSION['cart'][$id]['quantity'] = $stock;
                        $quantity = $stock;
                    }
                    $total = $price * $quantity;
                    $grandTotal += $total;
                    ?>
                    <tr>
                        <td><img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" style="max-width: 100px;"></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>Rs <?php echo number_format($price, 2); ?></td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <button class="btn btn-sm btn-outline-secondary me-2 btn-decrease" data-id="<?php echo $id; ?>">-</button>
                                <span id="qty-<?php echo $id; ?>"><?php echo $quantity; ?></span>
                                <button class="btn btn-sm btn-outline-secondary ms-2 btn-increase" data-id="<?php echo $id; ?>">+</button>
                            </div>
                            <div class="text-muted small mt-1">(Stock: <?php echo $stock; ?>)</div>
                        </td>
                        <td id="total-<?php echo $id; ?>">Rs <?php echo number_format($total, 2); ?></td>
                        <td><a href="remove_from_cart.php?id=<?php echo $id; ?>" class="btn btn-danger btn-sm">X</a></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" class="text-end fw-bold">Grand Total</td>
                    <td colspan="2" class="fw-bold text-success" id="grand-total">Rs <?php echo number_format($grandTotal, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-between">
            <a href="../index.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'chackout.php' : 'userlogin.php'; ?>" class="btn btn-success">Proceed to Checkout <i class="fas fa-arrow-right"></i></a>
        </div>
    <?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {
        function updateQuantity(productId, action) {
            $.ajax({
                url: 'update_quantity.php',
                type: 'POST',
                data: { id: productId, action: action },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        $('#qty-' + productId).text(response.quantity);
                        $('#total-' + productId).text("Rs " + response.item_total);
                        $('#grand-total').text("Rs " + response.grand_total);
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert("Failed to update quantity");
                }
            });
        }

        $('.btn-increase').click(function () {
            const id = $(this).data('id');
            updateQuantity(id, 'increase');
        });

        $('.btn-decrease').click(function () {
            const id = $(this).data('id');
            updateQuantity(id, 'decrease');
        });
    });
</script>
<?php include("includes/footer.php"); ?>