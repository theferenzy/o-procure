<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../../config/database.php');
include('../../../includes/header.php');

// Get buyer ID
$buyer_id = $_SESSION['user_id'];

// Fetch all contracts created by this buyer
$query = "SELECT * FROM contracts WHERE buyer_id = '$buyer_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contracts Archive</title>
    <link rel="stylesheet" href="../../../assets/style.css">
    <link rel="stylesheet" href="../../../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/tailwindcss/tailwind.min.css">
</head>

<div class="container">
    <h2 class="page-title">🗃️ My Contracts Archive</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Budget</th>
                    <th>Tier</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Deadline</th>
                    <th>ITT Document</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($contract = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($contract['title']) ?></td>
                        <td>₦<?= number_format($contract['budget'], 2) ?></td>
                        <td><?= $contract['tier'] ?></td>
                        <td><?= $contract['status'] ?></td>
                        <td><?= date('d M Y', strtotime($contract['created_at'])) ?></td>
                        <td><?= date('d M Y', strtotime($contract['deadline'])) ?></td>
                        <td>
                            <?php if (!empty($contract['itt_document']) && file_exists('../../../uploads/' . $contract['itt_document'])): ?>
                                <a href="/o-procure/uploads/<?= $contract['itt_document'] ?>" target="_blank">📥 View</a>
                            <?php else: ?>
                                <span class="text-muted">No File</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You haven't listed any contracts yet.</p>
    <?php endif; ?>
</div>

<?php include('../../../includes/footer.php'); ?>
