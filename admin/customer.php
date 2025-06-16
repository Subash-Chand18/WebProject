<?php
session_start();

// ✅ Check admin login
if (!isset($_SESSION['admin_email']) || $_SESSION['admin_type'] !== 'admin') {
    header("Location: Adminlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Fetch all non-admin users who are not soft deleted (deleted_at IS NULL)
$sql = "SELECT id, name, email, image, created_at FROM user WHERE user_type != 'admin' AND deleted_at IS NULL ORDER BY created_at ASC";
$res = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Customer Details</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
                $sn = 1;
                while ($user = mysqli_fetch_assoc($res)): 
            ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td>
                        <?php
                        // Construct image path
                        $imagePath = __DIR__ . '/../design-assets/img/' . $user['image'];
                        $imageUrl = '../design-assets/img/' . htmlspecialchars($user['image']);

                        // Use default image if no image or file missing
                        if (!empty($user['image']) && file_exists($imagePath)) {
                            $imgSrc = $imageUrl;
                        } else {
                            $imgSrc = '../design-assets/img/default-profile.png';
                        }
                        ?>
                        <img src="<?= $imgSrc ?>" alt="Profile Image">
                    </td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= date('Y-m-d h:i A', strtotime($user['created_at'])) ?></td>
                </tr>
            <?php 
                endwhile; 
            else: 
            ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">No customers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
