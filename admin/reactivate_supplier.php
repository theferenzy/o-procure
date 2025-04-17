<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}

require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: manage_suppliers.php?error=Missing supplier ID");
    exit();
}

$id = intval($_GET['id']);

// Ensure supplier exists
$sql = "SELECT * FROM supplier_profiles WHERE id = $id";
$result = mysqli_query($conn, $sql);
$supplier = mysqli_fetch_assoc($result);

if (!$supplier) {
    header("Location: manage_suppliers.php?error=Supplier not found");
    exit();
}

// Update supplier status to Approved
$update = "UPDATE supplier_profiles SET status = 'Approved' WHERE id = $id";
if (mysqli_query($conn, $update)) {
    header("Location: manage_suppliers.php?success=Supplier reactivated successfully");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
