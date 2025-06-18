<?php
session_start();

$_SESSION = [];

session_destroy();

// Delete the 'email' cookie used for Remember Me (adjust path if needed)
if (isset($_COOKIE['email'])) {

    setcookie("email", "", time() - 3600, "/");
}

header("Location: Adminlogin.php");
exit();
?>