<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once('../config/database.php');
include('../includes/header.php');

// Fetch all pending bids with contract and supplier info
$sql = "SELECT bids.*, contracts.title AS contract_title, users.full_name AS supplier_name
        FROM bids
        JOIN contracts ON bids.contract_id = contracts.contract_id
        JOIN users ON bids.supplier_id = users.user_id
        WHERE bids.bid_status = 'Pending'
        ORDER BY bids.created_at DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Supplier Bids</title>
</head>
<body>
    <link rel="stylesheet" href="../assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/tailwindcss/tailwind.min.css">
</body>
</html>

<div class="container">
    <h2 class="page-title">🧾 Manage Supplier Bids</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Contract</th>
                    <th>Bid Price (₦)</th>
                    <th>Comments</th>
                    <th>Proposal Document</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($bid = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($bid['supplier_name']) ?></td>
                        <td><?= htmlspecialchars($bid['contract_title']) ?></td>
                        <td>₦<?= number_format($bid['bid_price'], 2) ?></td>
                        <td><?= nl2br(htmlspecialchars($bid['comments'])) ?></td>
                        <td>
                            <?php if (!empty($bid['bid_doc']) && file_exists('../uploads/bid_doc/' . $bid['bid_doc'])): ?>
                                <a href="/o-procure/uploads/bid_doc/<?= urlencode($bid['bid_doc']) ?>" target="_blank">📄 View Proposal</a>
                            <?php else: ?>
                                <span class="text-red-500 italic">No doc</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $bid['created_at'] ?></td>
                        <td>
                            <a href="managebids_action.php?id=<?= $bid['bid_id'] ?>&action=approve" class="btn approve">✅ Approve</a>
                            <a href="managebids_action.php?id=<?= $bid['bid_id'] ?>&action=reject" class="btn reject">❌ Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending bids to review at the moment.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
