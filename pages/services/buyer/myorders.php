<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header('Location: ../../login.php');
    exit();
}
include '../../../config/database.php';

$buyer_id = $_SESSION['user_id'];
$query = "SELECT o.*, u.full_name AS supplier_name
          FROM orders o
          JOIN users u ON o.supplier_id = u.user_id
          WHERE o.buyer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders - O-Procure</title>
    <link rel="stylesheet" href="../../../assets/style.css">
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-4">My Orders</h2>
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="p-2 border">Order ID</th>
                    <th class="p-2 border">Supplier</th>
                    <th class="p-2 border">Amount</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="text-center">
                        <td class="p-2 border"><?= $row['order_id'] ?></td>
                        <td class="p-2 border"><?= $row['supplier_name'] ?></td>
                        <td class="p-2 border">₦<?= number_format($row['total_amount'], 2) ?></td>
                        <td class="p-2 border"><?= $row['order_status'] ?></td>
                        <td class="p-2 border"><?= $row['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php 
include_once '../../../includes/footer.php';
?>