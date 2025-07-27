<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$errors = [];
$success = '';

if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password_raw = trim($_POST['password']);
    $confirm_password_raw = trim($_POST['confirm_password']);

    $upload_dir = "../assets/images/";
    $image = "avatar.jpg";

    // Image upload validation
    if (!empty($_FILES['userfile']['name'])) {
        $image = basename($_FILES['userfile']['name']);
        $upload_file = $upload_dir . $image;

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg', 'image/avif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if ($_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            $errors['image'] = "File upload error: " . $_FILES['userfile']['error'];
        } elseif (!in_array($_FILES['userfile']['type'], $allowed_types)) {
            $errors['image'] = "Only image files (JPG, JPEG, PNG, GIF, WEBP, AVIF) are allowed.";
        } elseif ($_FILES['userfile']['size'] > $max_size) {
            $errors['image'] = "Image size should not exceed 2MB.";
        } else {
            move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file);
        }
    }

    // Server-side validation
    if (empty($name)) {
        $errors['name'] = "Full name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors['name'] = "Name should contain letters and spaces only.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($password_raw)) {
        $errors['password'] = "Password is required.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/", $password_raw)) {
        $errors['password'] = "Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, and one special character.";
    }

    if (empty($confirm_password_raw)) {
        $errors['confirm_password'] = "Confirm your password.";
    } elseif ($password_raw !== $confirm_password_raw) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $password = md5($password_raw); // Use password_hash() for production

        $checkQuery = "SELECT id FROM users WHERE email = '$email' AND deleted_at IS NULL";
        $checkResult = mysqli_query($con, $checkQuery);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $errors['email'] = "Email is already registered.";
        } else {
            $insertQuery = "INSERT INTO users (name, email, password, image) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password, $image);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! You can now <a href='Userlogin.php?email=" . urlencode($email) . "'>login</a>.";
                unset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['confirm_password']);
            } else {
                $errors['form'] = "Registration failed: " . mysqli_error($con);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>User Signup</title>
    <link rel="stylesheet" href="../assets/css/signup.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
    <form class="authForm" action="" method="post" enctype="multipart/form-data">
        <h2>User Signup</h2>

        <?php if (!empty($errors['form'])): ?>
            <p style="color: red; text-align: center;"><?php echo $errors['form']; ?></p>
        <?php elseif ($success): ?>
            <p style="color: green; text-align: center;"><?php echo $success; ?></p>
        <?php endif; ?>

        <div class="form-group">
            <input type="text" name="name" id="name" placeholder=" " required
                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />
            <label for="name">Full Name</label>
            <?php if (!empty($errors['name'])): ?>
                <small style="color: red;"><?php echo $errors['name']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <input type="email" name="email" id="email" placeholder=" " required
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
            <label for="email">Email</label>
            <?php if (!empty($errors['email'])): ?>
                <small style="color: red;"><?php echo $errors['email']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group" style="position: relative;">
            <input type="password" name="password" id="password" placeholder=" " required
                value="<?php echo (!isset($errors['password']) && isset($_POST['password'])) ? htmlspecialchars($_POST['password']) : ''; ?>" />
            <label for="password">Password</label>
            <i class="bx bx-show toggle-icon" id="togglePassword" aria-label="Toggle password visibility" role="button"
                tabindex="0"></i>
            <?php if (!empty($errors['password'])): ?>
                <small style="color: red;"><?php echo $errors['password']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group" style="position: relative;">
            <input type="password" name="confirm_password" id="confirm_password" placeholder=" " required
                value="<?php echo (!isset($errors['confirm_password']) && isset($_POST['confirm_password'])) ? htmlspecialchars($_POST['confirm_password']) : ''; ?>" />

            <label for="confirm_password">Confirm Password</label>
            <i class="bx bx-show toggle-icon" id="toggleConfirmPassword" aria-label="Toggle password visibility"
                role="button" tabindex="0"></i>
            <?php if (!empty($errors['confirm_password'])): ?>
                <small style="color: red;"><?php echo $errors['confirm_password']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="userfile"></label>
            <input type="file" name="userfile" id="userfile" accept="image/*" />
            <?php if (!empty($errors['image'])): ?>
                <small style="color: red;"><?php echo $errors['image']; ?></small>
            <?php endif; ?>
        </div>

        <div class="button-group">
            <button type="submit" name="signup">Sign Up</button>
            <a href="Userlogin.php" id="backToLogin" class="cancel-btn">Back to Login</a>
        </div>
    </form>

    <script>
        function toggleVisibility(toggleId, inputId) {
            const toggleIcon = document.getElementById(toggleId);
            const inputField = document.getElementById(inputId);

            toggleIcon.addEventListener("click", () => {
                const type = inputField.type === "password" ? "text" : "password";
                inputField.type = type;
                toggleIcon.classList.toggle("bx-show");
                toggleIcon.classList.toggle("bx-hide");
            });

            toggleIcon.addEventListener("keydown", (e) => {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    toggleIcon.click();
                }
            });
        }
        toggleVisibility("togglePassword", "password");
        toggleVisibility("toggleConfirmPassword", "confirm_password");

        document.getElementById("backToLogin").addEventListener("click", function (e) {
            e.preventDefault();
            const emailValue = document.getElementById("email").value.trim();
            let loginUrl = "Userlogin.php";
            if (emailValue) {
                loginUrl += "?email=" + encodeURIComponent(emailValue);
            }
            window.location.href = loginUrl;
        });

    </script>

</body>

</html>