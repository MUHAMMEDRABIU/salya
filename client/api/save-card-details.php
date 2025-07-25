<?php
require_once '../initialize.php';
header('Content-Type: application/json');

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $user['id'];

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $required = [
        'cardName', 'cardNumber', 'cardExpiry','billingAddressSame', 'paymentMethod', 'amount', 'currency'
    ];
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            exit;
        }
    }

    // Insert card details
    $stmt = $pdo->prepare(
        "INSERT INTO card_details 
        (user_id, card_name, card_number, card_expiry, card_cvc, billing_address_same, payment_method, amount, currency, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmt->execute([
        $user_id,
        $data['cardName'],
        $data['cardNumber'],
        $data['cardExpiry'],
        $data['cardCVC'] ?? null,
        $data['billingAddressSame'] ? 1 : 0,
        $data['paymentMethod'],
        $data['amount'],
        $data['currency']
    ]);

    echo json_encode(['success' => true, 'message' => 'Card details saved.']);
} catch (Throwable $e) {
    error_log('Save card details error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}