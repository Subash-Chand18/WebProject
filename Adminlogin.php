<?php
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Still MD5, though consider password_hash() in future

    $con = mysqli_connect("localhost", "root", "", "EClothingStore");

    if ($con) {
        $email_safe = mysqli_real_escape_string($con, $email);
        $sql = "SELECT * FROM user WHERE email='$email_safe' AND password='$password' AND deleted_at IS NULL";
        $res = mysqli_query($con, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            $user = mysqli_fetch_assoc($res);
            $_SESSION['email'] = $user['email'];
            $_SESSION['admin_name'] = $user['name'];

            if (!empty($_POST['remember'])) {
                setcookie("email", $email, time() + (86400 * 30), "/");
            } else {
                setcookie("email", "", time() - 3600, "/");
            }

            header("Location: Admindashboard.php");
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
    <title>Admin Login - E Clothing Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="wrapper">
        <form action="Adminlogin.php" method="POST">
            <h1>E-Clothing Store</h1>
            <div class="input-box">
                <i class='bx bxs-user'></i>
                <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : ''; ?>" />
            </div>
            <div class="input-box password-box">
                <i class='bx bxs-lock-alt'></i>
                <input type="password" name="password" id="password" placeholder="Password" required />
                <i class='bx bx-show toggle-icon' id="togglePassword"></i>
            </div>
            <div class="remember-forgot">
                <label>
                    <input type="checkbox" name="remember" <?php echo isset($_COOKIE['email']) ? 'checked' : ''; ?> /> Remember me
                </label>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
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
