<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Userlogin.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

// Fetch user details including profile image
$query = "SELECT name, email, created_at, image FROM users WHERE id = '$user_id'";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "User details not found.";
    exit;
}
?>

<?php include("includes/header.php"); ?>

<style>
.main-content {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    background: #f1f1f1;
}

.welcome-section {
    text-align: center;
    max-width: 700px;
    background-color: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.welcome-section h1 {
    font-size: 32px;
    margin-bottom: 20px;
    color: #333;
}

.user-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 20px;
    border: 3px solid #4a90e2;
}

.user-info {
    text-align: left;
    margin: 20px 0;
}

.user-info p {
    font-size: 18px;
    color: #444;
    margin: 10px 0;
    text-align: center;
}

.explore-btn {
    background-color: #4a90e2;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.explore-btn:hover {
    background-color: #357ab8;
}
</style>

<br><br><br><br><br><br>

<main class="main-content">
    <section class="welcome-section">
        
        <?php if (!empty($user['image'])): ?>
            <img src="../assets/images/<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Image" class="user-image">
        <?php else: ?>
            <img src="../assets/images/avatar.jpg" alt="Default Profile Image" class="user-image">
        <?php endif; ?>

        <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <!-- <p>Here are your account details:</p> -->

        <div class="user-info">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Joined Date:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
        </div>

        <a href="our_shop.php">
            <button class="explore-btn">Explore More</button>
        </a>
    </section>
</main>

<?php include("includes/footer.php"); ?>
