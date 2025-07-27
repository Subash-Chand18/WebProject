<?php
include '../includes/header.php';

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = (int) $_POST['order_id'];
    $new_status = mysqli_real_escape_string($con, $_POST['order_status']);
    $confirmed_cancel = isset($_POST['confirmed_cancel']) ? (int) $_POST['confirmed_cancel'] : 0;

    if ($new_status === 'Cancelled' && $confirmed_cancel === 1) {
        $get_products = "SELECT product_id, quantity FROM orderdetail WHERE order_id = $order_id";
        $res_products = mysqli_query($con, $get_products);
        while ($row = mysqli_fetch_assoc($res_products)) {
            $product_id = $row['product_id'];
            $qty = $row['quantity'];
            mysqli_query($con, "UPDATE product SET quantity = quantity + $qty WHERE id = $product_id");
        }
        mysqli_query($con, "UPDATE orders SET order_status='Cancelled', deleted_at = NOW() WHERE id = $order_id");
    } else {
        mysqli_query($con, "UPDATE orders SET order_status='$new_status' WHERE id=$order_id");
    }

    header("Location: Adminorders.php");
    exit();
}

$sql = "
    SELECT 
        o.id AS order_id, o.name AS order_name, o.order_status, o.payment_method, o.created_at,o.shipping_charge,
        u.name AS user_name, u.email AS user_email,
        GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names,
        GROUP_CONCAT(DISTINCT p.image SEPARATOR ', ') AS product_images,
        SUM(od.quantity) AS total_quantity,
        SUM(od.unit_price * od.quantity)+o.shipping_charge AS total_price
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN orderdetail od ON od.order_id = o.id
    LEFT JOIN product p ON od.product_id = p.id
    WHERE o.deleted_at IS NULL
    GROUP BY o.id
    ORDER BY o.created_at ASC
";

$res = mysqli_query($con, $sql);
?>

<style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow-y: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 25px;
        border-radius: 10px;
        max-width: 600px;
        position: relative;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        animation: fadeIn 0.3s ease-in-out;
        text-align: center;
    }

    .modal-product-images img {
        width: 100px;
        height: auto;
        margin: 5px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Responsive for small screens */
    @media (max-width: 768px) {

        .table th,
        .table td {
            font-size: 12px;
            padding: 8px 10px;
        }

        .modal-content {
            width: 90%;
        }
    }

    /* Button Styling */
    .pagination-controls .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: #007BFF;
        color: #fff;
        border: none;
        font-size: 1.1rem;
        padding: 0.7rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
        font-weight: bold;
    }

    .pagination-controls .btn:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    .pagination-controls .btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    .pagination-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding: 0 2rem;
    }
