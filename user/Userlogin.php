

<?php
session_start();
$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password'])); // MD5 hashing (consider bcrypt for real-world apps)

    // Database connection
    $con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");

    if ($con) {
        // Use prepared statement to prevent SQL injection
        $stmt = $con->prepare("SELECT * FROM user WHERE email = ? AND password = ? AND deleted_at IS NULL");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $_SESSION['email'] = $email;

            if (!empty($_POST['remember'])) {
                setcookie("email", $email, time() + (86400 * 30), "/"); // 30 days
            } else {
                setcookie("email", "", time() - 3600, "/"); // delete cookie
            }

            header("Location: ../index.php");
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Database connection failed!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E Clothing Store | Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<div class="wrapper">
    <form action="" method="post">
        <h1>Login</h1>

        <!-- Display error if any -->
        <?php if (!empty($error)) : ?>
            <p style="color: red; text-align: center; margin-bottom: 10px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="input-box"> 
            <i class='bx bxs-user'></i>
            <input type="email" placeholder="Email" name="email" 
                   value="<?php echo isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : ''; ?>" 
                   required>
        </div>
        <div class="input-box">
            <i class='bx bxs-lock-alt'></i>
            <input type="password" placeholder="Password" name="password" required>  
        </div>

        <div class="remember-forgot">
            <label><input type="checkbox" name="remember" <?php if (isset($_COOKIE['email'])) echo 'checked'; ?>> Remember me</label>
            <a href="passwordreset.php">Forgot password?</a>
        </div>

        <button type="submit" class="btn" name="login">Login</button><br><br>

        <div class="Signup-link">
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        </div>
    </form>
</div>
</body>
</html>
