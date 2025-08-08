<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../../config/constants.php';

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if admin is authenticated
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Admin not authenticated']);
    exit;
}

$adminId = $_SESSION['admin_id'];

// Validate file upload
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

try {
    $file = $_FILES['avatar'];

    // Check file type using constants
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
        echo json_encode(['success' => false, 'message' => 'Invalid image type. Only JPG, PNG, GIF, and WebP are allowed']);
        exit;
    }

    // Check file size using constant
    if ($file['size'] > MAX_AVATAR_SIZE) {
        $maxSizeMB = number_format(MAX_AVATAR_SIZE / (1024 * 1024), 0);
        echo json_encode(['success' => false, 'message' => "Image size exceeds {$maxSizeMB}MB"]);
        exit;
    }

    // Create upload directory using constant
    $uploadDir = ADMIN_AVATAR_DIR;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $avatarName = 'admin_' . $adminId . '_' . uniqid('', true) . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . $avatarName;

    // Get current avatar to delete (but protect default)
    $stmt = $pdo->prepare("SELECT avatar FROM admins WHERE id = ?");
    $stmt->execute([$adminId]);
    $oldAvatar = $stmt->fetchColumn();

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
        exit;
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE admins SET avatar = ?, updated_at = NOW() WHERE id = ?");
    if (!$stmt->execute([$avatarName, $adminId])) {
        // Clean up uploaded file if database update fails
        unlink($targetPath);
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
        exit;
    }

    // Remove old avatar file (if not default)
    if ($oldAvatar && 
        $oldAvatar !== DEFAULT_ADMIN_AVATAR && 
        file_exists(ADMIN_AVATAR_DIR . $oldAvatar)) {
        @unlink(ADMIN_AVATAR_DIR . $oldAvatar);
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Avatar updated successfully',
        'avatar' => $avatarName,
        'avatar_url' => ADMIN_AVATAR_URL . $avatarName
    ]);

} catch (Exception $e) {
    error_log("Admin avatar upload error for admin {$adminId}: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while uploading avatar'
    ]);
}
?>