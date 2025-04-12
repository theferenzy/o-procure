<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header('Location: ../../login.php');
    exit();
}
include '../../../includes/database.php';

$user_id = $_SESSION['user_id'];
$message = "";

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $company = $_POST['company_name'];
    $contact = $_POST['contact_number'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, company_name = ?, contact_number = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $full_name, $company, $contact, $user_id);
    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile. Please try again.";
    }
}

// Fetch updated profile
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile - O-Procure</title>
    <link rel="stylesheet" href="../../../assets/style.css">
</head>
<body>
<?php include '../../../includes/header.php'; ?>

<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">My Profile</h2>

    <?php if ($message): ?>
        <p class="text-green-600 mb-2"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 rounded shadow-md w-full max-w-md">
        <label class="block mb-2">Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" class="w-full p-2 mb-4 border rounded" required>

        <label class="block mb-2">Company Name</label>
        <input type="text" name="company_name" value="<?= htmlspecialchars($user['company_name']) ?>" class="w-full p-2 mb-4 border rounded">

        <label class="block mb-2">Contact Number</label>
        <input type="text" name="contact_number" value="<?= htmlspecialchars($user['contact_number']) ?>" class="w-full p-2 mb-4 border rounded">

        <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded hover:bg-blue-800">Update Profile</button>
    </form>
</div>

</body>
</html>
