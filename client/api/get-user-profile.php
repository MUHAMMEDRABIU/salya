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
    // Get fresh user profile data
    $user = getUserProfile($pdo, $user_id);
    
    if (!$user) {
        throw new Exception('User profile not found');
    }

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
    
} catch (Exception $e) {
    error_log('Get user profile error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}