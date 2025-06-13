<?php
session_start();

// DB connection
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");

// Handle registration
if (isset($_POST['submit'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);
    $phone = trim($_POST['phone']);
    $country = trim($_POST['country']);
    $city = trim($_POST['city']);
    $address = trim($_POST['address']);

    // Password match check
    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = md5($password); // use bcrypt in real-world

        // Check if email exists
        $check = mysqli_query($con, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already exists!";
        } else {
            // Insert user
            $query = "INSERT INTO user (first_name, last_name, email, password, phone, country, city, address)
                      VALUES ('$first_name', '$last_name','$email', '$hashed_password', '$phone', '$country', '$city','$address')";
            $result = mysqli_query($con, $query);

            if ($result) {
                $_SESSION['email'] = $email;
                header("Location: Userlogin.php");
                exit();
            } else {
                $error = "Failed to register. Please try again.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../assets/css/signup.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <form action="" method="post">
            <h1>Sign Up</h1>

            <!-- Error Message -->
            <?php if (!empty($error)) : ?>
                <p style="color: red; text-align:center;"><?php echo $error; ?></p>
            <?php endif; ?>

            <div class="input-box"><i class='bx bxs-user'></i><input type="text" name="first_name" placeholder="First Name" required></div>
            <div class="input-box"><i class='bx bxs-user'></i><input type="text" name="last_name" placeholder="Last Name" required></div>
            <div class="input-box"><i class='bx bxs-user'></i><input type="text" name="company_name" placeholder="Company Name (optional)"></div>
            <div class="input-box"><i class='bx bxs-map'></i><input type="text" name="country" placeholder="Country" required></div>
            <div class="input-box"><i class='bx bxs-city'></i><input type="text" name="city" placeholder="City" required></div>
            <div class="input-box"><i class='bx bxs-location-plus'></i><input type="text" name="zip_code" placeholder="Zip Code" required></div>
            <div class="input-box"><i class='bx bxs-phone'></i><input type="text" name="phone" placeholder="Phone Number" required></div>
            <div class="input-box"><i class='bx bxs-home'></i><input type="text" name="address" placeholder="Address" required></div>
            <div class="input-box"><i class='bx bxs-envelope'></i><input type="email" name="email" placeholder="Email" required></div>
            <div class="input-box"><i class='bx bxs-lock-alt'></i><input type="password" name="password" placeholder="Password" required></div>
            <div class="input-box"><i class='bx bxs-lock'></i><input type="password" name="confirm" placeholder="Confirm Password" required></div>

            <button type="submit" class="btn" name="submit">Sign Up</button>

            <div class="login-link">
                <p>Already have an account? <a href="Userlogin.php">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
