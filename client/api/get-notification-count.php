<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../../util/util.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $unread_count = getUnreadNotificationCount($pdo, $user_id);
    echo json_encode([
        'success' => true,
        'unread_count' => $unread_count
    ]);
} catch (Exception $e) {
    error_log('Notification count error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch notification count'
    ]);
}
