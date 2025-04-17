<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../config/database.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_SESSION['user_id']; // Assuming user_id is stored on login
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $tax_id = mysqli_real_escape_string($conn, $_POST['tax_id']);
    $compliance = mysqli_real_escape_string($conn, $_POST['compliance']);

    // Upload files
    $target_dir = "../../uploads/supplier_docs/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $cac_file = $target_dir . 'cac_' . uniqid() . '_' . basename($_FILES['cac']['name']);
    $portfolio_file = $target_dir . 'portfolio_' . uniqid() . '_' . basename($_FILES['portfolio']['name']);

    $upload_ok = move_uploaded_file($_FILES['cac']['tmp_name'], $cac_file)
              && move_uploaded_file($_FILES['portfolio']['tmp_name'], $portfolio_file);

    // Check if supplier already submitted profile
$check = mysqli_query($conn, "SELECT id FROM supplier_profiles WHERE supplier_id = '$supplier_id'");
if (mysqli_num_rows($check) > 0) {
    $error = "You have already submitted your documents. Please wait for verification.";
} else {
    $sql = "INSERT INTO supplier_profiles (supplier_id, company_name, tax_id, compliance_doc, cac_doc, portfolio_doc, status)
            VALUES ('$supplier_id', '$company_name', '$tax_id', '$compliance', '$cac_file', '$portfolio_file', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        $success = "Your documents have been submitted for review.";
    } else {
        $error = "Error saving your submission: " . mysqli_error($conn);
    }
}

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supplier Pre-Qualification - O-Procure</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css">
</head>

<body>

    <?php include('../../includes/header.php'); ?>

    <div class="container">
        <h2>Supplier Pre-Qualification</h2>

        <?php if (!empty($success)): ?>
        <p class="success"><?= $success ?></p>
        <?php elseif (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form">
            <label>Company Name</label>
            <input type="text" name="company_name" required>

            <label>Tax Identification Number (TIN)</label>
            <input type="text" name="tax_id" required>

            <label>Safety Compliance Certificate</label>
            <input type="text" name="compliance" required>

            <label>Upload CAC Document (PDF)</label>
            <input type="file" name="cac" accept="application/pdf" required>

            <label>Upload Portfolio / Previous Projects (PDF)</label>
            <input type="file" name="portfolio" accept="application/pdf" required>

            <button type="submit" class="btn">Submit for Verification</button>
        </form>
    </div>

    <?php include('../../includes/footer.php'); ?>
</body>

</html>