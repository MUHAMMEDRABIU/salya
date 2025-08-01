<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

/**
 * Add item to user's cart in database
 */
function addToUserCart($pdo, $user_id, $product_id, $quantity = 1)
{
    try {
        // Check if item already exists in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update quantity
            $new_quantity = $existing['quantity'] + $quantity;
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$new_quantity, $existing['id']]);
        } else {
            // Insert new item
            $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            return $stmt->execute([$user_id, $product_id, $quantity]);
        }
    } catch (PDOException $e) {
        error_log("Error adding to cart: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user's total cart count
 */
function getUserCartCount($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items FROM cart_items WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_items'] ?? 0;
    } catch (PDOException $e) {
        error_log("Error getting cart count: " . $e->getMessage());
        return 0;
    }
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in to add items to cart']);
        exit;
    }

    // Only allow POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;
    $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
    $user_id = $_SESSION['user_id'];

    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        exit;
    }

    // Verify product exists and is in stock
    $stmt = $pdo->prepare("SELECT id, name, price, image, stock_quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    if (isset($product['stock_quantity']) && !$product['stock_quantity']) {
        echo json_encode(['success' => false, 'message' => 'Product is out of stock']);
        exit;
    }

    // Add to cart in database
    if (addToUserCart($pdo, $user_id, $product_id, $quantity)) {
        $totalCount = getUserCartCount($pdo, $user_id);

        echo json_encode([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => (int)$totalCount
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add item to cart']);
    }
} catch (Throwable $e) {
    error_log('Add to cart error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again later.']);
}
