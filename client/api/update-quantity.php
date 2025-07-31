<?php
session_start();
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }

    $product_id = $data['product_id'];
    $cart = $_SESSION['cart'] ?? [];

    if (!isset($cart[$product_id])) {
        echo json_encode(['success' => false, 'message' => 'Product not found in cart.']);
        exit;
    }

    $_SESSION['cart'] = $cart;

    // Totals
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $delivery_fee = $subtotal >= 10000 ? 0 : 500;
    $tax = 0;
    $total = $subtotal + $delivery_fee + $tax;

    echo json_encode([
        'success' => true,
        'message' => 'Cart updated.',
        'product_id' => $product_id,
        'quantity' => $cart[$product_id]['quantity'],
        'item_total' => $cart[$product_id]['quantity'] * $cart[$product_id]['price'],
        'subtotal' => $subtotal,
        'delivery_fee' => $delivery_fee,
        'tax' => $tax,
        'total' => $total,
        'cartCount' => array_sum(array_column($cart, 'quantity'))
    ]);
} catch (PDOException $e) {
    error_log('Update quantity error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again.'
    ]);
}
