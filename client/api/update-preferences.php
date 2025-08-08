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

    $key = $data['key'] ?? '';
    $value = $data['value'] ?? false;

    if (empty($key)) {
        throw new Exception('Preference key is required');
    }

    // Valid preference keys
    $validKeys = ['push_notifications', 'email_updates'];
    if (!in_array($key, $validKeys)) {
        throw new Exception('Invalid preference key');
    }

    // Check if preferences exist
    $stmt = $pdo->prepare("SELECT id FROM user_preferences WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE user_preferences SET {$key} = ? WHERE user_id = ?");
        $result = $stmt->execute([$value ? 1 : 0, $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO user_preferences (user_id, {$key}) VALUES (?, ?)");
        $result = $stmt->execute([$user_id, $value ? 1 : 0]);
    }

    if (!$result) {
        throw new Exception('Failed to update preference');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Preference updated successfully'
    ]);
} catch (Exception $e) {
    error_log('Update preferences error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
