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

    $preference = $data['preference'] ?? '';
    $value = $data['value'] ?? false;

    if (empty($preference)) {
        throw new Exception('Preference key is required');
    }

    // Check if user preferences exist
    $stmt = $pdo->prepare("SELECT id FROM user_preferences WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        // Update existing preferences
        $stmt = $pdo->prepare("UPDATE user_preferences SET {$preference} = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->execute([$value ? 1 : 0, $user_id]);
    } else {
        // Create new preferences record
        $stmt = $pdo->prepare("
            INSERT INTO user_preferences (user_id, {$preference}, created_at, updated_at) 
            VALUES (?, ?, NOW(), NOW())
        ");
        $stmt->execute([$user_id, $value ? 1 : 0]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Preference updated successfully'
    ]);
} catch (Exception $e) {
    error_log('Update preferences error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
