<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}

include '../config/database.php';
include '../includes/header.php';

// Filter by status if passed
$status_filter = $_GET['status'] ?? 'Pending';
$filter_query = ($status_filter === 'All') ? "1" : "status = '$status_filter'";

$sql = "SELECT user_id, full_name, email, role, company_name, contact_number, status 
        FROM users WHERE $filter_query ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/tailwindcss/tailwind.min.css">
</head>


<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">👥 Manage Users</h1>

    <div class="mb-4">
        <a href="?status=Pending" class="btn <?= $status_filter === 'Pending' ? 'bg-blue-800 text-white' : '' ?>">Pending</a>
        <a href="?status=Active" class="btn <?= $status_filter === 'Active' ? 'bg-green-700 text-white' : '' ?>">Active</a>
        <a href="?status=Rejected" class="btn <?= $status_filter === 'Rejected' ? 'bg-red-700 text-white' : '' ?>">Rejected</a>
        <a href="?status=All" class="btn <?= $status_filter === 'All' ? 'bg-gray-700 text-white' : '' ?>">All Users</a>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border p-2">Full Name</th>
                    <th class="border p-2">Email</th>
                    <th class="border p-2">Role</th>
                    <th class="border p-2">Company</th>
                    <th class="border p-2">Contact</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="border p-2"><?= htmlspecialchars($row['full_name']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['role']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['company_name'] ?? 'N/A') ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['contact_number']) ?></td>
                        <td class="border p-2">
                            <?php
                                $status = $row['status'];
                                echo $status === 'Active' ? '✅ Active' :
                                     ($status === 'Rejected' ? '❌ Rejected' : '⏳ Pending');
                            ?>
                        </td>
                        <td class="border p-2 space-x-2">
                            <?php if ($status === 'Pending'): ?>
                                <a href="approve_user.php?id=<?= $row['user_id'] ?>" class="btn bg-green-600 text-white">Approve</a>
                                <a href="reject_user.php?id=<?= $row['user_id'] ?>" class="btn bg-red-600 text-white">Reject</a>
                            <?php elseif ($status === 'Active'): ?>
                                <a href="reject_user.php?id=<?= $row['user_id'] ?>&deactivate=1" class="btn bg-gray-600 text-white">Deactivate</a>
                            <?php elseif ($status === 'Rejected'): ?>
                                <a href="approve_user.php?id=<?= $row['user_id'] ?>&reactivate=1" class="btn bg-blue-600 text-white">Reactivate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found for the selected status.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
