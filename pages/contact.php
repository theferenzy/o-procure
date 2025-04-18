<?php
require_once('../includes/session_security.php');
require_once('../config/database.php');
include('../includes/header.php');

$success = $error = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $user_id = $_SESSION['user_id'] ?? null;
    $role = $_SESSION['role'] ?? 'Guest';

    if ($name && $email && $subject && $message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (user_id, name, email, role, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $name, $email, $role, $subject, $message);

        if ($stmt->execute()) {
            $success = "✅ Your message has been sent successfully!";
        } else {
            $error = "❌ Failed to send message: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "❌ Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - O-Procure</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/fontawesome/css/all.min.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/tailwindcss/tailwind.min.css?v=<?= time() ?>">
</head>
<body>
<div class="container">
    <h2 class="page-title">Help & Support</h2>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" class="form">
        <label>Your Full Name</label>
        <input type="text" name="name" required value="<?= $_SESSION['full_name'] ?? '' ?>">

        <label>Your Email</label>
        <input type="email" name="email" required value="<?= $_SESSION['email'] ?? '' ?>">

        <label>Subject</label>
        <input type="text" name="subject" required>

        <label>Your Message</label>
        <textarea name="message" rows="5" required></textarea>

        <button type="submit" class="btn">📨 Send Message</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
