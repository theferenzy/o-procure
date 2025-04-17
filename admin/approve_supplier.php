<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once '../config/database.php';


if (!isset($_GET['id'])) {
    header("Location: manage_suppliers.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch supplier and user info
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
$update = "UPDATE supplier_profiles SET status = 'Approved' WHERE id = $id";
if (mysqli_query($conn, $update)) {
    // Send email
    $to = $supplier['email'];
    $subject = "O-Procure: Prequalification Approved ✅";
    $message = "Dear " . $supplier['full_name'] . ",\n\nCongratulations! Your supplier prequalification has been approved.\n\nYou can now access available contracts and begin bidding at https://o-procure.local\n\nThank you,\nO-Procure Admin Team";
    $headers = "From: no-reply@o-procure.local";

    // Use mail() function (simple method)
    mail($to, $subject, $message, $headers);

    header("Location: manage_suppliers.php?success=Supplier approved");
    exit();
} else {
    echo "Error updating status: " . mysqli_error($conn);
}
