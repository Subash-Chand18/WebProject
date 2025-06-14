<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Userlogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #00c6ff, #0072ff);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        .success-container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            color: #333;
            max-width: 500px;
            width: 100%;
            animation: fadeIn 1s ease-in-out;
        }

        .success-container h1 {
            color: #28a745;
            margin-bottom: 20px;
        }

        .success-container a {
            display: inline-block;
            margin: 10px 20px;
            padding: 12px 25px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .success-container a:hover {
            background-color: #0056b3;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

    <div class="success-container">
        <h1>Thank you! 🎉</h1>
        <p>Your order has been placed successfully.</p>
        <div>
            <a href="../index.php">Continue Shopping</a>
            <a href="../index.php">Go to Home Page</a>
        </div>
    </div>

</body>
</html>
