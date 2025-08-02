<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

/**
 * Get cart totals for user
 */
function getUserCartTotals($pdo, $user_id) {
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

/**
 * Get specific cart item details
 */
function getCartItemDetails($pdo, $user_id, $product_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                ci.quantity,
                p.price
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = ? AND ci.product_id = ?
        ");
        $stmt->execute([$user_id, $product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting cart item details: " . $e->getMessage());
        return null;
    }
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in to update cart']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['product_id']) || !isset($data['quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }

    $product_id = (int)$data['product_id'];
    $quantity = (int)$data['quantity'];
    $user_id = $_SESSION['user_id'];

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        exit;
    }

    // Check if item exists in user's cart
    $stmt = $pdo->prepare("SELECT id FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cartItem) {
        echo json_encode(['success' => false, 'message' => 'Product not found in cart.']);
        exit;
    }

    if ($quantity <= 0) {
        // Remove item if quantity is 0 or less
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        $result = $stmt->execute([$user_id, $product_id]);
        
        if (!$result) {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart.']);
            exit;
        }
    } else {
        // Update quantity
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?");
        $result = $stmt->execute([$quantity, $user_id, $product_id]);
        
        if (!$result) {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart.']);
            exit;
        }
    }

    // Get updated cart totals
    $totals = getUserCartTotals($pdo, $user_id);
    
    // Get updated item details (if item still exists)
    $itemDetails = null;
    $itemTotal = 0;
    
    if ($quantity > 0) {
        $itemDetails = getCartItemDetails($pdo, $user_id, $product_id);
        if ($itemDetails) {
            $itemTotal = $itemDetails['quantity'] * $itemDetails['price'];
        }
    }

    echo json_encode([
        'success' => true,
        'message' => $quantity > 0 ? 'Cart updated.' : 'Item removed from cart.',
        'product_id' => $product_id,
        'quantity' => $quantity > 0 ? ($itemDetails['quantity'] ?? 0) : 0,
        'item_total' => $itemTotal,
        'subtotal' => $totals['subtotal'],
        'delivery_fee' => $totals['delivery_fee'],
        'tax' => $totals['tax'],
        'total' => $totals['total'],
        'cartCount' => $totals['item_count']
    ]);

} catch (PDOException $e) {
    error_log('Update quantity database error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again.'
    ]);
} catch (Throwable $e) {
    error_log('Update quantity error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again.'
    ]);
}
?>