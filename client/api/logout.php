<?php
session_start();

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'No active session found'
        ]);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Optional: Update last activity in database
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
            $stmt->execute([$user_id]);
        } catch (Exception $e) {
            error_log("Error updating last activity during logout: " . $e->getMessage());
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

    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully'
    ]);
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during logout'
    ]);
}
