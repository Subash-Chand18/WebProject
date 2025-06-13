<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "EClothingStore");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';
$success = '';

if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password'])); // Use stronger hashing in production
    $confirm_password = md5(trim($_POST['confirm_password']));
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $country = trim($_POST['country']);
    $phone = trim($_POST['phone_no']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $checkQuery = "SELECT id FROM user WHERE email = '$email' AND deleted_at IS NULL";
        $checkResult = mysqli_query($con, $checkQuery);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $error = "Email is already registered!";
        } else {
            $insertQuery = "INSERT INTO user (name, email, password, address, city, country, phone_no) 
                            VALUES ('$name', '$email', '$password', '$address', '$city', '$country', '$phone')";
            if (mysqli_query($con, $insertQuery)) {
                $success = "Registration successful! You can now <a href='Userlogin.php'>login</a>.";
            } else {
                $error = "Registration failed: " . mysqli_error($con);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Signup</title>
    <link rel="stylesheet" href="../assets/css/signup-style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <form class="authForm" action="" method="post" novalidate>
        <h2>User Signup</h2>

        <?php if ($error): ?>
            <p style="color: red; text-align: center; font-weight: 600;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p style="color: green; text-align: center; font-weight: 600;"><?php echo $success; ?></p>
        <?php endif; ?>

        <div class="form-group">
            <input type="text" name="name" id="name" placeholder=" " required
                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            <label for="name">Full Name</label>
        </div>

        <div class="form-group">
            <input type="email" name="email" id="email" placeholder=" " required
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <label for="email">Email</label>
        </div>

        <div class="form-group" style="position: relative;">
            <input type="password" name="password" id="password" placeholder=" " required>
            <label for="password">Password</label>
            <i class="bx bx-show toggle-icon" id="togglePassword" aria-label="Toggle password visibility" role="button" tabindex="0"></i>
        </div>

        <div class="form-group" style="position: relative;">
            <input type="password" name="confirm_password" id="confirm_password" placeholder=" " required>
            <label for="confirm_password">Confirm Password</label>
            <i class="bx bx-show toggle-icon" id="toggleConfirmPassword" aria-label="Toggle password visibility" role="button" tabindex="0"></i>
        </div>

        <div class="form-group">
            <input type="text" name="address" id="address" placeholder=" " required
                value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
            <label for="address">Address</label>
        </div>

        <div class="form-group">
            <input type="text" name="city" id="city" placeholder=" " required
                value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
            <label for="city">City</label>
        </div>

        <div class="form-group">
            <input type="text" name="country" id="country" placeholder=" " required
                value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>">
            <label for="country">Country</label>
        </div>

        <div class="form-group">
            <input type="tel" name="phone_no" id="phone_no" placeholder=" " required
                value="<?php echo isset($_POST['phone_no']) ? htmlspecialchars($_POST['phone_no']) : ''; ?>">
            <label for="phone_no">Phone Number</label>
        </div>

        <div class="button-group">
            <button type="submit" name="signup">Sign Up</button>
            <a href="Userlogin.php" class="cancel-btn"
                style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; margin-top: 10px;">Back to Login</a>
        </div>
    </form>

    <script>
        function toggleVisibility(toggleId, inputId) {
            const toggleIcon = document.getElementById(toggleId);
            const inputField = document.getElementById(inputId);

            toggleIcon.addEventListener("click", () => {
                const type = inputField.type === "password" ? "text" : "password";
                inputField.type = type;
                toggleIcon.classList.toggle('bx-show');
                toggleIcon.classList.toggle('bx-hide');
            });

            toggleIcon.addEventListener("keydown", e => {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    toggleIcon.click();
                }
            });
        }

        toggleVisibility('togglePassword', 'password');
        toggleVisibility('toggleConfirmPassword', 'confirm_password');
    </script>
</body>

</html>
