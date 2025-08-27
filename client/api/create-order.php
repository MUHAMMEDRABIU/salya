<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../util/util.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data received');
    }

    // Validate required fields
    $required_fields = ['payment_method', 'amount', 'virtual_account', 'cart_items', 'shipping_address'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }

    $payment_method = $data['payment_method'];
    $amount = (float)$data['amount'];
    $virtual_account = $data['virtual_account'];
    $cart_items = $data['cart_items'];
    $shipping = $data['shipping_address'];

    // Begin transaction
    $pdo->beginTransaction();

    // Generate unique order number and verification ID
    $order_number = 'ORD-' . date('Y') . '-' . strtoupper(uniqid());
    $verification_id = 'VER-' . strtoupper(uniqid());

    // Calculate totals from cart items
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    $delivery_fee = $subtotal >= 10000 ? 0 : 500; // Free delivery for orders above ₦10,000
    $total_amount = $subtotal + $delivery_fee;

    // Verify amount matches
    if (abs($total_amount - $amount) > 0.01) {
        throw new Exception('Amount mismatch detected');
    }

    // Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id, order_number, verification_id, payment_method, 
            subtotal, delivery_fee, total_amount, status, payment_status,
            shipping_name, shipping_phone, shipping_address, shipping_city, 
            shipping_state, shipping_postal_code, shipping_landmark, 
            shipping_area, delivery_option, special_instructions,
            created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending_payment', 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");

    $result = $stmt->execute([
        $user_id,
        $order_number,
        $verification_id,
        $payment_method,
        $subtotal,
        $delivery_fee,
        $total_amount,
        $shipping['name'],
        $shipping['phone'],
        $shipping['address'],
        $shipping['city'],
        $shipping['state'],
        $shipping['postal_code'],
        $shipping['landmark'],
        $shipping['area'],
        $shipping['delivery_option'],
        $shipping['special_instructions']
    ]);

    if (!$result) {
        throw new Exception('Failed to create order');
    }

    $order_id = $pdo->lastInsertId();

    // Insert order items
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, product_image, quantity, unit_price, total_price)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($cart_items as $item) {
        $item_subtotal = $item['price'] * $item['quantity'];
        $stmt->execute([
            $order_id,
            $item['product_id'],
            $item['name'],
            $item['image'],
            $item['quantity'],
            $item['price'],
            $item_subtotal
        ]);
    }

    // Commit transaction
    $pdo->commit();

    // Push notification for new order
    $notifTitle = 'Order Placed';
    $notifMessage = 'Your order #' . htmlspecialchars($order_number) . ' has been placed successfully.';
    $notifType = 'orders';
    // Custom color/icon logic for 'orders' type
    $notifIcon = 'fa-solid fa-bag-shopping';
    $notifColor = '#ea580c'; // Tailwind orange-600
    $notifAction = 'View';
    pushNotification($pdo, $user_id, $notifTitle, $notifMessage, $notifType, $notifIcon, $notifColor, $notifAction);
    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'order_id' => $order_id,
        'order_number' => $order_number,
        'verification_id' => $verification_id,
        'total_amount' => $total_amount
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }

    error_log('Order creation database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again.',
        'error_type' => 'database_error'
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }

    error_log('Order creation error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => 'validation_error'
    ]);
}
