<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Optional: Verify user's role if admin-specific access is required
if (isset($require_admin) && $require_admin && $_SESSION['role'] !== 'admin') {
    header('Location: unauthorized.php'); // Redirect to an unauthorized access page
    exit;
}
?>
