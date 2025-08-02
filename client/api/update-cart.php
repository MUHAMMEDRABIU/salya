<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

/**
 * Get cart totals for user
 */
function getUserCartTotals($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                SUM(ci.quantity * p.price) as subtotal,
                SUM(ci.quantity) as total_items
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $subtotal = $result['subtotal'] ?? 0;
        $delivery_fee = $subtotal >= 10000 ? 0 : 500;
        $tax = 0;
        $total = $subtotal + $delivery_fee + $tax;

        return [
            'subtotal' => $subtotal,
            'delivery_fee' => $delivery_fee,
            'tax' => $tax,
            'total' => $total,
            'item_count' => $result['total_items'] ?? 0
        ];
    } catch (PDOException $e) {
        error_log("Error calculating cart totals: " . $e->getMessage());
        return [
            'subtotal' => 0,
            'delivery_fee' => 500,
            'tax' => 0,
            'total' => 500,
            'item_count' => 0
        ];
    }
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in to update cart']);
        exit;
    }

    // Only allow POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = isset($input['id']) ? (int)$input['id'] : 0;
    $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
    $user_id = $_SESSION['user_id'];

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit;
    }

    if ($quantity <= 0) {
        // If quantity is 0 or less, remove the item
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        $result = $stmt->execute([$user_id, $product_id]);
    } else {
        // Update quantity
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?");
        $result = $stmt->execute([$quantity, $user_id, $product_id]);
    }

    if ($result) {
        $totals = getUserCartTotals($pdo, $user_id);

        echo json_encode([
            'success' => true,
            'message' => 'Cart updated successfully',
            'quantity' => $quantity,
            'subtotal' => $totals['subtotal'],
            'delivery_fee' => $totals['delivery_fee'],
            'tax' => $totals['tax'],
            'total' => $totals['total'],
            'cartCount' => $totals['item_count']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
    }
} catch (PDOException $e) {
    error_log('Update cart database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Throwable $e) {
    error_log('Update cart error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
}
