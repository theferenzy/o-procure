<?php
require_once('../../../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../../config/database.php');

$bid_id = intval($_GET['bid_id'] ?? 0);

if ($bid_id > 0) {
    $sql = "UPDATE bids SET bid_status = 'Rejected' WHERE bid_id = '$bid_id'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "✅ Bid has been rejected.";
    } else {
        $_SESSION['error'] = "❌ Failed to reject bid.";
    }
}

header('Location: managebids.php');
exit();
