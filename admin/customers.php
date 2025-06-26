<?php
include '../includes/header.php';

$sql = "SELECT id, name, email, image, created_at FROM users WHERE user_type != 'admin' AND deleted_at IS NULL ORDER BY created_at ASC";
$res = mysqli_query($con, $sql);
?>

<style>
.profile-img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #ccc;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
}

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

.modal-users-images img {
    width: 100px;
    height: auto;
    margin: 5px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

@media (max-width: 768px) {
    .table th, .table td {
        font-size: 12px;
        padding: 8px 10px;
    }
    .modal-content {
        width: 90%;
    }
}
</style>

<div class="dashboard-content">
    <header class="page-header center-content text-center">
        <h1><i class="fas fa-users"></i> Customer Details</h1>
    </header>

    <div class="search-wrapper">
        <input type="search" id="searchInput" placeholder="Search by ID, Username, Joined Date..." autocomplete="off" aria-label="Search orders" />
        <button type="button" class="page-close-btn" title="Back to Dashboard" onclick="window.location.href='Admindashboard.php'">&times;</button>
    </div>

    <div class="table-container" role="region" aria-live="polite" aria-relevant="all">
        <table id="usertTable" class="user-table" aria-label="List of users">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>User ID</th>
                    <th>Profile Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined Date</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res && mysqli_num_rows($res) > 0): $sn = 1; ?>
                    <?php while ($user = mysqli_fetch_assoc($res)): 
                        $jsonData = htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td>
                            <?php
                            $imagePath = '../design-assets/img/' . $user['image'];
                            $imgSrc = (!empty($user['image']) && file_exists($imagePath)) ? $imagePath : '../assets/images/avatar.jpg';
                            ?>
                            <img src="<?= $imgSrc ?>" class="profile-img" alt="Profile">
                        </td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= date('Y-m-d h:i A', strtotime($user['created_at'])) ?></td>
                        <td class="text-center">
                            <div class="actions">
                                <button class="btn view-btn" data-user='<?= $jsonData ?>' onclick="viewUserDetails(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="customerdelete.php?id=<?= $user['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this customer?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted">No customers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Customer View Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeOrderModal()">&times;</span>
        <h2 class="modal-title"><i class="fas fa-user"></i> Customer Details </h2>
        <p><strong>Customer Name:</strong> <span id="modalUserName"></span></p>
        <p><strong>Email Address:</strong> <span id="modalEmail"></span></p>
        <p><strong>Joined Date:</strong> <span id="modalDate"></span></p>
        <div class="modal-users-images" id="modalImages"></div>
    </div>
</div>

<script>
// View user details
function viewUserDetails(button) {
    const user = JSON.parse(button.getAttribute('data-user'));
    document.getElementById('modalUserName').innerText = user.name;
    document.getElementById('modalEmail').innerText = user.email;
    document.getElementById('modalDate').innerText = user.created_at;

    const imageContainer = document.getElementById('modalImages');
    imageContainer.innerHTML = '';

    if (user.image) {
        const imagePath = `../design-assets/img/${user.image}`;
        imageContainer.innerHTML = `<img src="${imagePath}" alt="User Image">`;
    } else {
        imageContainer.innerHTML = `<img src="../assets/images/avatar.jpg" alt="Default Image">`;
    }

    document.getElementById('userModal').style.display = 'block';
}

function closeOrderModal() {
    document.getElementById('userModal').style.display = 'none';
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#usertTable tbody tr');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<script src="../design-assets/js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
