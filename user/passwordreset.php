<?php
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = $_GET['id'];
require '../settings.php';

$error = '';

if (isset($_POST['submit'])) {
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    // Validate password strength
    if ($pass !== $cpass) {
        $error = "Passwords do not match!";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/", $pass)) {
        $error = "Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, and one special character.";
    } else {
        $sql = "UPDATE users SET password = '" . md5($pass) . "' WHERE id = " . intval($user_id);
        $res = mysqli_query($con, $sql);
        if ($res) {
            header("Location: Userlogin.php");
            exit();
        } else {
            $error = "Something went wrong while resetting the password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/passwordreset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="forgot-password-wrapper">
    <form action="" method="post" onsubmit="return validatePassword()">
        <h2>Reset Password</h2><br>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-group">
            <i class="bx bx-show toggle-icon" id="togglePassword"></i>
            <input type="password" name="pass" id="password" placeholder=" " required />
            <label for="password">New Password</label>
        </div>

        <div class="form-group">
            <i class="bx bx-show toggle-icon" id="toggleConfirmPassword"></i>
            <input type="password" name="cpass" id="cpassword" placeholder=" " required />
            <label for="cpassword">Confirm Password</label>
        </div>

        <button type="submit" class="btn" name="submit">Change Password</button>
    </form>
</div>

<script>
    // Toggle password visibility
    document.getElementById("togglePassword").addEventListener("click", () => {
        const input = document.getElementById("password");
        input.type = input.type === "password" ? "text" : "password";
        togglePassword.classList.toggle('bx-show');
        togglePassword.classList.toggle('bx-hide');
    });

    document.getElementById("toggleConfirmPassword").addEventListener("click", () => {
        const input = document.getElementById("cpassword");
        input.type = input.type === "password" ? "text" : "password";
        toggleConfirmPassword.classList.toggle('bx-show');
        toggleConfirmPassword.classList.toggle('bx-hide');
    });

    function validatePassword() {
        const password = document.getElementById("password").value;
        const confirm = document.getElementById("cpassword").value;

        const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/;

        if (password !== confirm) {
            alert("Passwords do not match!");
            return false;
        }

        if (!pattern.test(password)) {
            alert("Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, and one special character.");
            return false;
        }

        return true;
    }
</script>

</body>
</html>
