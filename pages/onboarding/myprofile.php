<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supplier') {
    header('Location: /o-procure/pages/login.php');
    exit();
}

require_once('../../config/database.php');
include('../../includes/header.php');

$supplier_id = $_SESSION['user_id'];
$message = "";

// Fetch profile
$result = mysqli_query($conn, "SELECT * FROM supplier_profiles WHERE supplier_id = '$supplier_id'");
$profile = mysqli_fetch_assoc($result);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = mysqli_real_escape_string($conn, $_POST['company_name']);
    $tax_id = mysqli_real_escape_string($conn, $_POST['tax_id']);
    $compliance = mysqli_real_escape_string($conn, $_POST['compliance']);

    $cac = $profile['cac_doc'];
    $portfolio = $profile['portfolio_doc'];

    if (!empty($_FILES['cac']['name'])) {
        $cac_name = 'cac_' . uniqid() . '_' . basename($_FILES['cac']['name']);
        $cac_path = "../../uploads/supplier_docs/$cac_name";
        move_uploaded_file($_FILES['cac']['tmp_name'], $cac_path);
        $cac = $cac_name;
    }

    if (!empty($_FILES['portfolio']['name'])) {
        $portfolio_name = 'portfolio_' . uniqid() . '_' . basename($_FILES['portfolio']['name']);
        $portfolio_path = "../../uploads/supplier_docs/$portfolio_name";
        move_uploaded_file($_FILES['portfolio']['tmp_name'], $portfolio_path);
        $portfolio = $portfolio_name;
    }

    $sql = "UPDATE supplier_profiles 
            SET company_name='$company', tax_id='$tax_id', compliance_doc='$compliance',
                cac_doc='$cac', portfolio_doc='$portfolio' 
            WHERE supplier_id = '$supplier_id'";

    if (mysqli_query($conn, $sql)) {
        $message = "<p class='success'>✅ Profile updated successfully!</p>";
        $profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM supplier_profiles WHERE supplier_id = '$supplier_id'"));
    } else {
        $message = "<p class='error'>❌ Error updating: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css">
    <link rel="stylesheet" href="/o-procure/assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/o-procure/assets/tailwindcss/tailwind.min.css">
</head>


<div class="container">
    <h2 class="page-title">👤 Edit Profile</h2>
    <?= $message ?>

    <form method="POST" enctype="multipart/form-data" class="form">
        <label>Company Name</label>
        <input type="text" name="company_name" required value="<?= htmlspecialchars($profile['company_name'] ?? '') ?>">

        <label>Tax ID</label>
        <input type="text" name="tax_id" required value="<?= htmlspecialchars($profile['tax_id'] ?? '') ?>">

        <label>Compliance Certificate</label>
        <input type="text" name="compliance" required value="<?= htmlspecialchars($profile['compliance_doc'] ?? '') ?>">

        <label>CAC Document (PDF)</label>
        <input type="file" name="cac" accept="application/pdf">
        <?php if (!empty($profile['cac_doc'])): ?>
            <p>Current: <a href="/o-procure/uploads/supplier_docs/<?= $profile['cac_doc'] ?>" target="_blank">📄 View CAC</a></p>
        <?php endif; ?>

        <label>Portfolio (PDF)</label>
        <input type="file" name="portfolio" accept="application/pdf">
        <?php if (!empty($profile['portfolio_doc'])): ?>
            <p>Current: <a href="/o-procure/uploads/supplier_docs/<?= $profile['portfolio_doc'] ?>" target="_blank">📄 View Portfolio</a></p>
        <?php endif; ?>

        <button type="submit" class="form-btn">💾 Save Changes</button>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
