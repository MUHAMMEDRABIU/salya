<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../initialize.php';

function jsonResponse(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

try {
    $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$productId) {
        jsonResponse(400, ['success' => false, 'message' => 'Product ID is required']);
    }

    // Collect updatable fields (either/both may be present)
    $fields = [];
    $params = [];

    if (isset($_POST['is_active'])) {
        $isActive = (int)($_POST['is_active'] == 1);
        $fields[] = 'is_active = ?';
        $params[] = $isActive;
    }

    if (isset($_POST['is_featured'])) {
        $isFeatured = (int)($_POST['is_featured'] == 1);
        $fields[] = 'is_featured = ?';
        $params[] = $isFeatured;
    }

    if (!$fields) {
        jsonResponse(400, ['success' => false, 'message' => 'No fields to update']);
    }

    // Ensure product exists
    $chk = $pdo->prepare('SELECT id FROM products WHERE id = ?');
    $chk->execute([$productId]);
    if (!$chk->fetchColumn()) {
        jsonResponse(404, ['success' => false, 'message' => 'Product not found']);
    }

    $sql = 'UPDATE products SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = ?';
    $params[] = $productId;

    $stmt = $pdo->prepare($sql);
    if (!$stmt->execute($params)) {
        jsonResponse(500, ['success' => false, 'message' => 'Failed to update product']);
    }

    jsonResponse(200, ['success' => true, 'message' => 'Updated successfully']);
} catch (Throwable $e) {
    error_log('[update-product-status.php] ' . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'An unexpected error occurred']);
}
