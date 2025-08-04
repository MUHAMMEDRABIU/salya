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

    // Check if address belongs to user and is not default
    $stmt = $pdo->prepare("SELECT is_default FROM user_addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$addressId, $user_id]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$address) {
        throw new Exception('Address not found or access denied');
    }

    if ($address['is_default']) {
        throw new Exception('Cannot delete default address. Set another address as default first.');
    }

    // Delete address
    $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$addressId, $user_id]);

    if (!$result || $stmt->rowCount() === 0) {
        throw new Exception('Failed to delete address');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Address deleted successfully'
    ]);

} catch (Exception $e) {
    error_log('Delete address error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>