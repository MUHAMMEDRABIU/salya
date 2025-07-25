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
    $required = ['firstName', 'lastName', 'phone', 'email', 'city', 'address', 'postalCode'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            exit;
        }
    }

    // Check if a checkout already exists for this user
    $stmt = $pdo->prepare("SELECT id FROM checkouts WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update
        $stmt = $pdo->prepare("UPDATE checkouts SET first_name=?, last_name=?, phone=?, email=?, city=?, address=?, postal_code=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([
            $data['firstName'],
            $data['lastName'],
            $data['phone'],
            $data['email'],
            $data['city'],
            $data['address'],
            $data['postalCode'],
            $user_id
        ]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO checkouts (id, first_name, last_name, phone, email, city, address, postal_code, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $user_id,
            $data['firstName'],
            $data['lastName'],
            $data['phone'],
            $data['email'],
            $data['city'],
            $data['address'],
            $data['postalCode']
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Checkout data saved.']);
} catch (Throwable $e) {
    error_log('Checkout save error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}