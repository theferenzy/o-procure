<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once '../config/database.php';
include '../includes/header.php';

$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'Approved';
$validStatuses = ['Approved', 'Suspended'];

if (!in_array($statusFilter, $validStatuses)) {
    $statusFilter = 'Approved';
}

// Fetch suppliers
$sql = "SELECT sp.id, sp.company_name, sp.compliance_doc, sp.status, u.full_name, u.email, u.contact_number 
        FROM supplier_profiles sp
        JOIN users u ON sp.supplier_id = u.user_id
        WHERE sp.status = '$statusFilter'
        ORDER BY sp.created_at DESC";

$result = mysqli_query($conn, $sql);
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
    <h1 class="text-3xl font-bold mb-4">Manage <?= $statusFilter ?> Suppliers</h1>

    <div class="mb-4">
        <a href="?status=Approved" class="btn <?= $statusFilter === 'Approved' ? 'bg-blue-800 text-white' : 'bg-gray-300' ?>">✅ Active</a>
        <a href="?status=Suspended" class="btn <?= $statusFilter === 'Suspended' ? 'bg-blue-800 text-white' : 'bg-gray-300' ?>">🚫 Suspended</a>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border p-2">Company Name</th>
                    <th class="border p-2">Contact Person</th>
                    <th class="border p-2">Email</th>
                    <th class="border p-2">Phone</th>
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
                        <td class="border p-2"><?= htmlspecialchars($row['status']) ?></td>
                        <td class="border p-2">
                            <?php if ($row['status'] === 'Approved'): ?>
                                <a href="suspend_supplier.php?id=<?= $row['id'] ?>" class="bg-red-500 text-white px-3 py-1 rounded">Suspend</a>
                            <?php else: ?>
                                <a href="approve_supplier.php?id=<?= $row['id'] ?>&reactivate=1" class="bg-green-600 text-white px-3 py-1 rounded">Reactivate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No <?= $statusFilter ?> suppliers found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
