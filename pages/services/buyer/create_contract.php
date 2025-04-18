<?php
require_once('../../../includes/session_security.php');

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
    $buyer_id = $_SESSION['user_id'];

    $itt_document = null;

    // File Upload Handling
    if (isset($_FILES['itt_document']) && $_FILES['itt_document']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../../uploads/';
        $fileTmpPath = $_FILES['itt_document']['tmp_name'];
        $fileName = basename($_FILES['itt_document']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['pdf', 'doc', 'docx'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $safeFileName = uniqid('itt_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $safeFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $itt_document = $safeFileName;
            } else {
                $message = "<p class='text-red-600'>❌ Error uploading file.</p>";
            }
        } else {
            $message = "<p class='text-red-600'>❌ Invalid file type. Only PDF, DOC, and DOCX allowed.</p>";
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO contracts (buyer_id, title, description, budget, tier, deadline, status, itt_document, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', ?, NOW())");
        $stmt->bind_param("issdsss", $buyer_id, $title, $description, $budget, $tier, $deadline, $itt_document);

        if ($stmt->execute()) {
            header("Location: contract_success.php");
            exit();
        } else {
            $message = "<p class='text-red-600'>❌ Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Contract</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css">
</head>

<div class="container mx-auto py-6 px-4">
    <h2 class="text-2xl font-bold mb-6 text-blue-900">📝 Create New Contract</h2>
    <?= $message ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md max-w-2xl mx-auto border border-gray-200">

        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-700">Contract Title</label>
            <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-700">Description</label>
            <textarea name="description" required rows="4" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-700">Budget (₦)</label>
            <input type="number" name="budget" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-700">Tier</label>
            <select name="tier" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Tier</option>
                <option value="Micro">Micro (₦0 - ₦1M)</option>
                <option value="Small">Small (₦1M - ₦10M)</option>
                <option value="Large">Large (₦10M+)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-700">Submission Deadline</label>
            <input type="date" name="deadline" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-6">
            <label class="block mb-1 font-semibold text-gray-700">Upload ITT Document (PDF)</label>
            <input type="file" name="itt_document" accept=".pdf" required class="w-full px-4 py-2 border border-gray-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit" class="w-full bg-blue-900 text-white py-2 px-6 rounded hover:bg-blue-800 transition duration-300">📤 Create Contract</button>
    </form>
</div>


<?php include '../../../includes/footer.php'; ?>
