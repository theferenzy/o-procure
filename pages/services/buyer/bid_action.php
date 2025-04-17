<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../../config/database.php');

$buyer_id = $_SESSION['user_id'];
$bid_id = isset($_GET['bid_id']) ? intval($_GET['bid_id']) : 0;
$action = $_GET['action'] ?? '';

if ($bid_id <= 0 || !in_array($action, ['reject', 'requestinfo'])) {
    $_SESSION['error'] = "❌ Invalid request.";
    header("Location: managebids.php");
    exit();
}

// Confirm the bid belongs to a contract owned by this buyer
$verify_sql = "SELECT b.bid_id 
               FROM bids b
               JOIN contracts c ON b.contract_id = c.contract_id
               WHERE b.bid_id = '$bid_id' AND c.buyer_id = '$buyer_id'";
$verify_result = mysqli_query($conn, $verify_sql);

if (!$verify_result || mysqli_num_rows($verify_result) == 0) {
    $_SESSION['error'] = "❌ Bid not found or access denied.";
    header("Location: managebids.php");
    exit();
}

// Set status to Rejected regardless of whether action is reject or requestinfo
$update_sql = "UPDATE bids SET bid_status = 'Rejected' WHERE bid_id = '$bid_id'";

if (mysqli_query($conn, $update_sql)) {
    $_SESSION['success'] = "❌ Bid has been rejected.";
} else {
    $_SESSION['error'] = "⚠️ Failed to reject the bid.";
}

header("Location: managebids.php");
exit();
