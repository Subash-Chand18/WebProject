<?php
    $user_id = $_GET['id'];

    if(isset($_POST['submit'])){
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];
        if($pass == $cpass){
            $sql = "update users set pass = '".md5($pass)."' where id =".$user_id;
            $res = mysqli_query($con, $sql);
            if($res){
                echo "Passowrd changed";
                header("Location: Userlogin.php");
            }else{
                echo 'Some problem during pass reset';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    
    <form action="" method="post">
        <label for="login">New Password</label>
        <input type="password" name="pass">

        <label for="Password">Confirm Password</label>
        <input type="pasword" name="cpass">

        <input type="submit" name="submit" value="Change password">

    </form>
</body>
</html>