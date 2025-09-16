<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../util/util.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $addressName = trim($data['address_name'] ?? '');
    $fullAddress = trim($data['full_address'] ?? '');
    $city = trim($data['city'] ?? '');
    $state = trim($data['state'] ?? '');
    $postalCode = trim($data['postal_code'] ?? '');
    $isDefault = (bool)($data['is_default'] ?? false);

    // Validation
    if (empty($addressName) || empty($fullAddress) || empty($city) || empty($state)) {
        throw new Exception('Address name, full address, city, and state are required');
    }

    // Start transaction
    $pdo->beginTransaction();

    // If this address is set as default, remove default from other addresses
    if ($isDefault) {
        $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    // Check if this is the first address (make it default automatically)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_addresses WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $addressCount = $stmt->fetchColumn();

    if ($addressCount == 0) {
        $isDefault = true;
    }

    // Insert new address
    $stmt = $pdo->prepare("
        INSERT INTO user_addresses (user_id, address_name, full_address, city, state, postal_code, is_default, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");

    $result = $stmt->execute([$user_id, $addressName, $fullAddress, $city, $state, $postalCode, $isDefault ? 1 : 0]);

    if (!$result) {
        throw new Exception('Failed to save address');
    }

    $addressId = $pdo->lastInsertId();

    // Commit transaction
    $pdo->commit();

    $notifTitle = 'Address Added';
    $notifMessage = 'Your address "' . htmlspecialchars($addressName) . '" was added successfully.';
    $notifType = 'updates';
    $notifIcon = 'fa-solid fa-map-location-dot';
    $notifColor = '#a7ff55ff';
    $notifAction = 'View';
    pushNotification($pdo, $user_id, $notifTitle, $notifMessage, $notifType, $notifIcon, $notifColor, $notifAction);

    echo json_encode([
        'success' => true,
        'message' => 'Address saved successfully',
        'address_id' => $addressId
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    // Push notification for address save
    error_log('Save address error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
