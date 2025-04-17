<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../config/database.php');
include('../../includes/header.php');

// Fetch approved contracts and buyer info
$sql = "SELECT contracts.*, users.full_name AS buyer_name, users.company_name 
        FROM contracts 
        JOIN users ON contracts.buyer_id = users.user_id 
        WHERE contracts.status = 'Approved' 
        ORDER BY contracts.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contract Bidding</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/o-procure/assets/tailwindcss/tailwind.min.css">
</head>
<body>

<div class="container">
    <h2 class="page-title">📑 Approved Contracts Available for Bidding</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="contract-card">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <p><strong>Budget:</strong> ₦<?= number_format($row['budget'], 2) ?></p>
                <p><strong>Deadline:</strong> <?= htmlspecialchars($row['deadline']) ?></p>
                <p><strong>Tier:</strong> <?= htmlspecialchars($row['tier']) ?></p>
                <p><strong>Posted by:</strong> <?= htmlspecialchars($row['company_name'] ?: $row['buyer_name']) ?></p>

                <?php if (!empty($row['itt_document'])): ?>
                    <?php
                    $ittFile = basename($row['itt_document']);
                    $fullPath = $_SERVER['DOCUMENT_ROOT'] . "/o-procure/uploads/" . $ittFile;
                    ?>
                    <?php if (file_exists($fullPath)): ?>
                        <p>
                            <a href="/o-procure/uploads/<?= urlencode($ittFile) ?>" class="btn" target="_blank">
                                📥 Download ITT Document
                            </a>
                        </p>
                    <?php else: ?>
                        <p class="text-warning">⚠️ ITT file reference found, but file is missing.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-warning">⚠️ No ITT document attached</p>
                <?php endif; ?>

                <a href="place_bid.php?contract_id=<?= $row['contract_id'] ?>" class="btn secondary">📤 Place Bid</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No approved contracts available at this time.</p>
    <?php endif; ?>
</div>

<?php include('../../includes/footer.php'); ?>
</body>
</html>
