<?php
session_start();

// Redirect to login if not logged in or not a buyer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header("Location: /o-procure/pages/login.php");
    exit();
}

$buyerName = $_SESSION['fullname'] ?? 'Valued Buyer';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Buyer Dashboard - O-Procure</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css?v=<?= time() ?>">
</head>

<body>
    <?php include '../../../includes/header.php'; ?>

    <main class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4">Welcome, <?= htmlspecialchars($buyerName) ?> 👋</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            <a href="create_contract.php"
                class="bg-blue-900 text-white p-6 rounded shadow hover:bg-blue-800 transition">
                📝 Create New Contract
            </a>
            <a href="managebids.php" class="bg-blue-600 text-white p-6 rounded shadow hover:bg-blue-500 transition">
                📄 Manage Contracts
            </a>
            <a href="mybids.php" class="bg-green-700 text-white p-6 rounded shadow hover:bg-green-600 transition">
                📦 Contracts Archive
            </a>
            <a href="profile.php" class="bg-yellow-600 text-white p-6 rounded shadow hover:bg-yellow-500 transition">
                👤 Profile
            </a>
            <a href="logout.php" class="bg-red-600 text-white p-6 rounded shadow hover:bg-red-500 transition">
                🚪 Logout
            </a>
        </div>
    </main>
</body>

</html>

<?php
include_once '../../../includes/footer.php';
?>