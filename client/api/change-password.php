<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../util/util.php';
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

    if (strlen($newPassword) < 6) {
        throw new Exception('New password must be at least 6 characters long');
    }

    // Verify current password
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $storedPassword = $stmt->fetchColumn();

    if (!$storedPassword || !password_verify($currentPassword, $storedPassword)) {
        throw new Exception('Current password is incorrect');
    }

    // Hash new password
    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$hashedNewPassword, $user_id]);

    if (!$result) {
        throw new Exception('Failed to update password');
    }

    // Push notification for password change
    pushNotification(
        $pdo,
        $user_id,
        'Password Changed',
        'Your password was updated successfully.',
        'security',
        'fas fa-key text-red-600',
        'bg-red-100',
        'View Profile'
    );

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
