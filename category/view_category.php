<?php
session_start();

// ✅ Check admin login
if (!isset($_SESSION['admin_email']) || $_SESSION['admin_type'] !== 'admin') {
    header("Location: ../admin/Adminlogin.php");
    exit;
}

$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_email'];

// ✅ Database connection
$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Fetch categories where deleted_at IS NULL
$result = mysqli_query($con, "SELECT * FROM category WHERE deleted_at IS NULL ORDER BY created_at ASC");
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Categories - E-Clothing Store Admin</title>
    <link rel="stylesheet" href="../assets/css/Admindashboard.css" />
    <link rel="stylesheet" href="../assets/css/view_category.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <!-- Top Navigation -->
    <header class="topnav">
        <div class="logo">
            <i class="fas fa-tshirt"></i> E-Clothing Store
        </div>
        <nav class="topnav-menu">
            <a href="../admin/Admindashboard.php" class="nav-link active">Home</a>
            <a href="../admin/logout.php" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="welcome-msg">
            <i class="fas fa-user-circle"></i> Welcome, <strong><?php echo htmlspecialchars($adminName); ?></strong>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="../admin/Admindashboard.php" class="sidebar-link"><i class="fas fa-chart-line"></i> Dashboard</a></li>

            <!-- Products with dropdown -->
            <li class="dropdown">
                <a href="#" class="sidebar-link dropdown-toggle">
                    <i class="fas fa-box-open"></i> Products <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../product/add.php" class="sidebar-sublink">Add Product</a></li>
                    <li><a href="../product/view.php" class="sidebar-sublink">View Products</a></li>
                </ul>
            </li>

            <!-- Categories with dropdown -->
            <li class="dropdown open">
                <a href="#" class="sidebar-link dropdown-toggle active">
                    <i class="fas fa-tags"></i> Categories <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../category/add_category.php" class="sidebar-sublink">Add Category</a></li>
                    <li><a href="../category/view_category.php" class="sidebar-sublink">View Categories</a></li>
                </ul>
            </li>

            <li><a href="../admin/customer.php" class="sidebar-link"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="../admin/admin_orders.php" class="sidebar-link"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="../admin/orderdetail.php" class="sidebar-link"><i class="fas fa-clipboard-list"></i> Order Details</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <section class="view-category-container">
            <header class="page-header center-content text-center">
                <h1><i class="fas fa-list"></i> Categories</h1>
            </header>

            <!-- Search Bar -->
            <div class="search-wrapper">
                <input type="search" id="searchInput" placeholder="Search by ID or Name..." autocomplete="off" aria-label="Search categories" />
                <button type="button" class="page-close-btn" title="Back to Dashboard" aria-label="Close and return to dashboard" onclick="window.location.href='../admin/Admindashboard.php'">
                    &times;
                </button>
            </div>

            <div class="table-container" role="region" aria-live="polite" aria-relevant="all">
                <table id="categoryTable" class="category-table" aria-label="List of categories">
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($categories) > 0):
                            $sn = 1;
                            foreach ($categories as $cat): ?>
                            <tr data-id="<?= $cat['id'] ?>" data-name="<?= htmlspecialchars(strtolower($cat['name'])) ?>">
                                <td><?= $sn++ ?></td>
                                <td><?= htmlspecialchars($cat['name']) ?></td>
                                <td><?= htmlspecialchars(strlen($cat['description']) > 80 ? substr($cat['description'], 0, 80) . '...' : $cat['description']) ?></td>
                                <td><?= date("Y-m-d H:i", strtotime($cat['created_at'])) ?></td>
                                <td class="text-center">
                                    <div class="actions">
                                        <button class="btn view-btn" aria-label="View <?= htmlspecialchars($cat['name']) ?>"
                                            onclick='openCategoryModal(<?= json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <a href="edit_category.php?id=<?= $cat['id'] ?>" class="btn edit-btn" aria-label="Edit <?= htmlspecialchars($cat['name']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="delete_category.php?id=<?= $cat['id'] ?>" class="btn delete-btn" aria-label="Delete <?= htmlspecialchars($cat['name']) ?>"
                                           onclick="return confirm('Are you sure you want to delete this category?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="5" class="no-data">No categories found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Category Modal -->
        <div id="categoryModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalCategoryName" style="display:none;">
            <div class="modal-content">
                <button class="modal-close-btn" aria-label="Close category details" onclick="closeCategoryModal()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="modal-body">
                    <div class="modal-details">
                        <h2 id="modalCategoryName"></h2>
                        <p id="modalCategoryDesc" class="desc-font"></p>
                        <p><strong>Created At:</strong> <span id="modalCategoryCreated"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 E-Clothing Store. All Rights Reserved.</p>
    </footer>

    <!-- Scripts -->
    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const categoryTable = document.getElementById('categoryTable');
        const tbodyRows = categoryTable.tBodies[0].rows;

        searchInput.addEventListener('input', () => {
            filterTable(searchInput.value);
        });

        function filterTable(query) {
            const q = query.trim().toLowerCase();
            for (let row of tbodyRows) {
                const id = row.getAttribute('data-id');
                const name = row.getAttribute('data-name');

                if (id.includes(q) || name.includes(q)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        // Modal functionality
        const categoryModal = document.getElementById('categoryModal');
        const modalCategoryName = document.getElementById('modalCategoryName');
        const modalCategoryDesc = document.getElementById('modalCategoryDesc');
        const modalCategoryCreated = document.getElementById('modalCategoryCreated');

        function openCategoryModal(category) {
            modalCategoryName.textContent = category.name;
            modalCategoryDesc.textContent = category.description;
            modalCategoryCreated.textContent = category.created_at;
            categoryModal.style.display = 'flex';
            modalCategoryName.focus();
        }

        function closeCategoryModal() {
            categoryModal.style.display = 'none';
        }

        // Sidebar and topnav active toggle
        document.querySelectorAll('.dropdown-toggle').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('open');
            });
        });

        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            link.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-link').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });

        document.querySelectorAll('.topnav-menu .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                document.querySelectorAll('.topnav-menu .nav-link').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
