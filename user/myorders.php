<?php
session_start();

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Userlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

// Fetch orders for this user
$orderQuery = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$orderResult = mysqli_query($con, $orderQuery);
?>

<?php include("includes/header.php"); ?>

<br><br><br><br><br><br><br><br>
<!-- order start -->
        <div class="container">
                <h1>My Orders</h1>

                <div class="order-details">
                    <h2>Order Details</h2>
                </div>

                <?php if (mysqli_num_rows($orderResult) > 0): ?>
                    <?php $count = 1; ?>
                    <?php while ($order = mysqli_fetch_assoc($orderResult)): ?>
                        <div class="order-card">
                            <button
                                class="order-header"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#orderDetails<?= $count ?>"
                                aria-expanded="false"
                                aria-controls="orderDetails<?= $count ?>"
                            >
                                <div class="order-info">
                                    <span>Order ID: <strong><?= htmlspecialchars($order['id']) ?></strong></span>
                                    <span>Date: <strong><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></strong></span>
                                    <span class="badge-status <?= strtolower($order['order_status']) === 'pending' ? 'pending' : 'completed' ?>">
                                        <?= htmlspecialchars($order['order_status']) ?>
                                    </span>
                                </div>
                            </button>

                            <div class="collapse order-body" id="orderDetails<?= $count ?>">
                                <?php
                                $order_id = $order['id'];

                                $detailsQuery = "SELECT od.*, p.name, p.image FROM orderdetail od
                                                JOIN product p ON od.product_id = p.id
                                                WHERE od.order_id = '$order_id'";
                                $detailsResult = mysqli_query($con, $detailsQuery);

                                $subtotal = 0;
                                $orderDetails = [];

                                while ($detail = mysqli_fetch_assoc($detailsResult)) {
                                    $itemTotal = $detail['unit_price'] * $detail['quantity'];
                                    $subtotal += $itemTotal;
                                    $orderDetails[] = $detail;
                                }

                                $totalShipping = $order['shipping_charge'];  // from orders table
                                $grandTotal = $subtotal + $totalShipping;
                                ?>

                                <div class="order-table-wrapper">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th scope="col">Product</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Unit Price</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orderDetails as $detail): ?>
                                                <tr>
                                                    <td>
                                                        <img
                                                            src="../assets/images/<?= htmlspecialchars($detail['image']) ?>"
                                                            alt="<?= htmlspecialchars($detail['name']) ?>"
                                                            style="max-width: 70px; max-height: 70px;"
                                                        />
                                                    </td>
                                                    <td><?= htmlspecialchars($detail['name']) ?></td>
                                                    <td>Rs <?= number_format($detail['unit_price'], 2) ?></td>
                                                    <td><?= (int)$detail['quantity'] ?></td>
                                                    <td>Rs <?= number_format($detail['unit_price'] * $detail['quantity'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="summary-container" role="region" aria-label="Order Summary" style="margin-top: 15px;">
                                    <div class="summary-item" tabindex="0">Shipping Charge: Rs <?= number_format($totalShipping, 2) ?></div>
                                    <div class="summary-item" tabindex="0"><strong>Grand Total: Rs <?= number_format($grandTotal, 2) ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <?php $count++; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- No Orders Message Start -->
                    <div class="success-container-wrapper" style="text-align:center; margin-top: 0px;">
                        <div class="success-container">
                            <i class="fas fa-shopping-cart mb-3 animate__animated animate__bounceIn" style="font-size: 60px; color: #777;"></i>
                            <h1 class="animate__animated animate__fadeInDown">No Orders Yet 😞</h1>
                            <p class="animate__animated animate__fadeInUp">Start shopping now to place your first order!</p>
                            <div class="mt-4">
                                <a href="../index.php" class="animate__animated animate__fadeInLeft btn btn-primary">Shop Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- No Orders Message End -->
                <?php endif; ?>
        </div>
        <!-- order end -->
<?php include("includes/footer.php"); ?>
