<?php require_once('../includes/session_security.php'); ?>
<?php include '../includes/header.php'; ?>
<?php include '../config/database.php'; ?>

<div class="auth-container">
    <h2>Create an Account</h2>
    <form method="POST" action="">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Register as:</label>
        <select id="role" name="role" required>
            <option value="Admin">Admin</option>
            <option value="Buyer">Buyer</option>
            <option value="Supplier">Supplier</option>
        </select>

        <label for="company_name">Company Name (Optional):</label>
        <input type="text" id="company_name" name="company_name">

        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" required>

        <button type="submit" name="create_account">Create Account</button>
    </form>
</div>

<?php
if (isset($_POST['create_account'])) {
    $fullName = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $conn->real_escape_string($_POST['role']);
    $companyName = $conn->real_escape_string($_POST['company_name']);
    $contactNumber = $conn->real_escape_string($_POST['contact_number']);

    $query = "INSERT INTO users (full_name, email, password, role, company_name, contact_number, status) 
              VALUES ('$fullName', '$email', '$password', '$role', '$companyName', '$contactNumber', 'Pending')";

    if ($conn->query($query)) {
        echo "<p class='success'>Account created successfully. Please wait for admin approval.</p>";
    } else {
        echo "<p class='error'>Error creating account: " . $conn->error . "</p>";
    }
}
?>

<?php include '../includes/footer.php'; ?>
