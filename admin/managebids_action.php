<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once('../config/database.php');

if (isset($_GET['id'], $_GET['action'])) {
    $bid_id = intval($_GET['id']);
    $action = $_GET['action'] === 'approve' ? 'Approved' : 'Rejected';

    $update = "UPDATE bids SET bid_status = '$action' WHERE bid_id = $bid_id";
    if (mysqli_query($conn, $update)) {
        header("Location: managebids.php?msg=updated");
        exit();
    } else {
        echo "Error updating bid: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
