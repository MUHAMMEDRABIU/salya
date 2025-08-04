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
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['avatar'];
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    // Basic validation
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedTypes)) {
        throw new Exception('Only JPG, PNG and GIF files allowed');
    }

    if ($file['size'] > $maxSize) {
        throw new Exception('File too large. Max 2MB');
    }

    // Create directory
    $uploadDir = __DIR__ . '/../../assets/uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate filename
    $filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    // Get current avatar to delete
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $currentAvatar = $stmt->fetchColumn();

    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Upload failed');
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    if (!$stmt->execute([$filename, $user_id])) {
        unlink($targetPath);
        throw new Exception('Database update failed');
    }

    // Delete old avatar
    if ($currentAvatar && file_exists($uploadDir . $currentAvatar)) {
        unlink($uploadDir . $currentAvatar);
    }

    echo json_encode([
        'success' => true,
        'avatar_url' => '../assets/uploads/avatars/' . $filename
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
