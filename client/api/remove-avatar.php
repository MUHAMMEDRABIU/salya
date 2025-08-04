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
    // Get current avatar
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $currentAvatar = $stmt->fetchColumn();

    // Update database to remove avatar
    $stmt = $pdo->prepare("UPDATE users SET avatar = NULL, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$user_id]);

    if (!$result) {
        throw new Exception('Failed to remove avatar from database');
    }

    // Delete avatar files
    if ($currentAvatar) {
        $uploadDir = __DIR__ . '/../../assets/img/avatars/';
        $avatarPath = $uploadDir . $currentAvatar;
        $thumbPath = $uploadDir . 'thumb_' . $currentAvatar;
        
        if (file_exists($avatarPath)) {
            unlink($avatarPath);
        }
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Avatar removed successfully'
    ]);

} catch (Exception $e) {
    error_log('Remove avatar error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>