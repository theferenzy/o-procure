<?php
include '../config/database.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Update status to 'Active' when approved
    $sql = "UPDATE users SET status = 'Active' WHERE user_id = $user_id";
    if (mysqli_query($conn, $sql)) {
        echo "User approved successfully.";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

header("Location: manageusers.php");
exit();
?>
