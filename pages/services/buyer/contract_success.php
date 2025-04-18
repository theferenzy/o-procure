<?php
require_once('../../../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header("Location: ../../login.php");
    exit();
}

include '../../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Submission Confirmation</title>
    <link rel="stylesheet" href="../../../assets/style.css?v=<?= time() ?>">
</head>
<body>
    
</body>
</html>

<div class="container mx-auto p-6 text-center">
    <h2 class="text-3xl font-bold text-green-700 mb-4">🎉 Contract Submitted Successfully!</h2>
    <p class="text-lg mb-6">Your contract has been submitted and is pending approval by the O-Procure Admin Team for compliance review.</p>
    
    <div class="flex justify-center space-x-4">
        <a href="index.php" class="bg-blue-900 text-white px-5 py-2 rounded hover:bg-blue-800">🏠 Back to Dashboard</a>
        <a href="create_contract.php" class="bg-green-700 text-white px-5 py-2 rounded hover:bg-green-600">➕ Create Another</a>
        <a href="mybids.php" class="bg-yellow-600 text-white px-5 py-2 rounded hover:bg-yellow-500">📦 View My Orders</a>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
