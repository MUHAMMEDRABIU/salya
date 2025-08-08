<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../../config/constants.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $file = $_FILES['avatar'];

    // Validate file type using constants
    $allowedTypes = [
        ALLOWED_IMAGE_JPEG,
        ALLOWED_IMAGE_PNG,
        ALLOWED_IMAGE_GIF,
        ALLOWED_IMAGE_WEBP
    ];

    // Get file MIME type for validation
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Only JPG, PNG, GIF, and WebP files are allowed');
    }

    // Validate file size using constant
    if ($file['size'] > MAX_AVATAR_SIZE) {
        $maxSizeMB = number_format(MAX_AVATAR_SIZE / (1024 * 1024), 0);
        throw new Exception("File too large. Maximum size is {$maxSizeMB}MB");
    }

    // Create directory using constant
    $uploadDir = USER_AVATAR_DIR;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'user_' . $user_id . '_' . uniqid('', true) . '_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    // Get current avatar to delete (but protect default)
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $currentAvatar = $stmt->fetchColumn();

    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to upload file');
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
    if (!$stmt->execute([$filename, $user_id])) {
        // Clean up uploaded file if database update fails
        unlink($targetPath);
        throw new Exception('Database update failed');
    }

    // Delete old avatar if it exists and is not the default
    if ($currentAvatar && 
        $currentAvatar !== DEFAULT_USER_AVATAR && 
        file_exists(USER_AVATAR_DIR . $currentAvatar)) {
        unlink(USER_AVATAR_DIR . $currentAvatar);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Avatar updated successfully',
        'avatar' => $filename,
        'avatar_url' => USER_AVATAR_URL . $filename
    ]);

} catch (Exception $e) {
    error_log("Avatar upload error for user {$user_id}: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>