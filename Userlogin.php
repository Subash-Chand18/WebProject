<?php
session_start(); // Start session to track user login

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash password (note: use stronger hashing in production)

    // Connect to database
    $con = mysqli_connect("localhost", "root", "", "EClothingStore");

    if ($con) {
        // Check user credentials and ensure account is not deleted
        $sql = "SELECT * FROM user WHERE email='$email' AND password='$password' AND deleted_at IS NULL";
        $res = mysqli_query($con, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            $_SESSION['email'] = $email; // Save login in session

            // Handle "Remember Me" using cookie
            if (!empty($_POST['remember'])) {
                setcookie("email", $email, time() + (86400 * 30), "/"); // Set for 30 days
            } else {
                setcookie("email", "", time() - 3600, "/"); // Clear cookie
            }

            header("Location: Userdashboard.php"); // Redirect to dashboard
            exit();
        } else {
            echo "<script>alert('Invalid email or password!');</script>";
        }
    } else {
        echo "<script>alert('Database connection failed!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E Clothing Store - Login</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="wrapper">
        <form action="Userlogin.php" method="POST">
            <h1>E-Clothing Store</h1>
            <div class="input-box">
                <i class='bx bxs-user'></i>
                <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>" />
            </div>
            <div class="input-box password-box">
                <i class='bx bxs-lock-alt'></i>
                <input type="password" name="password" id="password" placeholder="Password" required />
                <i class='bx bx-show toggle-icon' id="togglePassword"></i> <!-- Show/hide icon -->
            </div>
            <div class="remember-forgot">
                <label>
                    <input type="checkbox" name="remember" <?php echo isset($_COOKIE['email']) ? 'checked' : ''; ?> /> Remember me
                </label>
                <a href="passwordreset.php">Forgot password?</a>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
            <div class="signup-link">
                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
            </div>
            <div class="divider">
                <hr /><span>Or Login with</span><hr />
            </div>
            <button type="button" class="google-btn" onclick="alert('Button clicked!')">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/1200px-Google_%22G%22_logo.svg.png" alt="Google logo" width="20" />
                Sign in with Google
            </button>
        </form>
    </div>
    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", function () {
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;
            this.classList.toggle("bx-show");
            this.classList.toggle("bx-hide");
        });
    </script>
</body>
</html>
