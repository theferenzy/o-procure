<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header('Location: ../../login.php');
    exit();
}

include '../../../config/database.php';
include '../../../includes/header.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $budget = floatval($_POST['budget']);
    $tier = $conn->real_escape_string($_POST['tier']);
    $deadline = $conn->real_escape_string($_POST['deadline']);
    $buyer_id = $_SESSION['user_id']; // assuming logged-in buyer's ID is saved

    $sql = "INSERT INTO contracts (buyer_id, title, description, budget, tier, deadline, status, created_at)
            VALUES ('$buyer_id', '$title', '$description', '$budget', '$tier', '$deadline', 'Pending Approval', NOW())";

    if ($conn->query($sql)) {
        $message = "<p class='text-green-600'>✅ Contract created successfully! Awaiting admin approval.</p>";
    } else {
        $message = "<p class='text-red-600'>❌ Error: " . $conn->error . "</p>";
    }
}
?>

<div class="container mx-auto py-6 px-4">
    <h2 class="text-2xl font-bold mb-4">Create New Contract</h2>
    <?= $message ?>

    <form method="POST" class="bg-white p-6 rounded shadow-md max-w-xl">
        <label class="block mb-2 font-semibold">Contract Title</label>
        <input type="text" name="title" required class="w-full p-2 border rounded mb-4">

        <label class="block mb-2 font-semibold">Description</label>
        <textarea name="description" required class="w-full p-2 border rounded mb-4"></textarea>

        <label class="block mb-2 font-semibold">Budget (₦)</label>
        <input type="number" name="budget" required class="w-full p-2 border rounded mb-4" step="0.01">

        <label class="block mb-2 font-semibold">Tier</label>
        <select name="tier" required class="w-full p-2 border rounded mb-4">
            <option value="">Select Tier</option>
            <option value="Micro">Micro (₦0 - ₦1M)</option>
            <option value="Small">Small (₦1M - ₦10M)</option>
            <option value="Large">Large (₦10M+)</option>
        </select>

        <label class="block mb-2 font-semibold">Submission Deadline</label>
        <input type="date" name="deadline" required class="w-full p-2 border rounded mb-6">

        <button type="submit" class="bg-blue-900 text-white px-6 py-2 rounded">Create Contract</button>
    </form>
</div>

<?php include '../../../includes/footer.php'; ?>
