<?php
require_once('../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once '../config/database.php';
require_once '../includes/functions.php'; 

if (!isset($_GET['id'])) {
    header("Location: manage_suppliers.php");
    exit();
}

$id = intval($_GET['id']);

// Get supplier and email details
$sql = "SELECT sp.id, sp.supplier_id, u.email, u.full_name 
        FROM supplier_profiles sp
        JOIN users u ON sp.supplier_id = u.user_id
        WHERE sp.id = $id";
$result = mysqli_query($conn, $sql);
$supplier = mysqli_fetch_assoc($result);

if (!$supplier) {
    header("Location: manage_suppliers.php?error=Supplier not found");
    exit();
}

// Update status
$update = "UPDATE supplier_profiles SET status = 'Rejected' WHERE id = $id";
if (mysqli_query($conn, $update)) {
    $admin_id = $_SESSION['user_id'];
    $action = "Rejected supplier ID: {$supplier['supplier_id']} - {$supplier['full_name']}";
    log_admin_action($conn, $admin_id, $action);

    // Send rejection email
    $to = $supplier['email'];
    $subject = "O-Procure: Prequalification Rejected ❌";
    $message = "Dear " . $supplier['full_name'] . ",\n\nUnfortunately, your prequalification documents did not meet our compliance requirements at this time.\n\nWe encourage you to review our onboarding checklist and resubmit your documents.\n\nRegards,\nO-Procure Admin Team";
    $headers = "From: no-reply@o-procure.local";

    mail($to, $subject, $message, $headers);

    header("Location: manage_suppliers.php?success=Supplier rejected and notified");
    exit();
} else {
    echo "Error rejecting supplier: " . mysqli_error($conn);
}
