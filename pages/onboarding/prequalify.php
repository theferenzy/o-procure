<?php
// Include session security to ensure the user is authenticated and authorized
require_once('../../includes/session_security.php');

// Redirect to login if the user is not a Supplier
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

// Include database connection
require_once('../../config/database.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_SESSION['user_id']; // Get the supplier's user ID from the session
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']); // Sanitize company name input
    $tax_id = mysqli_real_escape_string($conn, $_POST['tax_id']); // Sanitize tax ID input
    $compliance = mysqli_real_escape_string($conn, $_POST['compliance']); // Sanitize compliance certificate input

    // Define the directory for uploading supplier documents
    $target_dir = "../../uploads/supplier_docs/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist

    // Generate unique file names for uploaded documents
    $cac_file = $target_dir . 'cac_' . uniqid() . '_' . basename($_FILES['cac']['name']);
    $portfolio_file = $target_dir . 'portfolio_' . uniqid() . '_' . basename($_FILES['portfolio']['name']);

    // Upload the files and check if both uploads are successful
    $upload_ok = move_uploaded_file($_FILES['cac']['tmp_name'], $cac_file)
              && move_uploaded_file($_FILES['portfolio']['tmp_name'], $portfolio_file);

    // Check if the supplier has already submitted their profile
    $check = mysqli_query($conn, "SELECT id FROM supplier_profiles WHERE supplier_id = '$supplier_id'");
    if (mysqli_num_rows($check) > 0) {
        $error = "You have already submitted your documents. Please wait for verification.";
    } else {
        // Insert the supplier's profile into the database
        $sql = "INSERT INTO supplier_profiles (supplier_id, company_name, tax_id, compliance_doc, cac_doc, portfolio_doc, status)
                VALUES ('$supplier_id', '$company_name', '$tax_id', '$compliance', '$cac_file', '$portfolio_file', 'Pending')";

        if (mysqli_query($conn, $sql)) {
            $success = "Your documents have been submitted for review.";
        } else {
            $error = "Error saving your submission: " . mysqli_error($conn); // Handle database errors
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supplier Pre-Qualification - O-Procure</title>
    <!-- Include stylesheets -->
    <link rel="stylesheet" href="/o-procure/assets/style.css">
    <link rel="stylesheet" href="/o-procure/assets/fontawesome/css/all.min.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/tailwindcss/tailwind.min.css?v=<?= time() ?>">
</head>

<body>

    <!-- Include the header -->
    <?php include('../../includes/header.php'); ?>

    <div class="container">
        <h2>Supplier Pre-Qualification</h2>

        <!-- Display success or error messages -->
        <?php if (!empty($success)): ?>
        <p class="success"><?= $success ?></p>
        <?php elseif (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <!-- Pre-qualification form -->
        <form method="POST" enctype="multipart/form-data" class="form">
            <!-- Input for company name -->
            <label>Company Name</label>
            <input type="text" name="company_name" required>

            <!-- Input for tax identification number -->
            <label>Tax Identification Number (TIN)</label>
            <input type="text" name="tax_id" required>

            <!-- Input for compliance certificate -->
            <label>Safety Compliance Certificate</label>
            <input type="text" name="compliance" required>

            <!-- Input for uploading CAC document -->
            <label>Upload CAC Document (PDF)</label>
            <input type="file" name="cac" accept="application/pdf" required>

            <!-- Input for uploading portfolio document -->
            <label>Upload Portfolio / Previous Projects (PDF)</label>
            <input type="file" name="portfolio" accept="application/pdf" required>

            <!-- Submit button -->
            <button type="submit" class="btn">Submit for Verification</button>
        </form>
    </div>

    <!-- Include the footer -->
    <?php include('../../includes/footer.php'); ?>
</body>

</html>