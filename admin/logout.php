<?php
session_start();

// Clear all session variables
$_SESSION = [];

// Destroy the session data on the server
session_destroy();

// Delete the 'email' cookie used for Remember Me (adjust path if needed)
if (isset($_COOKIE['email'])) {
    // Use the same path and domain as cookie was set; here '/' for root path
    setcookie("email", "", time() - 3600, "/");
}

// Redirect to login page - Adjust the path based on your folder structure
header("Location: Adminlogin.php");
exit();
?>
