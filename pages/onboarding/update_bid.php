<?php
require_once('../../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../config/database.php');
include('../../includes/header.php');

$supplier_id = $_SESSION['user_id'];
$bid_id = isset($_GET['bid_id']) ? intval($_GET['bid_id']) : 0;
$message = "";

$query = "SELECT b.*, c.title AS contract_title 
          FROM bids b 
          JOIN contracts c ON b.contract_id = c.contract_id 
          WHERE b.bid_id = '$bid_id' AND b.supplier_id = '$supplier_id'";
$result = mysqli_query($conn, $query);
$bid = mysqli_fetch_assoc($result);

if (!$bid) {
    $message = "<p class='error'>❌ Bid not found or access denied.</p>";
} elseif ($bid['is_awarded']) {
    $message = "<p class='error'>⚠️ You cannot edit this bid because it has been awarded.</p>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$bid['is_awarded']) {
    $new_price = floatval($_POST['bid_price']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);
    $bid_doc = $bid['bid_doc'];

    if (!empty($_FILES['bid_doc']['name'])) {
        $filename = 'proposal_' . uniqid() . '_' . basename($_FILES['bid_doc']['name']);
        $upload_path = '../../uploads/bid_doc/' . $filename;
        if (move_uploaded_file($_FILES['bid_doc']['tmp_name'], $upload_path)) {
            $bid_doc = $filename;
        }
    }

    $update_sql = "UPDATE bids 
                   SET bid_price='$new_price', comments='$comments', bid_doc='$bid_doc', bid_status='Pending', updated_at=NOW() 
                   WHERE bid_id='$bid_id' AND supplier_id='$supplier_id'";

    if (mysqli_query($conn, $update_sql)) {
        $message = "<p class='success'>✅ Bid updated and resubmitted for review.</p>";
        $result = mysqli_query($conn, $query);
        $bid = mysqli_fetch_assoc($result);
    } else {
        $message = "<p class='error'>❌ Update failed: " . mysqli_error($conn) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Bid</title>
    <link rel="stylesheet" href="../../assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../assets/tailwindcss/tailwind.min.css">
</head>

<div class="container">
    <h2 class="page-title">✏️ Edit Bid for: <?= $bid ? htmlspecialchars($bid['contract_title']) : '' ?></h2>
    <?= $message ?>

    <?php if ($bid && !$bid['is_awarded']): ?>
        <form method="POST" enctype="multipart/form-data" class="form">
            <label>Bid Price (₦)</label>
            <input type="number" name="bid_price" step="0.01" required value="<?= $bid['bid_price'] ?>">

            <label>Comments</label>
            <textarea name="comments" required><?= htmlspecialchars($bid['comments']) ?></textarea>

            <label>Replace Proposal Document (PDF)</label>
            <input type="file" name="bid_doc" accept="application/pdf">
            <?php if (!empty($bid['bid_doc'])): ?>
                <p>Current Doc: <a href="/o-procure/uploads/bid_doc/<?= $bid['bid_doc'] ?>" target="_blank">📄 View</a></p>
            <?php endif; ?>

            <button type="submit" class="form-btn">💾 Update Bid</button>
        </form>
    <?php elseif (!$bid): ?>
    <?php else: ?>
        <p class="text-warning">🔒 Editing is disabled for awarded bids.</p>
    <?php endif; ?>
</div>

<?php include('../../includes/footer.php'); ?>