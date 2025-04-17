<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header("Location: /o-procure/pages/login.php");
    exit();
}

include '../../includes/header.php';
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
