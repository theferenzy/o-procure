<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit();
}

include '../config/database.php';
include '../includes/header.php';

// Fetch all pending contracts
$sql = "SELECT * FROM contracts WHERE status = 'Pending' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-4">Pending Contracts for Review</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="w-full table-auto border">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="p-2 border">Title</th>
                    <th class="p-2 border">Budget (₦)</th>
                    <th class="p-2 border">Tier</th>
                    <th class="p-2 border">Deadline</th>
                    <th class="p-2 border">Created</th>
                    <th class="p-2 border">ITT</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="text-sm">
                        <td class="p-2 border"><?= htmlspecialchars($row['title']) ?></td>
                        <td class="p-2 border">₦<?= number_format($row['budget'], 2) ?></td>
                        <td class="p-2 border"><?= $row['tier'] ?></td>
                        <td class="p-2 border"><?= $row['deadline'] ?></td>
                        <td class="p-2 border"><?= $row['created_at'] ?></td>
                        <td class="p-2 border text-center">
                            <?php if (!empty($row['itt_document'])): ?>
                                <a href="../uploads/<?= htmlspecialchars($row['itt_document']) ?>" target="_blank" class="text-blue-700 underline hover:text-blue-900">
                                    📄 View
                                </a>
                            <?php else: ?>
                                <span class="text-gray-500 italic">None</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-2 border flex flex-col gap-1">
                            <a href="contract_action.php?id=<?= $row['contract_id'] ?>&action=approve"
                               class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-center">✅ Approve</a>
                            <a href="contract_action.php?id=<?= $row['contract_id'] ?>&action=reject"
                               class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-center">❌ Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-600">No pending contracts at the moment.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
