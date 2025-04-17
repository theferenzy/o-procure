<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_suppliers.php?status=Approved&error=Invalid supplier ID");
    exit();
}

$id = intval($_GET['id']);

// Check if supplier exists
$check = mysqli_query($conn, "SELECT * FROM supplier_profiles WHERE id = $id");
if (mysqli_num_rows($check) === 0) {
    header("Location: manage_suppliers.php?status=Approved&error=Supplier not found");
    exit();
}

// Suspend the supplier
$update = "UPDATE supplier_profiles SET status = 'Suspended' WHERE id = $id";
if (mysqli_query($conn, $update)) {
    header("Location: manage_suppliers.php?status=Approved&success=Supplier suspended successfully");
    exit();
} else {
    header("Location: manage_suppliers.php?status=Approved&error=Failed to suspend supplier");
    exit();
}
