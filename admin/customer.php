<?php
session_start();

// Check admin login
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Customer Details</title>
    <link href="../design-assets/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { padding: 20px; font-family: Arial, sans-serif; }
        table { margin-top: 20px; }
        th, td { vertical-align: middle !important; }
        img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
    </style>
</head>
<body>
    <h1>Customer Details</h1>
    <table class="table table-bordered table-hover">
        <thead class="table-success">
            <tr>
                <th>#User ID</th>
                <th>Profile Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Joined Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res && mysqli_num_rows($res) > 0): ?>
                <?php while ($user = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td>
                        <?php if (!empty($user['image'])): ?>
                            <img src="<?= htmlspecialchars($user['image']) ?>" alt="Profile Image">
                        <?php else: ?>
                            <img src="../design-assets/img/default-profile.png" alt="Default Image">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= date('Y-m-d h:i A', strtotime($user['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted">No customers found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script src="../design-assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
