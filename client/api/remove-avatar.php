<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../../config/constants.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Check authentication
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

    if (!$currentAvatar) {
        echo json_encode(['success' => false, 'message' => 'No avatar to remove']);
        exit();
    }

    // Prevent removal of default avatar
    if ($currentAvatar === DEFAULT_USER_AVATAR) {
        echo json_encode(['success' => false, 'message' => 'Cannot remove default avatar']);
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Update database to set avatar to default
        $stmt = $pdo->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
        $updateSuccess = $stmt->execute([DEFAULT_USER_AVATAR, $user_id]);

        if (!$updateSuccess) {
            throw new Exception('Failed to update database');
        }

        // Commit transaction
        $pdo->commit();

        // Delete physical file (only if not default avatar)
        if ($currentAvatar && $currentAvatar !== DEFAULT_USER_AVATAR) {
            $filePath = USER_AVATAR_DIR . $currentAvatar;
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    error_log("Failed to delete avatar file: {$filePath}");
                    // Don't fail the operation if file deletion fails
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Avatar removed successfully',
            'avatar' => DEFAULT_USER_AVATAR,
            'avatar_url' => USER_AVATAR_URL . DEFAULT_USER_AVATAR
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $pdo->rollback();
        throw $e;
    }
} catch (PDOException $e) {
    error_log("Database error in remove-avatar.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error in remove-avatar.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
}
