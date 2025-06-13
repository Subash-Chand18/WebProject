<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$cart = $_SESSION['cart'] ?? [];

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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Your Shopping Cart</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
<link rel="stylesheet" href="assets/css/custom-style.css" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    const updateQueue = {};

    function updateQuantity(productId, action) {
        if (updateQueue[productId]) return;
        updateQueue[productId] = true;

        const qtyElem = $("#qty-" + productId);
        const totalElem = $("#total-" + productId);
        const grandTotalElem = $("#grand-total");

        $.ajax({
            type: "POST",
            url: "update_quantity.php",
            data: { id: productId, action: action },
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    qtyElem.text(response.quantity);
                    totalElem.text("Rs " + response.item_total);
                    grandTotalElem.text("Rs " + response.grand_total);
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function() {
                alert("Error communicating with server.");
            },
            complete: function() {
                updateQueue[productId] = false;
            }
        });
    }

    $(".btn-increase").click(function() {
        const productId = $(this).data("id");
        updateQuantity(productId, 'increase');
    });

    $(".btn-decrease").click(function() {
        const productId = $(this).data("id");
        updateQuantity(productId, 'decrease');
    });
});
</script>

</head>
<body>

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
        <?php 
        $grandTotal = 0;
        foreach ($cart as $id => $item):
            if (!isset($products[$id])) continue;

            $product = $products[$id];
            $price = (float)$product['price'];
            $quantity = (int)$item['quantity'];
            $stock = (int)$product['stock'];

            if ($quantity > $stock) {
                $_SESSION['cart'][$id]['quantity'] = $stock;
                $quantity = $stock;
            }

            $total = $price * $quantity;
            $grandTotal += $total;
        ?>
            <tr>
                <td>
                    <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="img-fluid" style="max-width: 100px;">
                </td>
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
                <td>
                    <a href="remove_from_cart.php?id=<?php echo $id; ?>" class="btn btn-danger btn-sm">X</a>
                </td>
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
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout <i class="fas fa-arrow-right"></i></a>
    </div>
<?php endif; ?>
</div>

</body>
</html>