<?php
require_once('../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}

require_once('../config/database.php');
include('../includes/header.php');

// Handle mark as read
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $msg_id = intval($_GET['mark_read']);
    mysqli_query($conn, "UPDATE contact_messages SET status = 'Read' WHERE id = $msg_id");
    $_SESSION['success'] = "✅ Message marked as read.";
    header("Location: contact_messages.php");
    exit();
}

// Fetch all contact messages
$result = mysqli_query($conn, "SELECT cm.*, u.full_name 
    FROM contact_messages cm 
    LEFT JOIN users u ON cm.user_id = u.user_id 
    ORDER BY cm.submitted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Support Messages</title>
    <link rel="stylesheet" href="../assets/style.css?v=<?= time() ?>">
</head>
<body>
<div class="container">
    <h2 class="page-title">📨 Help & Support Messages</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name'] ?: $row['name']) ?></td>
                    <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                    <td>
                        <?php if ($row['status'] === 'New'): ?>
                            <span class="text-yellow-600 font-semibold">New</span>
                        <?php else: ?>
                            <span class="text-green-600 font-semibold">Read</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date("Y-m-d H:i", strtotime($row['submitted_at'])) ?></td>
                    <td>
                        <?php if ($row['status'] === 'New'): ?>
                            <a href="contact_messages.php?mark_read=<?= $row['id'] ?>" class="btn">✅ Mark as Read</a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No support messages yet.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
