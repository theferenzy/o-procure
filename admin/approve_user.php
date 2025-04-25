<?php
require_once('../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}

require_once('../config/database.php');
require_once('../includes/functions.php');

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$reactivate = isset($_GET['reactivate']) ? true : false;

if ($user_id <= 0) {
    $_SESSION['error'] = "Invalid user ID.";
    header("Location: manageusers.php");
    exit();
}

$status = 'Active'; // Whether it's approve or reactivate, set to 'Active'
$sql = "UPDATE users SET status = '$status' WHERE user_id = '$user_id'";

if (mysqli_query($conn, $sql)) {
    // Fetch user info for confirmation
    $action = $reactivate ? "Reactivated user ID: $user_id" : "Approved user ID: $user_id";
    log_admin_action($conn, $_SESSION['user_id'], $action);

    $_SESSION['success'] = $reactivate ? "✅ User reactivated successfully." : "✅ User approved successfully.";
} else {
    $_SESSION['error'] = "❌ Failed to update user.";
}

header("Location: manageusers.php");
exit();
