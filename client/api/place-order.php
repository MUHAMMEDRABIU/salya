<?php
require_once '../initialize.php';
header('Content-Type: application/json');

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $user['id'];

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $required = ['product_id', 'items', 'delivery_fee', 'total_amount'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            exit;
        }
    }

    // Generate order number
    $order_number = 'ORD-' . time() . '-' . $user_id;
    $status = 'pending';

    // Insert order (without items column)
    $stmt = $pdo->prepare(
        "INSERT INTO orders 
        (product_id, order_number, user_id, delivery_fee, total_amount, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
    );
    $stmt->execute([
        $data['product_id'],
        $order_number,
        $user_id,
        $data['delivery_fee'],
        $data['total_amount'],
        $status
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert each item into order_items table
    foreach ($data['items'] as $item) {
        $stmtItem = $pdo->prepare(
            "INSERT INTO order_items 
            (order_id, product_id, quantity, unit_price, total_price, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmtItem->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['unit_price'],
            $item['total_price']
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Order placed successfully.', 'order_number' => $order_number]);
} catch (Throwable $e) {
    error_log('Place order error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while placing the order.']);
}