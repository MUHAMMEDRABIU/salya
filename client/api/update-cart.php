<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

/**
 * Get cart totals for a user
 */
function getUserCartTotals($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                COALESCE(SUM(ci.quantity * p.price), 0) as subtotal,
                COUNT(ci.id) as item_count,
                COALESCE(SUM(ci.quantity), 0) as total_quantity
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $subtotal = (float)$result['subtotal'];
        $delivery_fee = $subtotal >= 10000 ? 0 : 500;
        $tax = 0; // No tax for now
        $total = $subtotal + $delivery_fee + $tax;

        return [
            'subtotal' => $subtotal,
            'delivery_fee' => $delivery_fee,
            'tax' => $tax,
            'total' => $total,
            'item_count' => (int)$result['total_quantity'],
            'unique_items' => (int)$result['item_count']
        ];
    } catch (Exception $e) {
        error_log("Error calculating cart totals: " . $e->getMessage());
        return [
            'subtotal' => 0,
            'delivery_fee' => 500,
            'tax' => 0,
            'total' => 500,
            'item_count' => 0,
            'unique_items' => 0
        ];
    }
}

/**
 * Get specific cart item details
 */
function getCartItemDetails($pdo, $user_id, $product_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT ci.quantity, p.price, p.name, p.image
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = ? AND ci.product_id = ?
        ");
        $stmt->execute([$user_id, $product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting cart item details: " . $e->getMessage());
        return null;
    }
}

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data received');
    }

    // ✅ FIXED: Get action first to determine validation requirements
    $action = isset($data['action']) ? $data['action'] : 'update';
    
    // ✅ ENHANCED: Conditional validation based on action
    if ($action === 'clear') {
        // Clear action doesn't need product_id
        $product_id = 0; // Dummy value for clear action
        $quantity = 0;   // Dummy value for clear action
        
        error_log("Clear cart action initiated for user: {$user_id}");
    } else {
        // All other actions require product_id
        if (!isset($data['product_id'])) {
            throw new Exception('Missing required parameter: product_id');
        }
        
        $product_id = (int)$data['product_id'];
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
        
        // Validate product_id for non-clear actions
        if ($product_id <= 0) {
            throw new Exception('Invalid product ID');
        }
        
        // Validate quantity for non-clear actions
        if ($quantity < 0) {
            throw new Exception('Quantity cannot be negative');
        }
        
        if ($quantity > 99) {
            throw new Exception('Quantity cannot exceed 99');
        }
    }

    // ✅ ENHANCED: Handle different actions with transaction support
    $pdo->beginTransaction();
    
    try {
        switch ($action) {
            case 'update':
            case 'increase':
            case 'decrease':
                if ($quantity === 0) {
                    // Remove item if quantity is 0
                    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
                    $result = $stmt->execute([$user_id, $product_id]);
                    $message = 'Item removed from cart';
                } else {
                    // Update or insert cart item
                    $stmt = $pdo->prepare("
                        INSERT INTO cart_items (user_id, product_id, quantity, created_at, updated_at)
                        VALUES (?, ?, ?, NOW(), NOW())
                        ON DUPLICATE KEY UPDATE 
                        quantity = VALUES(quantity),
                        updated_at = NOW()
                    ");
                    $result = $stmt->execute([$user_id, $product_id, $quantity]);
                    $message = 'Cart updated successfully';
                }
                break;

            case 'remove':
                // Explicitly remove item
                $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
                $result = $stmt->execute([$user_id, $product_id]);
                $quantity = 0;
                $message = 'Item removed from cart';
                break;

            case 'clear':
                // ✅ ENHANCED: Clear entire cart for the specific user
                error_log("Executing clear cart for user: {$user_id}");
                
                // First, get count of items to be deleted
                $countStmt = $pdo->prepare("SELECT COUNT(*) as item_count FROM cart_items WHERE user_id = ?");
                $countStmt->execute([$user_id]);
                $itemCount = $countStmt->fetch(PDO::FETCH_ASSOC)['item_count'];
                
                // Clear all cart items for this user
                $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
                $result = $stmt->execute([$user_id]);
                
                // Get number of affected rows
                $deletedRows = $stmt->rowCount();
                
                $quantity = 0;
                $product_id = 0; // Set to 0 for clear action
                
                if ($deletedRows > 0) {
                    $message = "Cart cleared successfully! Removed {$deletedRows} items.";
                } else {
                    $message = 'Cart was already empty';
                }
                
                // Always consider clear action successful
                $result = true;
                break;

            default:
                throw new Exception('Invalid action specified: ' . $action);
        }

        if (!$result) {
            throw new Exception('Failed to execute cart operation');
        }
        
        // Commit transaction
        $pdo->commit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollback();
        throw $e;
    }

    // Get updated totals after the operation
    $totals = getUserCartTotals($pdo, $user_id);

    // ✅ ENHANCED: Get item details only for non-clear actions
    $itemTotal = 0;
    $itemDetails = null;
    
    if ($action !== 'clear' && $quantity > 0 && $product_id > 0) {
        $itemDetails = getCartItemDetails($pdo, $user_id, $product_id);
        if ($itemDetails) {
            $itemTotal = $itemDetails['quantity'] * $itemDetails['price'];
        }
    }

    // ✅ ENHANCED: Success response with detailed logging
    $response = [
        'success' => true,
        'message' => $message,
        'action' => $action,
        'product_id' => $product_id,
        'quantity' => $action === 'clear' ? 0 : ($quantity > 0 ? ($itemDetails['quantity'] ?? 0) : 0),
        'item_total' => $itemTotal,
        'subtotal' => $totals['subtotal'],
        'delivery_fee' => $totals['delivery_fee'],
        'tax' => $totals['tax'],
        'total' => $totals['total'],
        'cart_count' => $totals['item_count'],
        'unique_items' => $totals['unique_items']
    ];
    
    error_log("Cart operation success: " . json_encode([
        'user_id' => $user_id,
        'action' => $action,
        'product_id' => $product_id,
        'new_cart_count' => $totals['item_count']
    ]));
    
    echo json_encode($response);

} catch (PDOException $e) {
    // Rollback if transaction is still active
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    error_log('Cart operation database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again.',
        'error_type' => 'database_error'
    ]);
} catch (Exception $e) {
    // Rollback if transaction is still active
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    error_log('Cart operation error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => 'validation_error'
    ]);
}
?>