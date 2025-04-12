<?php
// Start session and check if the user is an admin
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit;
}

// Include database connection
include('../config/database.php');

// Get total number of users
$query_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = mysqli_query($conn, $query_users);
$total_users = mysqli_fetch_assoc($result_users)['total_users'];

// Get total number of pending users
$query_pending_users = "SELECT COUNT(*) AS total_pending FROM users WHERE status = 'Pending'";
$result_pending_users = mysqli_query($conn, $query_pending_users);
$total_pending = mysqli_fetch_assoc($result_pending_users)['total_pending'];

// Get total number of bids
$query_bids = "SELECT COUNT(*) AS total_bids FROM bids";
$result_bids = mysqli_query($conn, $query_bids);
$total_bids = mysqli_fetch_assoc($result_bids)['total_bids'];

// Get total number of approved bids
$query_approved_bids = "SELECT COUNT(*) AS total_approved FROM bids WHERE bid_status = 'Approved'";
$result_approved_bids = mysqli_query($conn, $query_approved_bids);
$total_approved_bids = mysqli_fetch_assoc($result_approved_bids)['total_approved'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - O-Procure</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto p-6">
        <h1 class="text-4xl font-bold mb-4">Admin Dashboard</h1>

        <section class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Total Users -->
            <div class="bg-white p-6 shadow-lg rounded-lg">
                <h2 class="text-2xl font-semibold">Total Users</h2>
                <p class="text-3xl font-bold text-blue-900"><?php echo $total_users; ?></p>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-white p-6 shadow-lg rounded-lg">
                <h2 class="text-2xl font-semibold">Pending User Approvals</h2>
                <p class="text-3xl font-bold text-red-500"><?php echo $total_pending; ?></p>
            </div>

            <!-- Total Bids -->
            <div class="bg-white p-6 shadow-lg rounded-lg">
                <h2 class="text-2xl font-semibold">Total Bids</h2>
                <p class="text-3xl font-bold text-blue-900"><?php echo $total_bids; ?></p>
            </div>

            <!-- Approved Bids -->
            <div class="bg-white p-6 shadow-lg rounded-lg">
                <h2 class="text-2xl font-semibold">Approved Bids</h2>
                <p class="text-3xl font-bold text-green-600"><?php echo $total_approved_bids; ?></p>
            </div>
        </section>

        <section class="mt-6">
            <h2 class="text-2xl font-semibold mb-4">Quick Links</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <a href="manageusers.php" class="bg-blue-900 text-white text-center p-6 rounded-lg shadow-lg hover:bg-blue-800">Manage Users</a>
                <a href="managebids.php" class="bg-blue-900 text-white text-center p-6 rounded-lg shadow-lg hover:bg-blue-800">Manage Bids</a>
                <a href="reports.php" class="bg-blue-900 text-white text-center p-6 rounded-lg shadow-lg hover:bg-blue-800">View Reports</a>
                <a href="settings.php" class="bg-blue-900 text-white text-center p-6 rounded-lg shadow-lg hover:bg-blue-800">Settings</a>
            </div>
        </section>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
