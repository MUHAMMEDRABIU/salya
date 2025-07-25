<?php
header('Content-Type: application/json');

require __DIR__ . '/initialize.php';
require __DIR__ . '/../util/utilities.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method');
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = (int) ($input['product_id'] ?? 0);

    if (!$product_id) {
        error_log("[DELETE PRODUCT ERROR] Product ID is required");
        echo json_encode([
            'success' => false,
            'message' => 'Product ID is required'
        ]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Product deleted successfully'
    ]);
} catch (PDOException $e) {
    error_log("[DELETE PRODUCT ERROR] " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
