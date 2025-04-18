<?php
require_once('../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}

require_once '../config/database.php';
include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Suppliers</title>
    <link rel="stylesheet" href="../assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/tailwindcss/tailwind.min.css">
</head>


<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Manage Suppliers</h1>

    <?php if (isset($_GET['success'])): ?>
    <p class="success"><?= htmlspecialchars($_GET['success']) ?></p>
    <?php elseif (isset($_GET['error'])): ?>
    <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <?php
    $sql = "SELECT sp.*, u.full_name, u.email, u.company_name, u.contact_number 
            FROM supplier_profiles sp
            JOIN users u ON sp.supplier_id = u.user_id
            ORDER BY sp.created_at DESC";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0): ?>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border p-2">Company</th>
                <th class="border p-2">Full Name</th>
                <th class="border p-2">Email</th>
                <th class="border p-2">Contact</th>
                <th class="border p-2">Tax ID</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td class="border p-2"><?= htmlspecialchars($row['company_name']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['full_name']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['email']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['contact_number']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['tax_id']) ?></td>
                <td class="border p-2">
                    <?php
                                if ($row['status'] === 'Approved') {
                                    echo '<span class="text-green-600 font-semibold">Active</span>';
                                } elseif ($row['status'] === 'Suspended') {
                                    echo '<span class="text-red-600 font-semibold">Suspended</span>';
                                } else {
                                    echo htmlspecialchars($row['status']);
                                }
                            ?>
                </td>
                <td class="border p-2">
                    <?php if ($row['status'] === 'Approved'): ?>
                    <a href="suspend_supplier.php?id=<?= $row['id'] ?>"
                        class="bg-red-500 text-white px-3 py-1 rounded">⛔ Suspend</a>
                    <?php elseif ($row['status'] === 'Suspended'): ?>
                    <a href="reactivate_supplier.php?id=<?= $row['id'] ?>"
                        class="bg-blue-600 text-white px-3 py-1 rounded">🔄 Reactivate</a>
                    <?php elseif ($row['status'] === 'Pending'): ?>
                    <a href="approve_supplier.php?id=<?= $row['id'] ?>"
                        class="bg-green-600 text-white px-3 py-1 rounded">✅ Approve</a>
                    <a href="reject_supplier.php?id=<?= $row['id'] ?>"
                        class="bg-red-600 text-white px-3 py-1 rounded ml-2">❌ Reject</a>
                    <?php else: ?>
                    <span class="text-gray-500">No actions</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No supplier profiles found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>