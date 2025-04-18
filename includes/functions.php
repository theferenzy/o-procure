<?php
// CSRF Token Generator
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token Validator
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function log_admin_action($conn, $admin_id, $action) {
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $admin_id, $action);
    $stmt->execute();
}
