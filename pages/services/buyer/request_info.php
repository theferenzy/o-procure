<?php
require_once('../../../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../../config/database.php');

$bid_id = intval($_POST['bid_id']);
$feedback = mysqli_real_escape_string($conn, $_POST['buyer_feedback']);

if ($bid_id && $feedback) {
    $sql = "UPDATE bids SET buyer_feedback = '$feedback' WHERE bid_id = '$bid_id'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "📝 Info request sent to supplier.";
    } else {
        $_SESSION['error'] = "❌ Could not send feedback.";
    }
}

header("Location: managebids.php");
exit();
