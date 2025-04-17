<?php
session_start();
require_once('../config/database.php');

// Fetch approved contracts
$sql = "SELECT * FROM contracts WHERE status = 'approved'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Available Contracts - O-Procure</title>
    <link rel="stylesheet" href="../../../assets/style.css">
</head>

<body>

    <?php include('../includes/header.php'); ?>

    <div class="contracts-container">
        <h2 class="text-2xl font-bold mb-6 text-blue-900">📄 Available Contracts</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="contract-card bg-white border border-gray-200 p-4 mb-4 shadow rounded">
            <h3 class="text-xl font-semibold text-blue-800"><?php echo $row['title']; ?></h3>
            <p class="text-gray-700 mt-2"><?php echo $row['description']; ?></p>

            <?php if (!empty($row['itt_document'])): ?>
            <a href="/o-procure/uploads/<?php echo htmlspecialchars($row['itt_document']); ?>" target="_blank"
                class="text-blue-700 underline mt-2 inline-block">
                📄 Download ITT Document
            </a>
            <?php endif; ?>

            <p class="text-sm text-gray-500 mt-1">Deadline: <?php echo $row['deadline']; ?></p>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Buyer'): ?>
            <a href="place_bid.php?contract_id=<?php echo $row['id']; ?>"
                class="inline-block mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Place Bid</a>
            <?php else: ?>
            <p class="text-red-500 italic mt-3">Login as a supplier to participate in this bid.</p>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>No contracts available at the moment.</p>
        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>

</html>