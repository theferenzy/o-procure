<?php
require_once('../includes/session_security.php');

include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

$contract_id = intval($_GET['id']);
$action = $_GET['action'] === 'approve' ? 'Approved' : 'Rejected';

$sql = "UPDATE contracts 
        SET status = '$action', 
            approved_at = NOW(), 
            approved_by = {$_SESSION['user_id']} 
        WHERE contract_id = $contract_id";

$conn->query($sql);

header("Location: review_contracts.php");
exit();
?>
