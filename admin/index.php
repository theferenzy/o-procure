<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once '../config/database.php';
include '../includes/header.php';

// Query counts
$totalUsers     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$totalContracts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM contracts"))['total'];
$totalBids      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bids"))['total'];
$activeSuppliers= mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM supplier_profiles WHERE status='Approved'"))['total'];
$suspendedUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE status='Suspended'"))['total'];
$awardedBids    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bids WHERE is_awarded = 1"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/tailwindcss/tailwind.min.css">
</head>
<body>

<div class="container">
    <h2 class="page-title">📊 Admin Dashboard Overview</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-blue-700 text-lg font-bold">👥 Total Users</h3>
            <p class="text-xl"><?= $totalUsers ?></p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-blue-700 text-lg font-bold">📦 Contracts Created</h3>
            <p class="text-xl"><?= $totalContracts ?></p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-blue-700 text-lg font-bold">📝 Bids Submitted</h3>
            <p class="text-xl"><?= $totalBids ?></p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-blue-700 text-lg font-bold">✅ Active Suppliers</h3>
            <p class="text-xl"><?= $activeSuppliers ?></p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-blue-700 text-lg font-bold">🚫 Suspended Users</h3>
            <p class="text-xl"><?= $suspendedUsers ?></p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-blue-700 text-lg font-bold">🏆 Contracts Awarded</h3>
            <p class="text-xl"><?= $awardedBids ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
