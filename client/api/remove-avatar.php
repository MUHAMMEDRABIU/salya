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
    // Get current avatar filename
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $currentAvatar = $stmt->fetchColumn();

    // Update database to remove avatar reference
    $stmt = $pdo->prepare("UPDATE users SET avatar = NULL, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$user_id]);

    if (!$result) {
        throw new Exception('Failed to remove avatar from database');
    }

    // Delete avatar file if it exists
    if ($currentAvatar) {
        $uploadDir = __DIR__ . '/../../assets/uploads/avatars/';
        $avatarPath = $uploadDir . $currentAvatar;
        
        if (file_exists($avatarPath)) {
            if (unlink($avatarPath)) {
                error_log("Avatar file deleted: $avatarPath");
            } else {
                error_log("Failed to delete avatar file: $avatarPath");
            }
        }
    }

    // Log successful removal
    error_log("Avatar removed successfully for user ID: $user_id");

    echo json_encode([
        'success' => true,
        'message' => 'Avatar removed successfully!'
    ]);

} catch (Exception $e) {
    error_log('Remove avatar error for user ' . $user_id . ': ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>