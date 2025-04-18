<?php
require_once('../../../includes/session_security.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../../config/database.php');
include('../../../includes/header.php');

$buyer_id = $_SESSION['user_id'];
$message = "";
$selected_contract = null;

// Fetch all contracts that belong to this buyer
$contracts_result = mysqli_query($conn, "SELECT contract_id, title FROM contracts WHERE buyer_id = '$buyer_id' ORDER BY created_at DESC");

// Contract selected from dropdown
$contract_id = isset($_POST['contract_id']) ? intval($_POST['contract_id']) : 0;

// Load selected contract
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['load_contract'])) {
    if ($contract_id > 0) {
        $query = "SELECT * FROM contracts WHERE contract_id = '$contract_id' AND buyer_id = '$buyer_id'";
        $result = mysqli_query($conn, $query);
        $selected_contract = mysqli_fetch_assoc($result);
        if (!$selected_contract) {
            $message = "<p class='error'>❌ Contract not found or access denied.</p>";
        }
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contract'])) {
    $contract_id = intval($_POST['contract_id']);

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $budget = floatval($_POST['budget']);
    $tier = mysqli_real_escape_string($conn, $_POST['tier']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);

    $itt_doc = $_POST['existing_doc'] ?? '';

    if (!empty($_FILES['itt_doc']['name'])) {
        $upload_name = 'itt_' . uniqid() . '_' . basename($_FILES['itt_doc']['name']);
        $upload_path = '../../../uploads/' . $upload_name;
        if (move_uploaded_file($_FILES['itt_doc']['tmp_name'], $upload_path)) {
            $itt_doc = $upload_name;
        }
    }

    $update_sql = "UPDATE contracts 
                   SET title='$title', description='$description', budget='$budget', tier='$tier', deadline='$deadline', itt_document='$itt_doc' 
                   WHERE contract_id='$contract_id' AND buyer_id='$buyer_id'";

    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['success'] = "✅ Contract updated successfully.";
        header("Location: managebids.php");
        exit();
    } else {
        $message = "<p class='error'>❌ Error updating: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Contract</title>
    <link rel="stylesheet" href="../../../assets/style.css">
    <link rel="stylesheet" href="../../../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/tailwindcss/tailwind.min.css">
</head>
<body>
<div class="container">
    <h2 class="page-title">✏️ Edit Your Contract</h2>
    <?= $message ?>

    <!-- Contract Dropdown Selection -->
    <form method="POST" class="form" enctype="multipart/form-data">
        <label>Select a Contract to Edit:</label>
        <select name="contract_id" required>
            <option value="">-- Choose Contract --</option>
            <?php while ($row = mysqli_fetch_assoc($contracts_result)): ?>
                <option value="<?= $row['contract_id'] ?>" <?= ($contract_id == $row['contract_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['title']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="load_contract" class="form-btn">🔍 Load Contract</button>
    </form>

    <?php if ($selected_contract): ?>
        <!-- Edit Form -->
        <form method="POST" enctype="multipart/form-data" class="form mt-6">
            <input type="hidden" name="contract_id" value="<?= $selected_contract['contract_id'] ?>">
            <input type="hidden" name="existing_doc" value="<?= $selected_contract['itt_document'] ?>">

            <label>Contract Title</label>
            <input type="text" name="title" required value="<?= htmlspecialchars($selected_contract['title']) ?>">

            <label>Description</label>
            <textarea name="description" required><?= htmlspecialchars($selected_contract['description']) ?></textarea>

            <label>Budget (₦)</label>
            <input type="number" name="budget" required step="0.01" value="<?= $selected_contract['budget'] ?>">

            <label>Tier</label>
            <select name="tier" required>
                <option value="Micro" <?= $selected_contract['tier'] === 'Micro' ? 'selected' : '' ?>>Micro</option>
                <option value="Small" <?= $selected_contract['tier'] === 'Small' ? 'selected' : '' ?>>Small</option>
                <option value="Large" <?= $selected_contract['tier'] === 'Large' ? 'selected' : '' ?>>Large</option>
            </select>

            <label>Deadline</label>
            <input type="date" name="deadline" value="<?= $selected_contract['deadline'] ?>" required>

            <label>Replace ITT Document (optional)</label>
            <input type="file" name="itt_doc" accept="application/pdf">
            <?php if (!empty($selected_contract['itt_document'])): ?>
                <p>Current ITT: <a href="/o-procure/uploads/<?= $selected_contract['itt_document'] ?>" target="_blank">📄 View</a></p>
            <?php endif; ?>

            <button type="submit" name="update_contract" class="form-btn">💾 Save Changes</button>
        </form>
    <?php endif; ?>
</div>

<?php include('../../../includes/footer.php'); ?>
</body>
</html>
