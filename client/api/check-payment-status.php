<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$verification_id = $_GET['verification_id'] ?? '';

if (empty($verification_id)) {
    echo json_encode(['success' => false, 'message' => 'Verification ID is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT order_number, payment_status, status 
        FROM orders 
        WHERE verification_id = ? AND user_id = ?
    ");
    $stmt->execute([$verification_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit();
    }

    $status = 'pending';
    if ($order['payment_status'] === 'verified') {
        $status = 'verified';
    } elseif ($order['payment_status'] === 'failed') {
        $status = 'rejected';
    }

    echo json_encode([
        'success' => true,
        'status' => $status,
        'order_number' => $order['order_number'],
        'payment_status' => $order['payment_status']
    ]);

} catch (Exception $e) {
    error_log('Check payment status error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error checking payment status']);
}
?>