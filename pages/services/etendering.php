<?php
require_once('../../includes/session_security.php');
require_once('../../config/database.php');
include('../../includes/header.php');

// Fetch all approved contracts
$sql = "SELECT c.*, u.company_name 
        FROM contracts c
        JOIN users u ON c.buyer_id = u.user_id
        WHERE c.status = 'Approved'
        ORDER BY c.created_at DESC";

$result = mysqli_query($conn, $sql);

$isSupplier = isset($_SESSION['role']) && $_SESSION['role'] === 'Supplier';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Tendering - O-Procure</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/o-procure/assets/tailwindcss/tailwind.min.css?v=<?= time() ?>">
</head>
<body>

<div class="container">
    <h2 class="page-title">📢 E-Tendering Notices</h2>

    <div class="onboarding-intro">
        <p>Welcome to the O-Procure E-Tendering Hub.</p>
        <p>Below are current active tenders (contracts) published by buyers. To submit a bid, suppliers must be prequalified and logged in.</p>
    </div>

    <?php if (!$isSupplier): ?>
        <div class="message" style="color: darkred;">
            ⚠️ You must be a <strong>logged-in supplier</strong> and <a href="/o-procure/pages/onboarding/prequalify.php"><strong>prequalified</strong></a> to participate in tenders.
        </div>
    <?php endif; ?>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($contract = mysqli_fetch_assoc($result)): ?>
            <div class="contract-card">
                <h3><?= htmlspecialchars($contract['title']) ?></h3>
                <p><strong>Company:</strong> <?= htmlspecialchars($contract['company_name']) ?></p>
                <p><strong>Tier:</strong> <?= $contract['tier'] ?></p>
                <p><strong>Budget:</strong> ₦<?= number_format($contract['budget'], 2) ?></p>
                <p><strong>Deadline:</strong> <?= $contract['deadline'] ?></p>
                <p><?= nl2br(htmlspecialchars($contract['description'])) ?></p>

                <?php if (!empty($contract['itt_document']) && file_exists('../../uploads/' . $contract['itt_document'])): ?>
                    <a class="btn" href="/o-procure/uploads/<?= $contract['itt_document'] ?>" target="_blank">
                        📥 Download ITT Document
                    </a>
                <?php else: ?>
                    <p class="text-warning">⚠️ No ITT Document attached.</p>
                <?php endif; ?>

                <?php if ($isSupplier): ?>
                    <a class="btn secondary" href="/o-procure/pages/onboarding/place_bid.php?contract_id=<?= $contract['contract_id'] ?>">📤 Place Bid</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No active tenders available at the moment.</p>
    <?php endif; ?>
</div>

<?php include('../../includes/footer.php'); ?>
</body>
</html>
