<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $verification_id = $data['verification_id'] ?? '';

    if (empty($verification_id)) {
        throw new Exception('Verification ID is required');
    }

    // Check if order exists
    $stmt = $pdo->prepare("
        SELECT id, order_number, payment_status, status, total_amount 
        FROM orders 
        WHERE verification_id = ? AND user_id = ?
    ");
    $stmt->execute([$verification_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Order not found');
    }

    // Simulate payment verification (in production, this would be admin-verified)
    $payment_verified = true;

    if ($payment_verified) {
        // Update order status
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET payment_status = 'verified', status = 'confirmed', updated_at = NOW() 
            WHERE verification_id = ? AND user_id = ?
        ");
        $result = $stmt->execute([$verification_id, $user_id]);

        if (!$result) {
            throw new Exception('Failed to update payment status');
        }

        // Clear user's cart
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$user_id]);

        error_log("Payment verified for order: {$order['order_number']} - User: {$user_id}");

        echo json_encode([
            'success' => true,
            'message' => 'Payment verified successfully',
            'order_id' => $order['id'],
            'order_number' => $order['order_number'],
            'status' => 'verified'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Payment verification failed',
            'status' => 'pending'
        ]);
    }
} catch (Exception $e) {
    error_log('Payment verification error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
