<?php
session_start();

// Log the logout activity if admin is logged in
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    error_log("Admin logout: Admin ID $admin_id logged out at " . date('Y-m-d H:i:s'));

    // Optional: Update database if PDO is available
    try {
        require_once 'initialize.php';
        $stmt = $pdo->prepare("UPDATE admins SET last_activity = NOW() WHERE id = ?");
        $stmt->execute([$admin_id]);
    } catch (Exception $e) {
        error_log("Error updating admin last activity during logout: " . $e->getMessage());
    }
}

// Clear all session data
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to admin login page
header('Location: index.php');
exit();
