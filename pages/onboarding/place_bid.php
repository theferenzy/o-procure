<?php
// Include session security to ensure the user is authenticated and authorized
require_once('../../includes/session_security.php');

// Redirect to login if the user is not a Supplier
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header("Location: /o-procure/pages/login.php");
    exit();
}

// Include necessary files for database connection and utility functions
require_once('../../includes/functions.php');
require_once('../../config/database.php');
include('../../includes/header.php');

// Get the contract ID from the query string and the supplier ID from the session
$contract_id = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;
$supplier_id = $_SESSION['user_id'];
$success = $error = "";

// Check if the supplier is verified
$verified_result = mysqli_query($conn, "SELECT status FROM supplier_profiles WHERE supplier_id = '$supplier_id' AND status = 'Approved'");
$is_verified = mysqli_num_rows($verified_result) > 0;

// Fetch contract details if a valid contract ID is provided
$contract = null;
if ($contract_id > 0) {
    $query = mysqli_query($conn, "SELECT * FROM contracts WHERE contract_id = $contract_id AND status = 'Approved'");
    $contract = mysqli_fetch_assoc($query);
}

// Handle bid submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_verified && $contract) {

    // Validate CSRF token for security
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "❌ Invalid CSRF token.";
    }

    // Get bid price and comments from the form
    $bid_price = floatval($_POST['bid_price']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);
    $doc_path = null;

    // Handle file upload if a document is provided
    if (!$error && !empty($_FILES['bid_doc']['name'])) {
        $allowed_ext = ['pdf', 'doc', 'docx']; // Allowed file types
        $ext = strtolower(pathinfo($_FILES['bid_doc']['name'], PATHINFO_EXTENSION));

        // Validate file type and size
        if (!in_array($ext, $allowed_ext)) {
            $error = "❌ Invalid file type. Only PDF, DOC, or DOCX allowed.";
        } elseif ($_FILES['bid_doc']['size'] > 5 * 1024 * 1024) { // Max size: 5MB
            $error = "❌ File too large. Max allowed size is 5MB.";
        } else {
            // Create upload directory if it doesn't exist
            $upload_dir = "../../uploads/bid_doc/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            // Generate a unique name for the uploaded file
            $unique_name = 'bid_' . uniqid() . '.' . $ext;
            $target_path = $upload_dir . $unique_name;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['bid_doc']['tmp_name'], $target_path)) {
                $doc_path = $unique_name;
            } else {
                $error = "❌ Failed to upload bid document.";
            }
        }
    }

    // Insert bid into the database if no errors occurred
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Bid</title>
    <!-- Include stylesheets -->
    <link rel="stylesheet" href="/o-procure/assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/o-procure/assets/tailwindcss/tailwind.min.css">
</head>

<body>
<div class="container">
    <h2 class="page-title">📤 Place a Bid</h2>

    <!-- Display error if the supplier is not verified -->
    <?php if (!$is_verified): ?>
        <p class="error">❌ You are not verified to place bids. Please wait for admin approval.</p>
    <!-- Display error if the contract is invalid -->
    <?php elseif (!$contract): ?>
        <p class="error">❌ Invalid contract or not available for bidding.</p>
    <?php else: ?>
        <!-- Display contract summary -->
        <div class="contract-summary">
            <h3><?= htmlspecialchars($contract['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($contract['description'])) ?></p>
            <p><strong>Budget:</strong> ₦<?= number_format($contract['budget'], 2) ?></p>
            <p><strong>Deadline:</strong> <?= htmlspecialchars($contract['deadline']) ?></p>
        </div>

        <!-- Bid submission form -->
        <form method="POST" enctype="multipart/form-data" class="form">
            <!-- CSRF token for security -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token()) ?>">
            
            <!-- Input for bid price -->
            <label>Bid Price (₦)</label>
            <input type="number" name="bid_price" step="0.01" required>

            <!-- Input for comments -->
            <label>Comments or Proposal Notes (Optional)</label>
            <textarea name="comments" rows="4"></textarea>

            <!-- Input for file upload -->
            <label>Upload Proposal Document (PDF/Word) - Optional</label>
            <input type="file" name="bid_doc" accept=".pdf,.doc,.docx">

            <!-- Submit button -->
            <button type="submit" class="btn">Submit Bid</button>
        </form>

        <!-- Display success or error messages -->
        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include('../../includes/footer.php'); ?>
</body>
</html><?php
require_once('../../includes/session_security.php');

// Redirect to login if the user is not a Supplier
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header("Location: /o-procure/pages/login.php");
    exit();
}


