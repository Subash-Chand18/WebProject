<?php
include '../includes/dbcon.php';  // Adjust path if needed

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE product_ratings SET deleted_at = NOW() WHERE id = $id";
    if (mysqli_query($con, $sql)) {
        header('Location: product_rating_review.php');
    } else {
        echo "Error deleting rating.";
    }
} else {
    header('Location: product_rating_review.php');
}
?>
