<?php
require_once('../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

require_once('../config/database.php');
require_once('../includes/functions.php');
include('../includes/header.php');

// Fetch logs
$sql = "SELECT a.*, u.full_name 
        FROM admin_logs a 
        JOIN users u ON a.admin_id = u.user_id 
        ORDER BY a.timestamp DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Audit Logs</title>
    <link rel="stylesheet" href="../assets/style.css?v=<?= time() ?>">
</head>
<body>
<div class="container">
    <h2 class="page-title">📜 Admin Audit Trail Logs</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['full_name']) ?></td>
                        <td><?= nl2br(htmlspecialchars($log['action'])) ?></td>
                        <td><?= date("Y-m-d H:i:s", strtotime($log['timestamp'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No audit logs found.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
