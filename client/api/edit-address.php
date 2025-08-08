<?php
require_once __DIR__ . '/../initialize.php';
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

    $addressId = (int)($data['address_id'] ?? 0);
    $addressName = trim($data['address_name'] ?? '');
    $fullAddress = trim($data['full_address'] ?? '');
    $city = trim($data['city'] ?? '');
    $state = trim($data['state'] ?? '');
    $postalCode = trim($data['postal_code'] ?? '');
    $isDefault = (bool)($data['is_default'] ?? false);

    // Validation
    if (!$addressId) {
        throw new Exception('Address ID is required');
    }

    if (empty($addressName) || empty($fullAddress) || empty($city) || empty($state)) {
        throw new Exception('Address name, full address, city, and state are required');
    }

    // Check if address belongs to user
    $stmt = $pdo->prepare("SELECT is_default FROM user_addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$addressId, $user_id]);
    $currentAddress = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentAddress) {
        throw new Exception('Address not found or access denied');
    }

    // Start transaction
    $pdo->beginTransaction();

    // If this address is set as default, remove default from other addresses
    if ($isDefault && !$currentAddress['is_default']) {
        $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    // Update address
    $stmt = $pdo->prepare("
        UPDATE user_addresses 
        SET address_name = ?, full_address = ?, city = ?, state = ?, postal_code = ?, is_default = ?, updated_at = NOW()
        WHERE id = ? AND user_id = ?
    ");
    
    $result = $stmt->execute([$addressName, $fullAddress, $city, $state, $postalCode, $isDefault ? 1 : 0, $addressId, $user_id]);

    if (!$result) {
        throw new Exception('Failed to update address');
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Address updated successfully'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Edit address error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>