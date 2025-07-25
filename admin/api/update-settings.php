<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing admin ID']);
    exit;
}

$adminId = (int)$input['admin_id'];
$mfaEnabled = !empty($input['mfa_enabled']) ? 1 : 0;
$loginNotifications = !empty($input['login_notifications']) ? 1 : 0;
$sessionTimeout = isset($input['session_timeout']) ? (int)$input['session_timeout'] : 60;
$systemAlerts = !empty($input['system_alerts']) ? 1 : 0;
$userActivity = !empty($input['user_activity']) ? 1 : 0;

try {
    $stmt = $pdo->prepare(
        "UPDATE admins SET 
            mfa_enabled = :mfa_enabled,
            login_notifications = :login_notifications,
            session_timeout = :session_timeout,
            system_alerts = :system_alerts,
            user_activity = :user_activity
        WHERE id = :admin_id"
    );
    $stmt->execute([
        ':mfa_enabled' => $mfaEnabled,
        ':login_notifications' => $loginNotifications,
        ':session_timeout' => $sessionTimeout,
        ':system_alerts' => $systemAlerts,
        ':user_activity' => $userActivity,
        ':admin_id' => $adminId
    ]);

    echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
} catch (PDOException $e) {
    error_log("Admin settings update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update settings']);
}