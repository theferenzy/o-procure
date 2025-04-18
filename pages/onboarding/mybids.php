<?php
require_once('../../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../config/database.php');
include('../../includes/header.php');

$supplier_id = $_SESSION['user_id'];

$sql = "SELECT b.*, c.title AS contract_title, c.budget, c.deadline, c.buyer_id, u.full_name AS buyer_name, u.company_name 
        FROM bids b 
        JOIN contracts c ON b.contract_id = c.contract_id 
        JOIN users u ON c.buyer_id = u.user_id
        WHERE b.supplier_id = '$supplier_id' 
        ORDER BY b.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bid History</title>
    <link rel="stylesheet" href="../../assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../assets/tailwindcss/tailwind.min.css">
</head>

<body>
<div class="container">
    <h2 class="page-title">📋 My Bid History & Contract Awards</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Contract</th>
                    <th>Buyer</th>
                    <th>Bid Price (₦)</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Proposal</th>
                    <th>Award</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['contract_title']) ?></td>
                        <td><?= htmlspecialchars($row['company_name'] ?: $row['buyer_name']) ?></td>
                        <td>₦<?= number_format($row['bid_price'], 2) ?></td>
                        <td>
                            <?php
                                if ($row['bid_status'] === 'Approved') {
                                    echo '✅ Approved';
                                } elseif ($row['bid_status'] === 'Rejected') {
                                    echo '❌ Rejected';
                                    if (!empty($row['rejection_reason'])) {
                                        echo '<br><span style="color: orange;">📩 Info Requested</span>';
                                    }
                                } else {
                                    echo '⏳ Pending';
                                }
                            ?>
                        </td>
                        <td><?= $row['deadline'] ?></td>
                        <td>
                            <?php if (!empty($row['bid_doc']) && file_exists('../../uploads/bid_doc/' . $row['bid_doc'])): ?>
                                <a href="/o-procure/uploads/bid_doc/<?= $row['bid_doc'] ?>" target="_blank">📄 View</a>
                            <?php else: ?>
                                <span class="text-warning">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                if ($row['is_awarded'] == 1) {
                                    echo '🏆 <strong class="text-green-600">Awarded</strong>';
                                } elseif ($row['bid_status'] === 'Approved') {
                                    echo '⌛ <span class="text-blue-600">Awaiting Buyer Decision</span>';
                                } elseif ($row['bid_status'] === 'Rejected') {
                                    echo '🚫 <span class="text-gray-600">Not Selected</span>';
                                } else {
                                    echo '—';
                                }
                            ?>
                        </td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <?php if ($row['bid_status'] !== 'Approved' && $row['is_awarded'] != 1): ?>
                                <a href="edit_bid.php?bid_id=<?= $row['bid_id'] ?>" class="btn">✏️ Edit</a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bids submitted yet.</p>
    <?php endif; ?>
</div>

<?php include('../../includes/footer.php'); ?>
</body>
</html>
