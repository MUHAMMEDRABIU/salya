<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $currentPassword = $data['current_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword)) {
        throw new Exception('Current password and new password are required');
    }

    if (strlen($newPassword) < 8) {
        throw new Exception('New password must be at least 8 characters long');
    }

    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $storedPassword = $stmt->fetchColumn();

    if (!$storedPassword || !password_verify($currentPassword, $storedPassword)) {
        throw new Exception('Current password is incorrect');
    }

    // Hash new password
    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$hashedNewPassword, $user_id]);

    if (!$result) {
        throw new Exception('Failed to update password');
    }

    // Log security event
    error_log("Password changed for user ID: $user_id at " . date('Y-m-d H:i:s'));

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully'
    ]);

} catch (Exception $e) {
    error_log('Change password error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>