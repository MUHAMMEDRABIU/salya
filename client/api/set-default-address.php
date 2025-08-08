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

    if (!$addressId) {
        throw new Exception('Address ID is required');
    }

    // Check if address belongs to user
    $stmt = $pdo->prepare("SELECT id FROM user_addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$addressId, $user_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Address not found or access denied');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Remove default from all user addresses
    $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Set new default address
    $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 1, updated_at = NOW() WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$addressId, $user_id]);

    if (!$result) {
        throw new Exception('Failed to set default address');
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Default address updated successfully'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Set default address error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>