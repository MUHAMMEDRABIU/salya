<?php
session_start();
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in to clear cart']);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Clear all cart items for the user from database
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $result = $stmt->execute([$user_id]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Cart cleared successfully.',
            'cartCount' => 0,
            'subtotal' => 0,
            'delivery_fee' => 500, // Default delivery fee when cart is empty
            'total' => 500
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to clear cart'
        ]);
    }
} catch (PDOException $e) {
    error_log('Clear cart database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Throwable $e) {
    error_log('Clear cart error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
}
