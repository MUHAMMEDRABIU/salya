<?php
require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../initialize.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    if (!isset($_SESSION['admin_id'])) {
        throw new Exception('Unauthorized access');
    }

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if (empty($name)) {
        throw new Exception('Category name is required');
    }

    // Generate slug if not provided
    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    }

    // Check if category already exists
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ? OR slug = ?");
    $stmt->execute([$name, $slug]);
    if ($stmt->fetch()) {
        throw new Exception('Category with this name or slug already exists');
    }

    // Handle image upload
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../assets/uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileInfo = pathinfo($_FILES['image']['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            throw new Exception('Invalid image format. Only JPG, PNG, GIF, and WebP are allowed.');
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) { // 5MB
            throw new Exception('Image size too large. Maximum size is 5MB.');
        }

        $imageName = 'category_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload image');
        }
    }

    $isActive = 1;
    // Insert category
    $stmt = $pdo->prepare("
        INSERT INTO categories (name, slug, description, image_url, is_active, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $result = $stmt->execute([$name, $slug, $description, $imageName, $isActive]);

    if (!$result) {
        throw new Exception('Failed to create category');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Category created successfully',
        'category_id' => $pdo->lastInsertId()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
