<?php
require_once('../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}

require_once('../config/database.php');
require_once('../includes/functions.php'); 

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$deactivate = isset($_GET['deactivate']) ? true : false;

if ($user_id <= 0) {
    $_SESSION['error'] = "Invalid user ID.";
    header("Location: manageusers.php");
    exit();
}

$status = 'Rejected';
$sql = "UPDATE users SET status = '$status' WHERE user_id = '$user_id'";

if (mysqli_query($conn, $sql)) {
    $action = $deactivate ? "Deactivated user ID: $user_id" : "Rejected user ID: $user_id";
    log_admin_action($conn, $_SESSION['user_id'], $action);

    $_SESSION['success'] = $deactivate ? "🚫 User deactivated." : "❌ User rejected.";
} else {
    $_SESSION['error'] = "❌ Failed to reject/deactivate user.";
}

header("Location: manageusers.php");
exit();
