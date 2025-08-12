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

    $categoryId = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($categoryId) || empty($name)) {
        throw new Exception('Category ID and name are required');
    }

    // Check if category exists
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch();

    if (!$category) {
        throw new Exception('Category not found');
    }

    // Check if name is taken by another category
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
    $stmt->execute([$name, $categoryId]);
    if ($stmt->fetch()) {
        throw new Exception('Category with this name already exists');
    }

    // Generate slug
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));

    // Handle image upload
    $imageName = $category['image_url']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = CATEGORY_IMAGE_DIR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileInfo = pathinfo($_FILES['image']['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            throw new Exception('Invalid image format. Only JPG, PNG, GIF, and WebP are allowed.');
        }

        if ($_FILES['image']['size'] > MAX_CATEGORY_IMAGE_SIZE) {
            throw new Exception('Image size too large. Maximum size is 5MB.');
        }

        // Delete old image if it exists
        if ($category['image_url'] && file_exists($uploadDir . $category['image_url'])) {
            unlink($uploadDir . $category['image_url']);
        }

        $imageName = 'category_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload image');
        }
    }

    // Update category
    $stmt = $pdo->prepare("
        UPDATE categories 
        SET name = ?, slug = ?, description = ?, image_url = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $result = $stmt->execute([$name, $slug, $description, $imageName, $categoryId]);

    if (!$result) {
        throw new Exception('Failed to update category');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Category updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
