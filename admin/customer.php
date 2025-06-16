<?php
session_start();

// Check admin login
if (!isset($_SESSION['admin_type']) || $_SESSION['admin_type'] !== 'admin') {
    header("Location: Adminlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all non-admin users
$sql = "SELECT id, name, email, image, created_at FROM user WHERE user_type != 'admin' ORDER BY created_at ASC";
$res = mysqli_query($con, $sql);

// ✅ Include admin header
include '../includes/adminheader.php';
?>

<!-- Main Content -->
<div class="container p-4">
    <h1>Customer Details</h1>

    <table class="table table-bordered table-hover mt-4">
        <thead class="table-success">
            <tr>
                <th>S.N</th>
                <th>#User ID</th>
                <th>Profile Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Joined Date</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($res && mysqli_num_rows($res) > 0): 
                $sn = 1;  // Initialize serial number
                while ($user = mysqli_fetch_assoc($res)): 
            ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td>
                        <?php
                        // Build the server path to image file
                        $imageFile = __DIR__ . '/../design-assets/img/' . $user['image'];
                        // Web path to image for <img src="">
                        $imageUrl = '../design-assets/img/' . htmlspecialchars($user['image']);

                        if (!empty($user['image']) && file_exists($imageFile)) {
                            $imgSrc = $imageUrl;
                        } else {
                            // Default image path
                            $imgSrc = '../design-assets/img/default-profile.png';
                        }
                        ?>
                        <img src="<?= $imgSrc ?>" alt="Profile Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                    </td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= date('Y-m-d h:i A', strtotime($user['created_at'])) ?></td>
                </tr>
            <?php 
                endwhile; 
            else: 
            ?>
                <tr><td colspan="6" class="text-center text-muted">No customers found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/adminfooter.php'; ?>
