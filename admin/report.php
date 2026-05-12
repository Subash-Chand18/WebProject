<?php
include '../includes/header.php';
// Pagination Setup
$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$where = "WHERE o.order_status = 'delivered'";

// Date Range Check
$dateRangePattern = '/^(\d{4}-\d{2}-\d{2})\s*-\s*(\d{4}-\d{2}-\d{2})$/';

if (preg_match($dateRangePattern, $search, $matches)) {
    $start_date = $matches[1] . " 00:00:00";
    $end_date = $matches[2] . " 23:59:59";
    $where .= " AND o.created_at BETWEEN '$start_date' AND '$end_date'";
} elseif ($search !== '') {
    $where .= " AND (p.name LIKE '%$search%' OR p.id LIKE '%$search%' OR u.name LIKE '%$search%' OR u.email LIKE '%$search%')";
}

// Main Query
$query = "SELECT 
    p.id AS product_id,
    p.name AS product_name,
    DATE(o.created_at) AS Ordered_Date,
    SUM(od.quantity) AS total_quantity,
    SUM(od.quantity * od.unit_price) + SUM(o.shipping_charge) AS total_sold_price
FROM orderdetail od
JOIN product p ON od.product_id = p.id
JOIN orders o ON od.order_id = o.id 
JOIN users u ON o.user_id = u.id
$where
GROUP BY p.id, p.name, Ordered_Date
ORDER BY Ordered_Date DESC, total_quantity DESC
LIMIT $offset, $limit";

$result = mysqli_query($con, $query);

// Count Records
$countQuery = "SELECT COUNT(DISTINCT p.id) AS total 
               FROM orderdetail od
               JOIN product p ON od.product_id = p.id
               JOIN orders o ON od.order_id = o.id
               JOIN users u ON o.user_id = u.id
               $where";

$countResult = mysqli_query($con, $countQuery);
$totalRecords = mysqli_fetch_assoc($countResult)['total'] ?? 0;
$totalPages = ceil($totalRecords / $limit);

// Frequent Users
$freqUserQuery = "SELECT 
    u.id, 
    u.name, 
    u.email,
    u.created_at,
    COUNT(o.id) AS total_orders,
    SUM(od.unit_price * od.quantity) + SUM(o.shipping_charge) AS total_price
FROM orders o 
JOIN users u ON o.user_id = u.id 
LEFT JOIN shipping s ON s.order_id = o.id
LEFT JOIN orderdetail od ON od.order_id = o.id
LEFT JOIN product p ON od.product_id = p.id
WHERE o.order_status = 'delivered' 
GROUP BY u.id, u.name, u.email, u.created_at
HAVING total_orders >= 2 
ORDER BY total_orders DESC";

$freqUserResult = mysqli_query($con, $freqUserQuery);
?>

<style>
    .pagination a {
        margin: 0 5px;
        text-decoration: none;
    }

    .pagination a.active {
        font-weight: bold;
        color: blue;
    }

    .filter-form input {
        margin-right: 10px;
        padding: 5px;
    }

    /* Form wrapper keeps input and buttons inline */
    .search-wrapper {
        margin: 20px auto;
        width: 80%;
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
    }

    /* Search Input */
    .search-wrapper input[type="search"] {
        flex-grow: 1;
        padding: 10px 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        outline-offset: 2px;
        transition: border-color 0.3s ease;
        min-width: 0;
    }

    .search-wrapper input[type="search"]:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Hidden Search Button (doesn't affect layout) */
    .search-wrapper button[title="Search"] {
        display: none;
    }

    /* Close Button Styling */
    .page-close-btn {
        background: none;
        color: #666;
        font-size: 28px;
        padding: 0 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: color 0.3s ease, background-color 0.3s ease;
        height: 40px;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-close-btn:hover {
        color: #000;
        background-color: rgba(0, 0, 0, 0.05);
    }
</style>

<div class="dashboard-content">
    <header class="page-header center-content text-center">
        <h1><i class="fas fa-file-alt"></i> Product Sold Report (Delivered)</h1>
    </header>

    <form method="GET" class="search-wrapper">
        <input type="search" name="search" id="searchInput"
            placeholder="Search by Product, User, or Date Range (YYYY-MM-DD - YYYY-MM-DD)"
            value="<?= htmlspecialchars($search) ?>" autocomplete="off" />

        <!-- Hidden Search Button to trigger form submission with Enter -->
        <button type="submit" title="Search" style="display: none;"></button>

        <button type="button" class="page-close-btn" title="Back to Dashboard"
            onclick="window.location.href='Admindashboard.php'">&times;</button>
    </form>



    <div class="table-container" role="region">
        <table id="reportTable" class="report-table product-table">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Total Quantity Sold</th>
                    <th>Total Amount (Including Shipping)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0):
                    $sn = 1;
                    while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><?= $row['product_id'] ?></td>
                            <td><?= $row['product_name'] ?></td>
                            <td><?= $row['total_quantity'] ?></td>
                            <td><?= number_format($row['total_sold_price'], 2) ?></td>
                            <td><?= date('Y-m-d h:i A', strtotime($row['Ordered_Date'])) ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="6">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

    <header class="page-header center-content text-center">
        <h1><i class="fas fa-users"></i> Frequently Ordering Users</h1>
    </header>

    <div class="table-container" role="region">
        <table id="reportTable" class="report-table user-table">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Number of Orders</th>
                    <th>Total Price</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($freqUserResult) > 0):
                    $sn = 1;
                    while ($user = mysqli_fetch_assoc($freqUserResult)): ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><?= $user['id'] ?></td>
                            <td><?= $user['name'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['total_orders'] ?></td>
                            <td><?= number_format($user['total_price'], 2) ?></td>
                            <td><?= date('Y-m-d h:i A', strtotime($user['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="7">No frequent users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Search Functionality
        document.getElementById('searchInput').addEventListener('keyup', function (e) {
            if (e.key === 'Enter') return; // Avoid double action when pressing Enter
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#reportTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });

    </script>

    <?php include '../includes/footer.php'; ?>