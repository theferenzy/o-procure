<?php
require_once('../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once '../config/database.php';
require_once '../includes/functions.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_suppliers.php?error=Invalid request");
    exit();
}

$id = intval($_GET['id']);

// Fetch supplier and user info for confirmation
$sql = "SELECT sp.id, sp.supplier_id, sp.company_name, u.full_name, u.email 
        FROM supplier_profiles sp
        JOIN users u ON sp.supplier_id = u.user_id
        WHERE sp.id = $id";

$result = mysqli_query($conn, $sql);
$supplier = mysqli_fetch_assoc($result);

if (!$supplier) {
    header("Location: manage_suppliers.php?error=Supplier not found");
    exit();
}

// Update the supplier_profiles status to 'Approved'
$update_sql = "UPDATE supplier_profiles SET status = 'Approved' WHERE id = $id";

if (mysqli_query($conn, $update_sql)) {
    //Log the action
    log_admin_action($conn, $_SESSION['user_id'], "Approved supplier ID: {$supplier['supplier_id']} - {$supplier['company_name']}");

    $to = $supplier['email'];
    $subject = "O-Procure Prequalification Approved ✅";
    $message = "Dear " . $supplier['full_name'] . ",\n\nYour prequalification has been approved.\n\nYou can now log in to O-Procure and begin bidding for contracts.\n\nBest regards,\nO-Procure Admin Team";
    $headers = "From: no-reply@o-procure.local";

    @mail($to, $subject, $message, $headers); // Suppress errors in case mail is disabled

    header("Location: manage_suppliers.php?success=" . urlencode("✅ Supplier approved successfully"));
    exit();
} else {
    header("Location: manage_suppliers.php?error=" . urlencode("❌ Failed to approve supplier"));
    exit();
}
