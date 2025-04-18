<?php
require_once('../../../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../../config/database.php');
include('../../../includes/header.php');

$buyer_id = $_SESSION['user_id'];

// ✅ Fetch ALL bids related to this buyer's contracts
$sql = "SELECT b.*, c.title, c.budget, c.deadline, u.full_name, u.company_name
        FROM bids b
        JOIN contracts c ON b.contract_id = c.contract_id
        JOIN users u ON b.supplier_id = u.user_id
        WHERE c.buyer_id = '$buyer_id'
        ORDER BY b.created_at DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bids</title>
    <link rel="stylesheet" href="../../../assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../../assets/fontawesome/css/all.min.css">
</head>

<body>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success"><?= $_SESSION['success'] ?></p>
    <?php unset($_SESSION['success']); ?>
<?php elseif (isset($_SESSION['error'])): ?>
    <p class="error"><?= $_SESSION['error'] ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="container">
    <h2 class="page-title">📦 Manage All Bids</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
    <table class="table">
        <thead>
            <tr>
                <th>Contract</th>
                <th>Supplier</th>
                <th>Company</th>
                <th>Bid Price</th>
                <th>Comments</th>
                <th>Proposal</th>
                <th>Status</th>
                <th>Actions</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['company_name']) ?></td>
                <td>₦<?= number_format($row['bid_price'], 2) ?></td>
                <td><?= nl2br(htmlspecialchars($row['comments'])) ?></td>
                <td>
                    <?php if (!empty($row['bid_doc']) && file_exists('../../../uploads/bid_doc/' . $row['bid_doc'])): ?>
                        <a href="/o-procure/uploads/bid_doc/<?= $row['bid_doc'] ?>" target="_blank">📄 View</a>
                    <?php else: ?>
                        <span class="text-warning">No doc</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                        if ($row['is_awarded'] == 1) {
                            echo '<span style="color:green; font-weight:bold;">Awarded</span>';
                        } else {
                            switch ($row['bid_status']) {
                                case 'Approved':
                                    echo '<span style="color:blue; font-weight:bold;">(A) - Pending BD</span>';
                                    break;
                                case 'Rejected':
                                    echo '<span style="color:red; font-weight:bold;">Rejected</span>';
                                    break;
                                case 'InfoRequested':
                                    echo '<span style="color:orange; font-weight:bold;">Info Requested</span>';
                                    break;
                                case 'Pending':
                                    echo '<span style="color:blue; font-weight:bold;">Pending</span>';
                                    break;
                                default:
                                    echo htmlspecialchars($row['bid_status']);
                            }
                        }
                    ?>
                </td>
                <td>
                    <?php if ($row['is_awarded'] == 1): ?>
                        <span class="btn awarded" style="background-color: green; color: white; pointer-events: none;">✅ Awarded</span>
                    <?php else: ?>
                        <a href="award_contract.php?bid_id=<?= $row['bid_id'] ?>&contract_id=<?= $row['contract_id'] ?>" class="btn approve">🏆 Award</a>
                        <a href="bid_action.php?bid_id=<?= $row['bid_id'] ?>&action=reject" class="btn reject" onclick="return confirm('Are you sure you want to reject this bid?');">❌ Reject</a>
                        <a href="bid_action.php?bid_id=<?= $row['bid_id'] ?>&action=requestinfo" class="btn info">ℹ️ Request Info</a>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" action="edit_contract.php" style="display:inline;">
                        <input type="hidden" name="contract_id" value="<?= $row['contract_id'] ?>">
                        <button type="submit" class="btn">✏️ Edit Contract</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No bids to manage at this time.</p>
    <?php endif; ?>
</div>

<?php include('../../../includes/footer.php'); ?>
</body>
</html>
