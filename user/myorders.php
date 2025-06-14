<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Userlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the logged-in user
$orderQuery = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$orderResult = mysqli_query($con, $orderQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            padding: 40px 20px;
            background-color: #f0f4ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .order-card {
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(50, 50, 93, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            background: #fff;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(50, 50, 93, 0.15), 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 30px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px 12px 0 0;
            font-size: 1.1rem;
        }

        .badge-status {
            padding: 8px 16px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 50px;
            letter-spacing: 0.04em;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: background-color 0.3s ease;
        }

        .badge-status.pending {
            background-color: #fbbf24;
            color: #663c00;
        }

        .badge-status.completed {
            background-color: #34d399;
            color: #064e3b;
        }

        .order-body {
            padding: 25px 30px;
            background-color: white;
            border-radius: 0 0 12px 12px;
            border-top: 2px solid #667eea;
            box-shadow: inset 0 2px 5px rgba(102, 126, 234, 0.15);
            animation: fadeIn 0.3s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        table.table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 20px rgb(0 0 0 / 0.05);
        }

        table thead {
            background: #667eea;
            color: white;
            text-transform: uppercase;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.05em;
        }

        table thead th {
            border: none;
        }

        table tbody tr:hover {
            background-color: #e7ebff;
        }

        table tbody td {
            vertical-align: middle;
            font-weight: 500;
            color: #555;
        }

        table tbody td img {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
        }

        .grand-total {
            font-size: 1.25rem;
            font-weight: 700;
            color: #4c51bf;
            margin-top: 15px;
            text-align: right;
            user-select: none;
        }

        .order-header::after {
            content: '\25BC';
            font-size: 1.25rem;
            transition: transform 0.3s ease;
            display: inline-block;
            margin-left: 15px;
            transform-origin: center;
        }

        .order-header[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        @media (max-width: 576px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                font-size: 1rem;
            }

            .grand-total {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="text-center mb-5 fw-bold text-primary">My Orders</h1>

        <?php if (mysqli_num_rows($orderResult) > 0): ?>
            <?php $count = 1; ?>
            <?php while ($order = mysqli_fetch_assoc($orderResult)): ?>
                <div class="order-card mb-4" role="region" aria-labelledby="order-header-<?= $count ?>">
                    <button
                        class="order-header"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#orderDetails<?= $count ?>"
                        aria-expanded="false"
                        aria-controls="orderDetails<?= $count ?>"
                        id="order-header-<?= $count ?>"
                    >
                        <div>
                            <span>Order ID: <strong><?= $order['id'] ?></strong></span> |
                            <span>Date: <strong><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></strong></span>
                        </div>
                        <div>
                            <span class="badge-status <?= strtolower($order['order_status']) === 'pending' ? 'pending' : 'completed' ?>">
                                <?= htmlspecialchars($order['order_status']) ?>
                            </span>
                        </div>
                    </button>

                    <div class="collapse" id="orderDetails<?= $count ?>">
                        <div class="order-body">
                            <?php
                            $order_id = $order['id'];

                            // Fetch product details including shipping_charge
                            $detailsQuery = "SELECT od.*, p.name, p.image FROM orderdetail od
                                             JOIN product p ON od.product_id = p.id
                                             WHERE od.order_id = '$order_id'";
                            $detailsResult = mysqli_query($con, $detailsQuery);
                            ?>

                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Unit Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Shipping Charge</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $grandTotal = 0;
                                    while ($detail = mysqli_fetch_assoc($detailsResult)):
                                        $itemTotal = ($detail['unit_price'] * $detail['quantity']) + $detail['shipping_charge'];
                                        $grandTotal += $itemTotal;
                                    ?>
                                        <tr>
                                            <td><img src="../assets/images/<?= htmlspecialchars($detail['image']) ?>" alt="<?= htmlspecialchars($detail['name']) ?>" width="70" height="70" /></td>
                                            <td><?= htmlspecialchars($detail['name']) ?></td>
                                            <td>Rs <?= number_format($detail['unit_price'], 2) ?></td>
                                            <td><?= (int)$detail['quantity'] ?></td>
                                            <td>Rs <?= number_format($detail['shipping_charge'], 2) ?></td>
                                            <td>Rs <?= number_format($itemTotal, 2) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>

                            <div class="grand-total">
                                Grand Total: Rs <?= number_format($grandTotal, 2) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $count++; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center my-5">
                <h3 class="text-muted">😞 You have no orders yet.</h3>
                <p>Start shopping now to place your first order!</p>
                <a href="../index.php" class="btn btn-success btn-lg mt-3">Shop Now</a>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-primary btn-lg px-5">Continue Shopping</a>
        </div>
    </div>

</body>

</html>
