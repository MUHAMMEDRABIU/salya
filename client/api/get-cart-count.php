<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');
function getUserCartCount($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items FROM cart_items WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total_items'] ?? 0);
    } catch (PDOException $e) {
        error_log("Error getting cart count: " . $e->getMessage());
        return 0;
    }
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => true, 'cart_count' => 0]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $cartCount = getUserCartCount($pdo, $user_id);

    echo json_encode([
        'success' => true,
        'cart_count' => $cartCount
    ]);
} catch (PDOException $e) {
    error_log('Get cart count database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'cart_count' => 0, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Get cart count error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'cart_count' => 0, 'message' => 'An unexpected error occurred']);
}
