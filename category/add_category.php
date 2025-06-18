<?php
include '../includes/header.php';

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = trim(mysqli_real_escape_string($con, $_POST['name']));
    $desc = trim(mysqli_real_escape_string($con, $_POST['description']));

    if ($name === '') {
        echo "<script>alert('Category name is required.');</script>";
    } else {
        $sql = "INSERT INTO category (name, description) VALUES ('$name', '$desc')";
        if (mysqli_query($con, $sql)) {
            echo "<script>alert('Category added successfully.'); window.location.href='view_category.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
        }
    }
}
?>

<link rel="stylesheet" href="../assets/css/add_category.css">

<section class="add-category-container">
    <form id="addCategoryForm" method="POST" novalidate>
        <button type="button" class="close-btn" onclick="window.location.href='../admin/Admindashboard.php'">&times;</button>

        <h2><i class="fas fa-plus-circle"></i> Add Category</h2>

        <div class="form-group">
            <input type="text" name="name" placeholder=" " required>
            <label><i class="fas fa-tags"></i> Category Name</label>
        </div>

        <div class="form-group">
            <textarea name="description" placeholder=" "></textarea>
            <label><i class="fas fa-align-left"></i> Description</label>
        </div>

        <div class="button-group">
            <button type="submit" name="submit"><i class="fas fa-upload"></i> Add Category</button>
            <button type="reset" class="cancel-btn"><i class="fas fa-eraser"></i> Clear Form</button>
        </div>
    </form>
</section>

<?php include '../includes/footer.php'; ?>