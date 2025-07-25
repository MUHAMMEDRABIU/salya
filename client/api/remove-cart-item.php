<?php
session_start();
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $itemId = $data['id'];

    if (!isset($_SESSION['cart'][$itemId])) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit;
    }

    unset($_SESSION['cart'][$itemId]);

    // Recalculate cart totals
    $cart = $_SESSION['cart'];
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $delivery_fee = $subtotal >= 10000 ? 0 : 500;
    $total = $subtotal + $delivery_fee;

    echo json_encode([
        'success' => true,
        'message' => 'Item removed successfully.',
        'cartCount' => array_sum(array_column($cart, 'quantity')),
        'subtotal' => $subtotal,
        'delivery_fee' => $delivery_fee,
        'total' => $total
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    error_log('Remove cart item error: ' . $e->getMessage());
}
