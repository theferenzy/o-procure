<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header("Location: /o-procure/pages/login.php");
    exit();
}

require_once('../../../config/database.php');

$bid_id = isset($_GET['bid_id']) ? intval($_GET['bid_id']) : 0;
$contract_id = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;

if ($bid_id > 0 && $contract_id > 0) {

    // Step 1: Approve selected bid
    $approve_sql = "UPDATE bids 
                    SET bid_status = 'Approved', is_awarded = 1 
                    WHERE bid_id = '$bid_id' AND contract_id = '$contract_id'";
    $approve_result = mysqli_query($conn, $approve_sql);

    // Step 2: Close all other bids for this contract
    $close_sql = "UPDATE bids 
                  SET bid_status = 'Not Selected', is_awarded = 0 
                  WHERE contract_id = '$contract_id' AND bid_id != '$bid_id'";
    $close_result = mysqli_query($conn, $close_sql);

    if ($approve_result && $close_result) {
        $_SESSION['success'] = "✅ Contract awarded successfully. All other bids have been closed.";
    } else {
        $_SESSION['error'] = "❌ Failed to finalize award. Please try again.";
    }

} else {
    $_SESSION['error'] = "❌ Invalid bid or contract ID.";
}

header("Location: managebids.php");
exit();
