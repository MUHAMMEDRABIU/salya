<?php
require_once '../../config/database.php';
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

    if (empty($categoryId)) {
        throw new Exception('Category ID is required');
    }

    // Check if category exists
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch();

    if (!$category) {
        throw new Exception('Category not found');
    }

    // Check if category has products
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$categoryId]);
    $productCount = $stmt->fetchColumn();

    if ($productCount > 0) {
        throw new Exception("Cannot delete category. It contains {$productCount} product(s). Please move or delete the products first.");
    }

    // Delete category image if it exists
    $uploadDir = __DIR__ . '/../../assets/uploads/categories/';
    if ($category['image_url'] && file_exists($uploadDir . $category['image_url'])) {
        unlink($uploadDir . $category['image_url']);
    }

    // Delete category
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $result = $stmt->execute([$categoryId]);

    if (!$result) {
        throw new Exception('Failed to delete category');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Category deleted successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
