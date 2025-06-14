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
            background-color: #f9fbff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 20px;
        }

        h1 {
            font-weight: 700;
            color: #334155;
            margin-bottom: 2rem;
            text-align: center;
        }

        .order-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgb(100 116 139 / 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .order-card:hover {
            box-shadow: 0 20px 40px rgb(100 116 139 / 0.15);
        }

        .order-header {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            color: white;
            padding: 20px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
            border-radius: 12px 12px 0 0;
        }

        .order-header[aria-expanded="true"]::after {
            content: '▲';
            font-size: 1.2rem;
        }

        .order-header::after {
            content: '▼';
            font-size: 1.2rem;
        }

        .badge-status {
            padding: 6px 16px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.12);
            user-select: none;
        }

        .badge-status.pending {
            background-color: #facc15;
            color: #78350f;
        }

        .badge-status.completed {
            background-color: #22c55e;
            color: #14532d;
        }

        .order-body {
            padding: 25px 30px;
            animation: fadeInDown 0.3s ease forwards;
            background: #fefefe;
            border-top: 3px solid #4f46e5;
            border-radius: 0 0 12px 12px;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        thead tr {
            background: #4f46e5;
            color: white;
            text-transform: uppercase;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 12px;
        }

        thead th {
            padding: 14px 12px;
        }

        tbody tr {
            background: #eef2ff;
            box-shadow: 0 4px 8px rgb(79 70 229 / 0.1);
            transition: background-color 0.3s ease;
            border-radius: 10px;
        }

        tbody tr:hover {
            background-color: #dbeafe;
        }

        tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-weight: 600;
            color: #334155;
            border: none;
        }

        tbody td img {
            border-radius: 10px;
            box-shadow: 0 3px 10px rgb(0 0 0 / 0.15);
            width: 70px;
            height: 70px;
            object-fit: cover;
        }

        .summary-container {
            margin-top: 25px;
            background: #e0e7ff;
            border-radius: 12px;
            padding: 20px 30px;
            display: flex;
            justify-content: flex-end;
            gap: 40px;
            font-weight: 700;
            font-size: 1.15rem;
            color: #3730a3;
            box-shadow: 0 8px 20px rgb(99 102 241 / 0.2);
            user-select: none;
        }

        .summary-item {
            background: #4338ca;
            color: white;
            padding: 14px 30px;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgb(67 56 202 / 0.5);
            transition: background-color 0.3s ease;
        }

        .summary-item:hover {
            background-color: #3730a3;
        }

        @media (max-width: 576px) {
            .order-header {
                flex-direction: column;
                gap: 10px;
                font-size: 1rem;
            }

            .summary-container {
                flex-direction: column;
                align-items: flex-end;
                gap: 15px;
                font-size: 1.05rem;
            }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const headers = document.querySelectorAll('.order-header');
            headers.forEach(header => {
                header.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-bs-target').substring(1);
                    const content = document.getElementById(targetId);

                    const expanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !expanded);

                    if (content.classList.contains('show')) {
                        // Collapse
                        content.classList.remove('show');
                        content.style.height = null;
                    } else {
                        // Expand
                        content.classList.add('show');
                        content.style.height = content.scrollHeight + "px";
                    }
                });
            });
        });
    </script>
</head>

<body>

    <div class="container">
        <h1>My Orders</h1>

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
                        id="order-header-<?= $count ?>"
                    >
                        <div>
                            Order ID: <strong><?= $order['id'] ?></strong> |
                            Date: <strong><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></strong>
                        </div>
                        <div>
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
                        ?>

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
                                <?php
                                $subtotal = 0;
                                $totalShipping = 0;
                                while ($detail = mysqli_fetch_assoc($detailsResult)):
                                    $itemTotal = $detail['unit_price'] * $detail['quantity'];
                                    $subtotal += $itemTotal;
                                    $totalShipping += $detail['shipping_charge'];
                                ?>
                                    <tr>
                                        <td><img src="../assets/images/<?= htmlspecialchars($detail['image']) ?>" alt="<?= htmlspecialchars($detail['name']) ?>" /></td>
                                        <td><?= htmlspecialchars($detail['name']) ?></td>
                                        <td>Rs <?= number_format($detail['unit_price'], 2) ?></td>
                                        <td><?= (int)$detail['quantity'] ?></td>
                                        <td>Rs <?= number_format($itemTotal, 2) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                        <div class="summary-container" role="region" aria-label="Order Summary">
                            <div class="summary-item" tabindex="0">Shipping Charge: Rs <?= number_format($totalShipping, 2) ?></div>
                            <div class="summary-item" tabindex="0">Grand Total: Rs <?= number_format($subtotal + $totalShipping, 2) ?></div>
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
