<?php
include '../includes/header.php';

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch existing settings
$sql = "SELECT * FROM store_settings LIMIT 1";
$result = mysqli_query($con, $sql);
$setting = mysqli_fetch_assoc($result);

$upload_dir = '../assets/images/';
$default_logo = 'groupdiscuss.png';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $store_name = trim(mysqli_real_escape_string($con, $_POST['store_name']));
    $contact_number = trim(mysqli_real_escape_string($con, $_POST['contact_number']));
    $store_email = trim(mysqli_real_escape_string($con, $_POST['store_email']));
    $store_address = trim(mysqli_real_escape_string($con, $_POST['store_address']));
    $store_information = trim(mysqli_real_escape_string($con, $_POST['store_information']));
    $established_date = trim(mysqli_real_escape_string($con, $_POST['established_date']));

    // Server-side validation
    if (!preg_match('/^\+?[0-9]{10,15}$/', $contact_number)) {
        $error_message = "Contact number must contain only numbers, 10 to 15 digits, and optional + sign.";
    } elseif (!filter_var($store_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email address.";
    } else {
        // Handle logo upload
        $store_logo = $setting['store_logo'] ?? $default_logo;
        if (isset($_FILES['store_logo']) && $_FILES['store_logo']['name'] != '') {
            $file_name = basename($_FILES['store_logo']['name']);
            $file_tmp = $_FILES['store_logo']['tmp_name'];
            $file_type = $_FILES['store_logo']['type'];
            $file_size = $_FILES['store_logo']['size'];

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg', 'image/avif'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($file_type, $allowed_types)) {
                $error_message = "Only image files (jpg, jpeg, png, gif, webp, avif) are allowed.";
            } elseif ($file_size > $max_size) {
                $error_message = "File size should not exceed 2MB.";
            } elseif (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                $store_logo = $file_name;
            } else {
                $error_message = "Error uploading the logo.";
            }
        }

        // If no errors, insert or update
        if (empty($error_message)) {
            if ($setting) {
                $sql_update = "UPDATE store_settings SET 
                            store_name = ?, 
                            store_logo = ?, 
                            contact_number = ?, 
                            store_email = ?, 
                            store_address = ?, 
                            store_information = ?, 
                            established_date = ? 
                            WHERE id = ?";
                $stmt = mysqli_prepare($con, $sql_update);
                mysqli_stmt_bind_param($stmt, "sssssssi", $store_name, $store_logo, $contact_number, $store_email, $store_address, $store_information, $established_date, $setting['id']);

                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Settings updated successfully.";
                    $setting = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM store_settings LIMIT 1"));
                } else {
                    $error_message = "Error updating settings: " . mysqli_error($con);
                }
                mysqli_stmt_close($stmt);
            } else {
                $sql_insert = "INSERT INTO store_settings 
                            (store_name, store_logo, contact_number, store_email, store_address, store_information, established_date) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $sql_insert);
                mysqli_stmt_bind_param($stmt, "sssssss", $store_name, $store_logo, $contact_number, $store_email, $store_address, $store_information, $established_date);

                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Settings added successfully.";
                    $setting = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM store_settings LIMIT 1"));
                } else {
                    $error_message = "Error adding settings: " . mysqli_error($con);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>


<link rel="stylesheet" href="../assets/css/add_category.css">

<section class="add-category-container">
    <form method="POST" enctype="multipart/form-data" id="settingsForm">
        <button type="button" class="close-btn" onclick="window.location.href='../admin/Admindashboard.php'">&times;</button>

        <h2><i class="fas fa-cogs"></i> Store Settings</h2>

        <!-- Success & Error Messages -->
        <?php if (!empty($success_message)): ?>
            <div style="color: green; margin-bottom:10px;"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div style="color: red; margin-bottom:10px;"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <div id="clientError" style="color: red; margin-bottom:10px;"></div>

        <div class="form-group">
            <input type="text" name="store_name" placeholder=" " required value="<?php echo $setting['store_name'] ?? ''; ?>">
            <label><i class="fas fa-store"></i> Store Name</label>
        </div>

        <div class="form-group">
            <input type="file" name="store_logo">
            <?php if (!empty($setting['store_logo'])): ?>
                <br><img src="../assets/images/<?php echo $setting['store_logo']; ?>" width="100" alt="Logo">
            <?php endif; ?>
            <label><i class="fas fa-image"></i> Store Logo</label>
        </div>

        <div class="form-group">
            <input type="text" name="contact_number" id="contact_number" placeholder=" " value="<?php echo $setting['contact_number'] ?? ''; ?>">
            <label><i class="fas fa-phone"></i> Contact Number</label>
        </div>

        <div class="form-group">
            <input type="email" name="store_email" id="store_email" placeholder=" " value="<?php echo $setting['store_email'] ?? ''; ?>">
            <label><i class="fas fa-envelope"></i> Store Email</label>
        </div>

        <div class="form-group">
            <input type="text" name="store_address" placeholder=" " value="<?php echo $setting['store_address'] ?? ''; ?>">
            <label><i class="fas fa-map-marker-alt"></i> Store Address</label>
        </div>

        <div class="form-group">
            <textarea name="store_information" placeholder=" "><?php echo $setting['store_information'] ?? ''; ?></textarea>
            <label><i class="fas fa-info-circle"></i> Store Information</label>
        </div>

        <div class="form-group">
            <input type="date" name="established_date" placeholder=" " value="<?php echo $setting['established_date'] ?? ''; ?>">
            <label><i class="fas fa-calendar-alt"></i> Established Date</label>
        </div>

        <div class="button-group">
            <?php if ($setting): ?>
                <button type="submit" onclick="return validateForm()"><i class="fas fa-sync-alt"></i> Update Settings</button>
            <?php else: ?>
                <button type="submit" onclick="return validateForm()"><i class="fas fa-plus-circle"></i> Add Settings</button>
            <?php endif; ?>
            <button type="button" class="cancel-btn" onclick="clearForm()"><i class="fas fa-eraser"></i> Clear Form</button>
        </div>
    </form>
</section>

<script>
function validateForm() {
    const contact = document.getElementById('contact_number').value.trim();
    const email = document.getElementById('store_email').value.trim();
    const errorDiv = document.getElementById('clientError');
    errorDiv.innerHTML = '';

    const contactRegex = /^\+?[0-9]{10,15}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!contactRegex.test(contact)) {
        errorDiv.innerHTML = "Contact number must contain only numbers, 10 to 15 digits, and optional + sign.";
        return false;
    }
    if (!emailRegex.test(email)) {
        errorDiv.innerHTML = "Invalid email format.";
        return false;
    }
    return true;
}

function clearForm() {
    document.getElementById('settingsForm').reset();
    document.getElementById('clientError').innerHTML = '';
}
</script>

<?php include '../includes/footer.php'; ?>
