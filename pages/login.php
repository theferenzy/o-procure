<?php
require_once('../includes/session_security.php'); // Secure session handling

require_once '../includes/functions.php'; // For CSRF token
include '../includes/header.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Validate CSRF token
    if (!validate_csrf_token($_POST['csrf_token'])) {
        echo "<p class='error'>❌ Invalid CSRF token. Please try again.</p>";
    } else {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $conn->real_escape_string($_POST['password']);
        $role = $conn->real_escape_string($_POST['role']);

        $query = "SELECT * FROM users WHERE email='$email' AND role='$role'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'Admin') {
                    header("Location: ../admin/index.php");
                } elseif ($user['role'] == 'Buyer') {
                    header("Location: /o-procure/pages/services/buyer/index.php");
                } elseif ($user['role'] == 'Supplier') {
                    header("Location: /o-procure/pages/onboarding/index.php");
                }
                exit;
            } else {
                echo "<p class='error'>Invalid Password. Please try again.</p>";
            }
        } else {
            echo "<p class='error'>User not found or role mismatch.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css?v=<?= time() ?>">
</head>
<body>

<div class="auth-container">
    <h2>Login to O-Procure</h2>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Login as:</label>
        <select id="role" name="role" required>
            <option value="Admin">Admin</option>
            <option value="Buyer">Buyer</option>
            <option value="Supplier">Supplier</option>
        </select>

        <button type="submit" name="login">Login</button>
        <p>Don't have an account? <a href="createaccount.php">Create Account</a></p>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
