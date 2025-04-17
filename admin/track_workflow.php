<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once '../config/database.php';
include '../includes/header.php';

// Get all contracts
$query = "SELECT c.*, u.full_name AS buyer_name 
          FROM contracts c
          JOIN users u ON c.buyer_id = u.user_id
          ORDER BY c.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h2 class="page-title">🔁 Procurement Workflow Tracker</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Contract</th>
                    <th>Buyer</th>
                    <th>Status</th>
                    <th>Stage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($contract = mysqli_fetch_assoc($result)): 
                    $contract_id = $contract['contract_id'];
                    $title = $contract['title'];
                    $buyer = $contract['buyer_name'];
                    $status = $contract['status'];

                    // Check workflow stages
                    $bids_q = mysqli_query($conn, "SELECT * FROM bids WHERE contract_id = '$contract_id'");
                    $has_bids = mysqli_num_rows($bids_q) > 0;

                    $evaluated = mysqli_query($conn, "SELECT * FROM bids WHERE contract_id = '$contract_id' AND bid_status IN ('Approved', 'Rejected')");
                    $evaluated_count = mysqli_num_rows($evaluated);

                    $awarded = mysqli_query($conn, "SELECT * FROM bids WHERE contract_id = '$contract_id' AND is_awarded = 1");
                    $is_awarded = mysqli_num_rows($awarded) > 0;

                    // Determine stage
                    if ($is_awarded) {
                        $stage = "🏆 Awarded";
                    } elseif ($evaluated_count > 0) {
                        $stage = "🧐 Bid Evaluated";
                    } elseif ($has_bids) {
                        $stage = "📬 Bids Submitted";
                    } elseif ($status === 'Approved') {
                        $stage = "🧾 Approved by Admin";
                    } else {
                        $stage = "📝 Created";
                    }
                ?>
                <tr>
                    <td><?= htmlspecialchars($title) ?></td>
                    <td><?= htmlspecialchars($buyer) ?></td>
                    <td><?= htmlspecialchars($status) ?></td>
                    <td><?= $stage ?></td>
                    <td><a href="review_contracts.php" class="btn">🔍 View</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No contracts found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
