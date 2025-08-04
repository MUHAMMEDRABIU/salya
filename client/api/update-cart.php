<?php
// filepath: c:\xampp\htdocs\salya\client\api\update-cart.php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

    // Validate required parameters
    if (!isset($data['product_id'])) {
        throw new Exception('Missing required parameter: product_id');
    }

    $product_id = (int)$data['product_id'];
    $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
    $action = isset($data['action']) ? $data['action'] : 'update';

    // Validate product_id
    if ($product_id <= 0) {
        throw new Exception('Invalid product ID');
    }

    // Validate quantity
    if ($quantity < 0) {
        throw new Exception('Quantity cannot be negative');
    }

    if ($quantity > 99) {
        throw new Exception('Quantity cannot exceed 99');
    }

    // Handle different actions
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
            // Clear entire cart
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $result = $stmt->execute([$user_id]);
            $quantity = 0;
            $message = 'Cart cleared successfully';
            break;

        default:
            throw new Exception('Invalid action specified');
    }

    if (!$result) {
        throw new Exception('Failed to update cart');
    }

    // Get updated totals
    $totals = getUserCartTotals($pdo, $user_id);

    // Get item details if quantity > 0
    $itemTotal = 0;
    $itemDetails = null;
    if ($quantity > 0) {
        $itemDetails = getCartItemDetails($pdo, $user_id, $product_id);
        if ($itemDetails) {
            $itemTotal = $itemDetails['quantity'] * $itemDetails['price'];
        }
    }

    // Success response
    echo json_encode([
        'success' => true,
        'message' => $message,
        'product_id' => $product_id,
        'quantity' => $quantity > 0 ? ($itemDetails['quantity'] ?? 0) : 0,
        'item_total' => $itemTotal,
        'subtotal' => $totals['subtotal'],
        'delivery_fee' => $totals['delivery_fee'],
        'tax' => $totals['tax'],
        'total' => $totals['total'],
        'cart_count' => $totals['item_count'],
        'unique_items' => $totals['unique_items']
    ]);
} catch (PDOException $e) {
    error_log('Cart operation database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again.'
    ]);
} catch (Exception $e) {
    error_log('Cart operation error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
