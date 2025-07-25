<?php
session_start();
header('Content-Type: application/json');

try {
    $_SESSION['cart'] = [];

    echo json_encode([
        'success' => true,
        'message' => 'Cart cleared successfully.',
        'cartCount' => 0,
        'subtotal' => 0,
        'delivery_fee' => 0,
        'total' => 0
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    error_log('Clear cart error: ' . $e->getMessage());
}
