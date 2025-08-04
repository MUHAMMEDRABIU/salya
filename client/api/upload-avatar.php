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
    // Check if file was uploaded
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $file = $_FILES['avatar'];

    // Configuration
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB (reduced since no compression)

    // Get file info
    $originalName = $file['name'];
    $fileSize = $file['size'];
    $fileTmpPath = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Validate file extension
    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.');
    }

    // Validate file size
    if ($fileSize > $maxSize) {
        throw new Exception('File size too large. Maximum size is 2MB.');
    }

    // Additional security: Check actual file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $actualMimeType = finfo_file($finfo, $fileTmpPath);
    finfo_close($finfo);

    if (!in_array($actualMimeType, $allowedMimeTypes)) {
        throw new Exception('Invalid file type detected. File content does not match extension.');
    }

    // Create upload directory if it doesn't exist
    $uploadDir = __DIR__ . '/../../assets/uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename with timestamp and user ID
    $filename = 'avatar_' . $user_id . '_' . time() . '.' . $fileExtension;
    $targetPath = $uploadDir . $filename;

    // Get current avatar to delete later
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $currentAvatar = $stmt->fetchColumn();

    // Move uploaded file to destination
    if (!move_uploaded_file($fileTmpPath, $targetPath)) {
        throw new Exception('Failed to save uploaded file. Check directory permissions.');
    }

    // Update database with new avatar filename
    $stmt = $pdo->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$filename, $user_id]);

    if (!$result) {
        // If database update fails, delete the uploaded file
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        throw new Exception('Failed to update user avatar in database');
    }

    // Delete old avatar file if it exists and is different
    if ($currentAvatar && $currentAvatar !== $filename) {
        $oldAvatarPath = $uploadDir . $currentAvatar;
        if (file_exists($oldAvatarPath)) {
            unlink($oldAvatarPath);
        }
    }

    // Log successful upload
    error_log("Avatar uploaded successfully for user ID: $user_id, filename: $filename");

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Avatar uploaded successfully!',
        'avatar_url' => '../assets/uploads/avatars/' . $filename,
        'file_size' => formatBytes($fileSize),
        'file_type' => $actualMimeType
    ]);
} catch (Exception $e) {
    error_log('Avatar upload error for user ' . $user_id . ': ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Format bytes to human readable format
 */
function formatBytes($size, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $unitIndex = 0;

    while ($size >= 1024 && $unitIndex < count($units) - 1) {
        $size /= 1024;
        $unitIndex++;
    }

    return round($size, $precision) . ' ' . $units[$unitIndex];
}
