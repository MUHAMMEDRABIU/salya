<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../initialize.php';
require __DIR__ . '/../../config/constants.php';

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

    // Fetch existing product (for image cleanup)
    $stmt = $pdo->prepare('SELECT image FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        jsonResponse(404, ['success' => false, 'message' => 'Product not found']);
    }

    $imageName = $product['image'] ?? null;

    $pdo->beginTransaction();

    $del = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $ok = $del->execute([$productId]);

    if (!$ok) {
        $pdo->rollBack();
        jsonResponse(500, ['success' => false, 'message' => 'Failed to delete product']);
    }

    $pdo->commit();

    // Delete image file after successful delete (if not default)
    if ($imageName && $imageName !== DEFAULT_PRODUCT_IMAGE) {
        $path = PRODUCT_IMAGE_DIR . $imageName;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    jsonResponse(200, ['success' => true, 'message' => 'Product deleted successfully']);
} catch (PDOException $e) {
    // Handle FK constraint violations gracefully
    if ((int)$e->getCode() === 23000) {
        jsonResponse(409, [
            'success' => false,
            'message' => 'Cannot delete product because it is referenced by other records.'
        ]);
    }
    error_log('[delete-product.php] ' . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'An unexpected error occurred']);
} catch (Throwable $e) {
    error_log('[delete-product.php] ' . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'An unexpected error occurred']);
}
