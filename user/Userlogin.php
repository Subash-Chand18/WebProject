<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password'])); // In production, use password_hash()

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password' AND deleted_at IS NULL";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['user_image'] = $user['image'];

        if (!empty($_POST['remember'])) {
            setcookie("email", $email, time() + (86400 * 30), "/"); // 30 days
        } else {
            setcookie("email", "", time() - 3600, "/");
        }

        // Default redirect to  shop
        $redirectUrl = '../index.php';

        // Optional: Dynamic redirect if set and safe
        if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
            $allowedPages = ['chackout.php', 'index.php']; // Allowed pages
            $requestedPage = basename($_GET['redirect']);

            if (in_array($requestedPage, $allowedPages)) {
                // Adjust folder path if needed
                $redirectUrl = ($requestedPage == 'chackout.php') ? 'chackout.php' : '../index.php';
            }
        }

        header("Location: " . $redirectUrl);
        exit();
    } else {
        $error = "Invalid User credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>User Login - E-Clothing Store</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
    <form class="authForm" action="" method="post" novalidate>
        <h2>E-Clothing Store</h2>

        <?php if ($error): ?>
            <p style="color: red; font-weight: 600;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <div class="form-group">
            <input type="email" name="email" id="email" placeholder=" " required
                value="<?php echo isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : ''; ?>" />
            <label for="email">Email</label>
        </div>

        <div class="form-group" style="position: relative;">
            <!-- Your original icon structure -->
            <i class="bx bx-show toggle-icon" id="togglePassword"></i>
            <input type="password" name="password" id="password" placeholder=" " required />
            <label for="password">Password</label>
        </div>

        <div class="form-options">
            <label>
                <input type="checkbox" name="remember" <?php if (isset($_COOKIE['email'])) echo 'checked'; ?> />
                Remember me
            </label>
            <a href="passwordforgot.php">Forgot Password?</a>
        </div>

        <div class="button-group">
            <button type="submit" name="login">Login</button>
        </div>

        <p>
            Don't have an account? <a href="Signup.php">Sign Up</a>
        </p>
    </form>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", () => {
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;

            togglePassword.classList.toggle('bx-show');
            togglePassword.classList.toggle('bx-hide');
        });

        togglePassword.addEventListener("keydown", e => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                togglePassword.click();
            }
        });
    </script>
</body>

</html>