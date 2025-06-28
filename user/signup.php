<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';
$success = '';

if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password_raw = trim($_POST['password']);
    $confirm_password_raw = trim($_POST['confirm_password']);

    $upload_dir = "../assets/images/";
    $image = "avatar.jpg";

    if (!empty($_FILES['userfile']['name'])) {
        $image = basename($_FILES['userfile']['name']);
        $upload_file = $upload_dir . $image;

        if ($_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            $error = "File upload error: " . $_FILES['userfile']['error'];
        } else {
            move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file);
        }
    }

    // Server-side validation
    if (empty($name) || empty($email) || empty($password_raw) || empty($confirm_password_raw)) {
        $error = "All fields except profile image are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password_raw !== $confirm_password_raw) {
        $error = "Passwords do not match!";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/", $password_raw)) {
        $error = "Password must be at least 8 characters, include uppercase, lowercase, and a special character.";
    } else {
        $password = md5($password_raw); // For production, use password_hash()

        $checkQuery = "SELECT id FROM users WHERE email = '$email' AND deleted_at IS NULL";
        $checkResult = mysqli_query($con, $checkQuery);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $error = "Email is already registered!";
        } else {
            $insertQuery = "INSERT INTO users (name, email, password, image) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password, $image);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! You can now <a href='Userlogin.php?email=" . urlencode($email) . "'>login</a>.";
                unset($_POST);
            } else {
                $error = "Registration failed: " . mysqli_error($con);
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
    <form class="authForm" action="" method="post" enctype="multipart/form-data" onsubmit="return validateSignupForm()" novalidate>
        <h2>User Signup</h2>

        <?php if ($error): ?>
            <p class="form-message" style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p class="form-message" style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>

        <div class="form-group">
            <input type="text" name="name" id="name" placeholder=" " required
                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />
            <label for="name">Full Name</label>
        </div>

        <div class="form-group">
            <input type="email" name="email" id="email" placeholder=" " required
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
            <label for="email">Email</label>
        </div>

        <div class="form-group" style="position: relative;">
            <input type="password" name="password" id="password" placeholder=" " required />
            <label for="password">Password</label>
            <i class="bx bx-show toggle-icon" id="togglePassword" role="button" tabindex="0"></i>
        </div>

        <div class="form-group" style="position: relative;">
            <input type="password" name="confirm_password" id="confirm_password" placeholder=" " required />
            <label for="confirm_password">Confirm Password</label>
            <i class="bx bx-show toggle-icon" id="toggleConfirmPassword" role="button" tabindex="0"></i>
        </div>

        <div class="form-group">
            <label for="userfile"></label>
            <input type="file" name="userfile" id="userfile" accept="image/*" />
        </div>

        <div class="button-group">
            <button type="submit" name="signup">Sign Up</button>
            <a href="Userlogin.php" id="backToLogin" class="cancel-btn"
                style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; margin-top: 10px;">Back
                to Login</a>
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

        function validateSignupForm() {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const confirm = document.getElementById("confirm_password").value;

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/;

            const messageElement = document.querySelector(".form-message");
            if (messageElement) messageElement.textContent = "";

            if (!emailPattern.test(email)) {
                displayMessage("Please enter a valid email address.", "red");
                return false;
            }

            if (password !== confirm) {
                displayMessage("Passwords do not match!", "red");
                return false;
            }

            if (!passwordPattern.test(password)) {
                displayMessage("Password must be at least 8 characters, include one uppercase, one lowercase, and one special character.", "red");
                return false;
            }

            return true;
        }

        function displayMessage(message, color) {
            let msgElem = document.querySelector(".form-message");
            if (!msgElem) {
                msgElem = document.createElement("p");
                msgElem.className = "form-message";
                document.querySelector(".authForm").insertBefore(msgElem, document.querySelector(".authForm .form-group"));
            }
            msgElem.style.color = color;
            msgElem.textContent = message;
        }
    </script>
</body>

</html>