</style>
<div class="dashboard-content">
    <header class="page-header center-content text-center">
        <h1><i class="fas fa-box"></i> Orders Management</h1>
    </header>

    <div class="search-wrapper">
        <input type="search" id="searchInput" placeholder="Search by ID, Product Name, customer names , ..."
            autocomplete="off" aria-label="Search orders" />
        <button type="button" class="page-close-btn" title="Back to Dashboard"
            onclick="window.location.href='Admindashboard.php'">&times;</button>
    </div>

    <div class="table-container">
        <table id="orderTable" class="user-table">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Order Date</th>
                    <th>Payment Method</th>
                    <th>Product Names</th>
                    <th>Shipping Charge</th>
                    <th>Total Qty</th>
                    <th>Total Price (Rs)</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res && mysqli_num_rows($res) > 0): $sn = 1 ?>
                    <?php while ($order = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><?= $order['order_id'] ?></td>
                            <td><?= htmlspecialchars($order['order_name']) ?: htmlspecialchars($order['user_name']) ?></td>
                            <td><?= htmlspecialchars($order['user_email']) ?></td>
                            <td><?= date('Y-m-d h:i A', strtotime($order['created_at'])) ?></td>
                            <td><?= htmlspecialchars($order['payment_method']) ?></td>
                            <td><?= htmlspecialchars($order['product_names']) ?></td>
                            <td><?= number_format($order['shipping_charge'], 2) ?></td>
                            <td><?= (int) $order['total_quantity'] ?></td>
                            <td><?= number_format($order['total_price'], 2) ?></td>
                            <td>
                                <form method="POST" action="Adminorders.php" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                    <select name="order_status" class="form-select status-select"
                                        onchange="confirmCancel(this)">
                                        <?php
                                        $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
                                        foreach ($statuses as $status):
                                            ?>
                                            <option value="<?= $status ?>" <?= ($order['order_status'] === $status) ? 'selected' : '' ?>><?= $status ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="confirmed_cancel" value="0">
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="actions">
                                    <button class="btn view-btn" onclick='openOrderModal(<?= json_encode($order) ?>)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="Orderdetailsdelete.php?id=<?= $order['order_id'] ?>" class="btn delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this Orders ?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- Stylish Pagination Controls -->
        <div class="pagination-controls">
            <button id="prevBtn" class="btn" disabled><i class="fas fa-arrow-left"></i> Previous</button>
            <button id="nextBtn" class="btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>
    <!-- Order View Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeOrderModal()">&times;</span>
            <h2 class="modal-title"><i class="fas fa-box"></i>Customers Order </h2>
            <p><strong>Customer:</strong> <span id="modalUserName"></span></p>
            <p><strong>Email Address:</strong> <span id="modalEmail"></span></p>
            <p><strong>Order Date:</strong> <span id="modalDate"></span></p>
            <div id="modalProducts"></div>
            <div class="modal-product-images" id="modalImages"></div>
            <p><strong>Total:</strong> Rs <span id="modalTotal"></span></p>
        </div>
    </div>
</div>

<script>
    function openOrderModal(order) {
        document.getElementById('modalUserName').innerText = order.user_name;
        document.getElementById('modalEmail').innerText = order.user_email;
        document.getElementById('modalDate').innerText = order.created_at;
        document.getElementById('modalTotal').innerText = parseFloat(order.total_price).toFixed(2);

        // Product names and quantity
        let productHTML = `
            <p><strong>Products:</strong> ${order.product_names}</p>
            <p><strong>Total Quantity:</strong> ${order.total_quantity}</p>
        `;
        document.getElementById('modalProducts').innerHTML = productHTML;

        // Product images
        const imageContainer = document.getElementById('modalImages');
        imageContainer.innerHTML = '';
        if (order.product_images) {
            const images = order.product_images.split(', ');
            images.forEach(img => {
                const imagePath = `../assets/images/${img}`;
                imageContainer.innerHTML += `<img src=\"${imagePath}\" alt=\"Product Image\" />`;
            });
        }

        document.getElementById('orderModal').style.display = 'block';
    }

    function closeOrderModal() {
        document.getElementById('orderModal').style.display = 'none';
    }

</script>

<!-- Bootstrap JS First -->
<script src="../design-assets/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->
<script>
    // Search Functionality
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#orderTable tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Confirm Cancel
    function confirmCancel(selectElement) {
        const form = selectElement.closest('form');
        const selectedValue = selectElement.value;

        if (selectedValue === 'Cancelled') {
            if (confirm("Do you want to cancel and hide this order?")) {
                form.querySelector('input[name="confirmed_cancel"]').value = '1';
                form.submit();
            } else {
                form.querySelector('input[name="confirmed_cancel"]').value = '0';
                Array.from(selectElement.options).forEach((opt, i) => {
                    if (opt.defaultSelected) selectElement.selectedIndex = i;
                });
            }
        } else {
            form.querySelector('input[name="confirmed_cancel"]').value = '0';
            form.submit();
        }
    }

    // Pagination Logic
    const rows = Array.from(document.querySelectorAll('#orderTable tbody tr'));
    const rowsPerPage = 10;
    let currentPage = 1;
    const totalPages = Math.ceil(rows.length / rowsPerPage);

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    function displayPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        rows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? '' : 'none';
        });
        prevBtn.disabled = page === 1;
        nextBtn.disabled = page === totalPages;
    }

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            displayPage(currentPage);
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            displayPage(currentPage);
        }
    });

    // Initialize first page
    displayPage(currentPage);
</script>

<?php include '../includes/footer.php'; ?>