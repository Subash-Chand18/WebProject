<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Adminlogin.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard O the Project</title>
    <title>Dashboard of the Project</title>
    <link rel="stylesheet" href="Admindashboard.css">
</head>
<body>
    <h1>E CLothing Store</h1>
    <p><b>Welcome to our store</b><br>This is our Dashboard</p>
      <header>
        <nav>
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Contact</a>
            <a href="#">Disclaimer</a>
            <a href="#">Privacy</a>
            <a href="#">Sitemap</a>
            <a href="#">Terms</a>
            <a href="#">Logout</a>
        </nav>
    </header>
   <main class="main-content">
        <section class="welcome-section">
            <h1>Welcome to E Clothing Store Web Application</h1>
            <p>This is our home page. Explore the features and enjoy using our application.</p>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2025 . All Rights Reserved.</p>
    </footer>
</body>
</html>