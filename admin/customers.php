<?php
include '../includes/header.php';

$sql = "SELECT id, name, email, image, created_at FROM user WHERE user_type != 'admin' ORDER BY created_at ASC";
$res = mysqli_query($con, $sql);
?>

    <div class="table-container">
        <h1><i class="fas fa-users"></i> Customer Details</h1>
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>User ID</th>
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
                            $imagePath = '../design-assets/img/' . $user['image'];
                            $imgSrc = (!empty($user['image']) && file_exists($imagePath)) 
                                        ? $imagePath 
                                        : '../design-assets/img/default-profile.png';
                            ?>
                            <img src="<?= $imgSrc ?>" class="profile-img" alt="Profile">
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
    </div>
  <?php  include '../includes/footer.php';?>