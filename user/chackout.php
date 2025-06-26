<?php
session_start();


$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$query = "SELECT name, email FROM users WHERE id = $user_id";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $full_name = trim($user['name']);
    $user_email = $user['email'];

    // Separate first name and last name
    $name_parts = explode(' ', $full_name, 2);
    $first_name = $name_parts[0];
    $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
} else {
    $first_name = $last_name = $user_email = '';
}


$cart_empty_message = "";
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $cart_empty_message = "Please first add any products to the cart.";
}

if (!isset($_SESSION['user_id'])) {
    header("Location: Userlogin.php?redirect=chackout.php");
    exit;
}

// Calculate subtotal
$grandTotal = 0;
if (empty($cart_empty_message)) {
    foreach ($_SESSION['cart'] as $item) {
        $grandTotal += $item['price'] * $item['quantity'];
    }
}
?>
<?php include("includes/header.php"); ?>
<style>
    body {
        background-color: #fff;
        /* Page background */
    }

    .table thead {
        background-color: #e0f7fa;
        /* Light sky blue */
    }

    .table tbody tr:hover {
        background-color: #f1faff;
        /* Slight hover effect */
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .table th,
    .table td {
        vertical-align: middle !important;
        text-align: center;
    }

    .table th {
        color: #000;
        /* Optional: darken header text */
        font-weight: 600;
    }

    .table img {
        border: 2px solid #ccc;
        border-radius: 50%;
    }
</style>

<br><br><br>

<!-- Checkout Page Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <h1 class="mb-4">Billing Details</h1>

        <?php if ($cart_empty_message): ?>
            <div style="color: red; font-weight: bold; font-size: 1.5rem; text-align: center; margin: 50px 0;">
                <?= htmlspecialchars($cart_empty_message) ?>
            </div>
        <?php else: ?>
            <form action="place_order.php" method="POST" enctype="multipart/form-data">
                <div class="row g-5">
                    <!-- Billing Details Form -->
                    <div class="col-md-12 col-lg-6 col-xl-7">
                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                <div class="form-item w-100">
                                    <label class="form-label my-3" for="last_name">First Name<sup>*</sup></label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" required
                                        readonly value="<?= htmlspecialchars($first_name) ?>">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="form-item w-100">
                                    <label class="form-label my-3" for="last_name">Last Name<sup>*</sup></label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" required
                                        readonly value="<?= htmlspecialchars($last_name) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-item">
                            <label class="form-label my-3" for="billing_address">Billing Address<sup>*</sup></label>
                            <input type="text" id="billing_address" name="billing_address" class="form-control" required>
                        </div>

                        <div class="form-item">
                            <label class="form-label my-3" for="shipping_address">Shipping Address<sup>*</sup></label>
                            <input type="text" id="shipping_address" name="shipping_address" class="form-control" required>
                        </div>

                        <div class="form-item">
                            <label class="form-label my-3" for="country">Country<sup>*</sup></label>
                            <input type="text" id="country" name="country" class="form-control" required>
                        </div>

                        <div class="form-item">
                            <label class="form-label my-3" for="mobile">Phone Number<sup>*</sup></label>
                            <input type="tel" id="mobile" name="mobile" class="form-control" required
                                pattern="^\+?[0-9]{8,15}$"
                                title="Enter a valid phone number (10 to 15 digits, with optional + at start)">

                        </div>

                        <div class="form-item">
                            <label class="form-label my-3" for="email">Email Address<sup>*</sup></label>
                            <input type="email" id="email" name="email" class="form-control" required readonly
                                value="<?= htmlspecialchars($user_email) ?>">
                        </div>

                        <hr>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-md-12 col-lg-6 col-xl-5">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['cart'] as $item):
                                        $itemTotal = $item['price'] * $item['quantity'];
                                        ?>
                                        <tr>
                                            <th scope="row" class="align-middle">
                                                <img src="../assets/images/<?= htmlspecialchars($item['image']) ?>"
                                                    class="img-fluid rounded-circle" style="width: 90px; height: 90px;"
                                                    alt="<?= htmlspecialchars($item['name']) ?>">
                                            </th>
                                            <td class="py-5 align-middle"><?= htmlspecialchars($item['name']) ?></td>
                                            <td class="py-5 align-middle">Rs <?= number_format($item['price'], 2) ?></td>
                                            <td class="py-5 align-middle"><?= (int) $item['quantity'] ?></td>
                                            <td class="py-5 align-middle">Rs <?= number_format($itemTotal, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- Subtotal -->
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="py-5">
                                            <p class="mb-0 text-dark py-3">Subtotal: </p>
                                        </td>
                                        <td class="py-5">
                                            <div class="py-3 border-bottom border-top">
                                                <p class="mb-0 text-dark" id="subtotal">Rs
                                                    <?= number_format($grandTotal, 2) ?>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Shipping Options -->
                                    <tr>
                                        <td colspan="2" class="py-5">
                                            <p class="mb-0 text-dark py-4"> Shipping </p>
                                        </td>
                                        <td colspan="3" class="py-5">
                                            <div class="form-check text-start mb-2">
                                                <input type="radio" class="form-check-input bg-primary border-0"
                                                    name="shipping_charge" id="Shipping-500" value="500" required
                                                    onchange="updateGrandTotal(500)">
                                                <label class="form-check-label" for="Shipping-500">Flat rate: Rs 500</label>
                                            </div>
                                            <div class="form-check text-start">
                                                <input type="radio" class="form-check-input bg-primary border-0"
                                                    name="shipping_charge" id="Shipping-300" value="300" required
                                                    onchange="updateGrandTotal(300)">
                                                <label class="form-check-label" for="Shipping-300">Local Pickup: Rs
                                                    300</label>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Grand Total -->
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="py-5">
                                            <p class="mb-0 text-dark text-uppercase py-3">Grand Total: </p>
                                        </td>
                                        <td class="py-5">
                                            <div class="py-3 border-bottom border-top">
                                                <p class="mb-0 text-dark fw-bold" id="grand-total">Rs
                                                    <?= number_format($grandTotal, 2) ?></p>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                        <!-- Payment Method -->
                        <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                            <div class="col-12">
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input bg-primary border-0" id="PaymentCOD"
                                        name="payment_method" value="Cash on Delivery" required>
                                    <label class="form-check-label" for="PaymentCOD">Cash On Delivery</label>
                                </div>
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                            <button type="submit"
                                class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">Place
                                Order</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- JavaScript to update Grand Total -->
            <script>
                let baseTotal = <?= $grandTotal ?>;

                function updateGrandTotal(shippingCharge) {
                    let grandTotal = baseTotal + shippingCharge;
                    document.getElementById('grand-total').innerText = 'Rs ' + grandTotal.toFixed(2);
                }
            </script>
        <?php endif; ?>
    </div>
</div>
<!-- Checkout Page End -->
<script>
    document.querySelector("form").addEventListener("submit", function (e) {
        const phone = document.getElementById("mobile").value;
        const phonePattern = /^\+?[0-9]{10,15}$/;

        if (!phonePattern.test(phone)) {
            alert("Please enter a valid phone number (10–15 digits, optionally starting with +).");
            e.preventDefault(); // Stops form from submitting
        }
    });
</script>


<?php include("includes/footer.php"); ?>