<?php
require_once('../../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header("Location: /o-procure/pages/login.php");
    exit();
}

require_once('../../config/database.php');
include '../../includes/header.php';

$supplier_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'] ?? 'Supplier';

// Fetch supplier verification status
$profile_query = mysqli_query($conn, "SELECT status FROM supplier_profiles WHERE supplier_id = '$supplier_id'");
$profile = mysqli_fetch_assoc($profile_query);
$isVerified = $profile && $profile['status'] === 'Approved';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Onboarding</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/fontawesome/css/all.min.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/tailwindcss/tailwind.min.css?v=<?= time() ?>">
</head>
<body>

<div class="container">
    <h1 class="page-title">Supplier Onboarding</h1>

    <!-- ✅ Badge or Warning -->
    <?php if ($isVerified): ?>
        <p style="background-color: white; color: #012E40; font-weight: bold; display: inline-block; padding: 8px 16px; border-radius: 8px; margin-bottom: 20px;">
        ⭐ Verified Supplier
        </p>
    <?php else: ?>
        <p class="text-warning" style="color: darkred; font-weight: bold;">⚠️ You are not yet verified. Complete your prequalification and wait for admin approval to bid on contracts.</p>
    <?php endif; ?>

    <div class="onboarding-intro">
        <p>Welcome to O-Procure’s Supplier Onboarding Hub.</p>
        <p>To ensure fairness and transparency in our procurement process, all suppliers must complete the onboarding and pre-qualification steps below before accessing contracts.</p>
    </div>

    <div class="onboarding-steps">
        <h2>🛠️ Steps to Get Started:</h2>
        <ol>
            <li><strong>Complete Pre-Qualification:</strong> Upload your company documents and verify your supplier status.</li>
            <li><strong>Explore Available Contracts:</strong> Once approved, you can view and bid for contracts based on your tier.</li>
        </ol>
    </div>

    <div class="onboarding-links">
        <a class="btn" href="/o-procure/pages/onboarding/prequalify.php">🚀 Start Pre-Qualification</a>
        <a class="btn secondary" href="/o-procure/pages/onboarding/faq.php">📘 Supplier FAQs</a>
        <a class="btn" href="/o-procure/pages/onboarding/edit_bid.php">🛠 Edit My Bids</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
