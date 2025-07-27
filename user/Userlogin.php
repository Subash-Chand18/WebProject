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
            setcookie("email", $email, time() + (86400 * 30), "/");
        } else {
            setcookie("email", "", time() - 3600, "/");
        }

        $redirectUrl = '../index.php';

        if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
            $allowedPages = ['chackout.php', 'index.php'];
            $requestedPage = basename($_GET['redirect']);

            if (in_array($requestedPage, $allowedPages)) {
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
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        .divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: #888;
    margin: 10px 0; /* Reduced space */
}
.divider hr {
    flex: 1;
    border: none;
    border-top: 1px solid #ccc;
    margin: 0 10px;
}
.divider span {
    font-size: 14px;
}
.google-btn {
    z-index: 10;
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ccc;
    border-radius: 40px;
    padding: 10px;
    color: #555;
    font-size: 18px;
    font-weight: 500;
    background-color: white;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;
}
.google-btn img {
    width: 20px;
    height: 20px;
    margin-right: 10px;
    object-fit: contain;
    flex-shrink: 0;
}
.google-btn:hover {
    background-color: rgb(247, 247, 247);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.google-btn:active {
    transform: translateY(0px);
    box-shadow: none;
}

    </style>
</head>

<body>
    <form class="authForm" action="" method="post" novalidate>
        <h2>E-Clothing Store</h2>
        <?php if ($error): ?>
            <p style="color: red; font-weight: 600;"> <?php echo htmlspecialchars($error); ?> </p>
        <?php endif; ?>
        <div class="form-group">
            <input type="email" name="email" id="email" placeholder=" " required
                value="<?php echo isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : ''; ?>" />
            <label for="email">Email</label>
        </div>
        <div class="form-group" style="position: relative;">
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
        <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        <div class="divider">
            <hr><span>Or Login with</span><hr>
        </div>
        <div id="g_id_onload"
            data-client_id="731513726294-dtfa773a7fpbuhc4d543f20a36m7pt7n.apps.googleusercontent.com"
            data-context="signin"
            data-ux_mode="popup"
            data-callback="handleCredentialResponse"
            data-auto_prompt="false">
        </div>
        <div class="google-btn" id="customGoogleBtn" onclick="google.accounts.id.prompt()">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/1200px-Google_%22G%22_logo.svg.png"
                alt="Google logo">
            Sign in with Google
        </div>
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

        document.querySelector("form.authForm").addEventListener("submit", function (e) {
            const email = document.getElementById("email").value;
            const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!pattern.test(email)) {
                alert("Please enter a valid email address.");
                e.preventDefault();
            }
        });

       function handleCredentialResponse(response) {
    fetch("googleloginhandler.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ token: response.credential })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Successfully logged in with Google!");
            setTimeout(() => {
                window.location.href = "../index.php";
            }, 500);
        } else {
            alert("Google login failed.");
        }
    })
    .catch(err => console.error("Google login error:", err));
}

    </script>
</body>

</html>