require_once('../../includes/functions.php');
require_once('../../config/database.php');
include('../../includes/header.php');

// Get the contract ID from the query string and the supplier ID from the session
$contract_id = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;
$supplier_id = $_SESSION['user_id'];
$success = $error = "";

// Check if the supplier is verified
$verified_result = mysqli_query($conn, "SELECT status FROM supplier_profiles WHERE supplier_id = '$supplier_id' AND status = 'Approved'");
$is_verified = mysqli_num_rows($verified_result) > 0;

// Fetch contract details if a valid contract ID is provided
$contract = null;
if ($contract_id > 0) {
    $query = mysqli_query($conn, "SELECT * FROM contracts WHERE contract_id = $contract_id AND status = 'Approved'");
    $contract = mysqli_fetch_assoc($query);
}

// Handle bid submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_verified && $contract) {

    // Validate CSRF token for security
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "❌ Invalid CSRF token.";
    }

    // Get bid price and comments from the form
    $bid_price = floatval($_POST['bid_price']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);
    $doc_path = null;

    // Handle file upload if a document is provided
    if (!$error && !empty($_FILES['bid_doc']['name'])) {
        $allowed_ext = ['pdf', 'doc', 'docx']; // Allowed file types
        $ext = strtolower(pathinfo($_FILES['bid_doc']['name'], PATHINFO_EXTENSION));

        // Validate file type and size
        if (!in_array($ext, $allowed_ext)) {
            $error = "❌ Invalid file type. Only PDF, DOC, or DOCX allowed.";
        } elseif ($_FILES['bid_doc']['size'] > 5 * 1024 * 1024) { // Max size: 5MB
            $error = "❌ File too large. Max allowed size is 5MB.";
        } else {
            // Create upload directory if it doesn't exist
            $upload_dir = "../../uploads/bid_doc/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            // Generate a unique name for the uploaded file
            $unique_name = 'bid_' . uniqid() . '.' . $ext;
            $target_path = $upload_dir . $unique_name;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['bid_doc']['tmp_name'], $target_path)) {
                $doc_path = $unique_name;
            } else {
                $error = "❌ Failed to upload bid document.";
            }
        }
    }

    // Insert bid into the database if no errors occurred
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

    <!-- Display error if the supplier is not verified -->
    <?php if (!$is_verified): ?>
        <p class="error">❌ You are not verified to place bids. Please wait for admin approval.</p>
    <!-- Display error if the contract is invalid -->
    <?php elseif (!$contract): ?>
        <p class="error">❌ Invalid contract or not available for bidding.</p>
    <?php else: ?>
        <!-- Display contract summary -->
        <div class="contract-summary">
            <h3><?= htmlspecialchars($contract['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($contract['description'])) ?></p>
            <p><strong>Budget:</strong> ₦<?= number_format($contract['budget'], 2) ?></p>
            <p><strong>Deadline:</strong> <?= htmlspecialchars($contract['deadline']) ?></p>
        </div>

        <!-- Bid submission form -->
        <form method="POST" enctype="multipart/form-data" class="form">
            <!-- CSRF token for security -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token()) ?>">
            
            <!-- Input for bid price -->
            <label>Bid Price (₦)</label>
            <input type="number" name="bid_price" step="0.01" required>

            <!-- Input for comments -->
            <label>Comments or Proposal Notes (Optional)</label>
            <textarea name="comments" rows="4"></textarea>

            <!-- Input for file upload -->
            <label>Upload Proposal Document (PDF/Word) - Optional</label>
            <input type="file" name="bid_doc" accept=".pdf,.doc,.docx">

            <!-- Submit button -->
            <button type="submit" class="btn">Submit Bid</button>
        </form>

        <!-- Display success or error messages -->
        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
    <?php endif; ?>
</div>


<?php include('../../includes/footer.php'); ?>
</body>
</html>