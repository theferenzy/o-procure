<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header("Location: /o-procure/pages/login.php");
    exit();
}

require_once('../../config/database.php');
include('../../includes/header.php');

$contract_id = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;
$supplier_id = $_SESSION['user_id'];
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bid_price = floatval($_POST['bid_price']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);

    // Handle optional document upload
    $doc_path = null;
    if (!empty($_FILES['bid_doc']['name'])) {
        $upload_dir = "../../uploads/bid_doc/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $unique_name = 'bid_' . uniqid() . '_' . basename($_FILES['bid_doc']['name']);
        $target_path = $upload_dir . $unique_name;

        if (move_uploaded_file($_FILES['bid_doc']['tmp_name'], $target_path)) {
            $doc_path = $target_path;
        } else {
            $error = "❌ Failed to upload bid document.";
        }
    }

    if (!$error) {
        $insert = "INSERT INTO bids (contract_id, supplier_id, bid_price, comments, bid_doc, bid_status, created_at)
                   VALUES ('$contract_id', '$supplier_id', '$bid_price', '$comments', '$doc_path', 'Pending', NOW())";

        if (mysqli_query($conn, $insert)) {
            $success = "✅ Bid submitted successfully! Awaiting evaluation.";
        } else {
            $error = "❌ Error saving bid: " . mysqli_error($conn);
        }
    }
}

// Fetch contract details for display
$contract = null;
if ($contract_id) {
    $query = mysqli_query($conn, "SELECT * FROM contracts WHERE contract_id = $contract_id AND status = 'Approved'");
    $contract = mysqli_fetch_assoc($query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Place Bid</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/o-procure/assets/tailwindcss/tailwind.min.css">
</head>

<body>
    <div class="container">
        <h2 class="page-title">📤 Place a Bid</h2>

        <?php if ($contract): ?>
        <div class="contract-summary">
            <h3><?= htmlspecialchars($contract['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($contract['description'])) ?></p>
            <p><strong>Budget:</strong> ₦<?= number_format($contract['budget'], 2) ?></p>
            <p><strong>Deadline:</strong> <?= htmlspecialchars($contract['deadline']) ?></p>
        </div>

        <form method="POST" enctype="multipart/form-data" class="form">
            <label>Bid Price (₦)</label>
            <input type="number" name="bid_price" step="0.01" required>

            <label>Comments or Proposal Notes (Optional)</label>
            <textarea name="comments" rows="4"></textarea>

            <label>Upload Proposal Document (PDF/Word) - Optional</label>
            <input type="file" name="bid_doc" accept=".pdf,.doc,.docx">

            <button type="submit" class="btn">Submit Bid</button>
        </form>


        <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
        <?php elseif ($error): ?>
        <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <?php else: ?>
        <p class="error">❌ Invalid contract or contract not available for bidding.</p>
        <?php endif; ?>
    </div>

    <?php include('../../includes/footer.php'); ?>
</body>

</html>