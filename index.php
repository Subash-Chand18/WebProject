<?php
session_start();

// Handle login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash password with MD5

    // Connect to database
    $con = mysqli_connect("localhost", "root", "", "EClothingStore");

    if ($con) {
        $sql = "SELECT * FROM User WHERE Email='$email' AND Password='$password' AND Deleted_at IS NULL";
        $res = mysqli_query($con, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            $_SESSION['email'] = $email;
            header("Location: homepage.html");
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
        <form action="index.php" method="POST">
            <h1>E-Clothing Store</h1>
            <div class="input-box">
                <i class='bx bxs-user'></i>
                <input type="email" name="email" placeholder="Email" required />
            </div>
            <div class="input-box">
                <i class='bx bxs-lock-alt'></i>
                <input type="password" name="password" placeholder="Password" required />
            </div>
            <div class="remember-forgot">
                <label>
                    <input type="checkbox" name="remember" /> Remember me
                </label>
                <a href="passwordreset.html">Forgot password?</a>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
            <div class="signup-link">
                <p>Don't have an account? <a href="signup.html">Sign up</a></p>
            </div>
            <div class="divider">
                <hr /><span>Or Login with</span><hr />
            </div>
            <button type="button" class="google-btn" onclick="alert('Button clicked!')">
                <img
                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/1200px-Google_%22G%22_logo.svg.png"
                    alt="Google logo"
                    width="20"
                />
                Sign in with Google
            </button>
        </form>
    </div>

    <script>
        const rememberCheckbox = document.querySelector('input[name="remember"]');
        const passwordInput = document.querySelector('input[name="password"]');

        rememberCheckbox.addEventListener('change', function () {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
        
    </script>
</body>
</html>
