<?php
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

    require 'Mail/mailer.php';
    require '../settings.php';

    if(isset($_POST['submit'])){
        $email = $_POST['email'];

        $sql = "select * from users where email = '".$email."'";
        $res = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($res);
        if($res->num_rows > 0){
            $link= BASE_URL."user\passwordreset.php?id=".$row['id'];
            $msg = "Hello ".$row['email']." <br >";
            $msg .= "Please click on the link : <a href='".$link."'>Click here</a>";
            mailer($msg);
        }else{
            echo "email address does not match our database";
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/passwordreset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
   <div class="forgot-password-wrapper">
        <form action="" method="POST">
            
            <h1>Forgot Password</h1>
            <p>Enter your email address and we’ll send you a link to reset your password.</p>
            
            <div class="input-box">
                <i class='bx bxs-envelope'></i>
                <input type="email" placeholder="Email Address" name="email" required>
            </div>
            
            <button type="submit" class="btn" name="submit">Send Reset Link</button>
            
            <div class="login-link">
                <p>Remember your password? <a href="Userlogin.php">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
