<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Delete the 'email' cookie used for Remember Me
if (isset($_COOKIE['email'])) {
    setcookie("email", "", time() - 3600, "/"); // Expire it
}

// Redirect to the login page
header("Location: Adminlogin.php");
exit();
?>
