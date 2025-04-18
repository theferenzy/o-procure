<?php
require_once('../../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../config/database.php');
include('../../includes/header.php');

$supplier_id = $_SESSION['user_id'];
$message = "";

// Fetch all bids by the supplier with contract info
$query = "SELECT b.*, c.title AS contract_title, c.deadline, c.buyer_id, u.full_name AS buyer_name
          FROM bids b
          JOIN contracts c ON b.contract_id = c.contract_id
          JOIN users u ON c.buyer_id = u.user_id
          WHERE b.supplier_id = '$supplier_id'
          ORDER BY b.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bids</title>
    <link rel="stylesheet" href="../../assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../assets/tailwindcss/tailwind.min.css">
</head>

<div class="container">
    <h2 class="page-title">✏️ Edit My Bids</h2>
    <?= $message ?>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Contract</th>
                    <th>Buyer</th>
                    <th>Deadline</th>
                    <th>Bid Price</th>
                    <th>Comments</th>
                    <th>Status</th>
                    <th>Proposal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($bid = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($bid['contract_title']) ?></td>
                        <td><?= htmlspecialchars($bid['buyer_name']) ?></td>
                        <td><?= htmlspecialchars($bid['deadline']) ?></td>
                        <td>₦<?= number_format($bid['bid_price'], 2) ?></td>
                        <td><?= nl2br(htmlspecialchars($bid['comments'])) ?></td>
                        <td>
                            <?php if ($bid['is_awarded']): ?>
                                🏆 Awarded
                            <?php elseif ($bid['bid_status'] === 'Rejected'): ?>
                                ❌ Rejected <br>
                                <span class="text-sm text-gray-500">You may edit & resubmit</span>
                            <?php elseif ($bid['bid_status'] === 'Pending'): ?>
                                ⏳ Pending Review
                            <?php elseif ($bid['bid_status'] === 'Approved'): ?>
                                ✅ Approved
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($bid['bid_doc']) && file_exists('../../uploads/bid_doc/' . $bid['bid_doc'])): ?>
                                <a href="/o-procure/uploads/bid_doc/<?= $bid['bid_doc'] ?>" target="_blank">📄 View</a>
                            <?php else: ?>
                                <span class="text-warning">No doc</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$bid['is_awarded']): ?>
                                <a href="update_bid.php?bid_id=<?= $bid['bid_id'] ?>" class="btn">✏️ Edit</a>
                            <?php else: ?>
                                <span class="text-muted" style="color:gray;">🔒 Locked</span>
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
