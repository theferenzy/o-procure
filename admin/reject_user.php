<?php
include '../config/database.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Update status to 'Suspended' when rejected
    $sql = "UPDATE users SET status = 'Suspended' WHERE user_id = $user_id";
    if (mysqli_query($conn, $sql)) {
        echo "User rejected successfully.";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

header("Location: manageusers.php");
exit();
?>
