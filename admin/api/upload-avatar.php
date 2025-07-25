<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate admin ID
$adminId = isset($_POST['admin_id']) ? (int)$_POST['admin_id'] : 0;
if (!$adminId) {
    echo json_encode(['success' => false, 'message' => 'Missing admin ID']);
    exit;
}

// Validate file upload
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['avatar'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$maxSize = 2 * 1024 * 1024; // 2MB

// Check file type
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid image type']);
    exit;
}

// Check file size
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'Image size exceeds 2MB']);
    exit;
}

// Generate unique filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$avatarName = 'admin_' . $adminId . '_' . time() . '.' . strtolower($ext);
$uploadDir = realpath(__DIR__ . '/../../assets/uploads');
if (!$uploadDir) {
    echo json_encode(['success' => false, 'message' => 'Upload directory not found']);
    exit;
}
$targetPath = $uploadDir . DIRECTORY_SEPARATOR . $avatarName;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
    exit;
}

// Optionally: Remove old avatar file (if not default)
$stmt = $pdo->prepare("SELECT avatar FROM admins WHERE id = ?");
$stmt->execute([$adminId]);
$oldAvatar = $stmt->fetchColumn();
if ($oldAvatar && $oldAvatar !== 'avatar.jpg' && file_exists($uploadDir . DIRECTORY_SEPARATOR . $oldAvatar)) {
    @unlink($uploadDir . DIRECTORY_SEPARATOR . $oldAvatar);
}

// Update DB
$stmt = $pdo->prepare("UPDATE admins SET avatar = ? WHERE id = ?");
$stmt->execute([$avatarName, $adminId]);

echo json_encode(['success' => true, 'avatar' => $avatarName]);