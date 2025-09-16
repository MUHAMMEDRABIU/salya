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

    $first_name = trim($data['first_name'] ?? '');
    $last_name = trim($data['last_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email)) {
        throw new Exception('First name, last name, and email are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        throw new Exception('Email is already taken by another user');
    }

    // Update user profile
    $stmt = $pdo->prepare("
        UPDATE users 
        SET first_name = ?, last_name = ?, email = ?, phone = ?, updated_at = NOW() 
        WHERE id = ?
    ");
    $result = $stmt->execute([$first_name, $last_name, $email, $phone, $user_id]);

    if (!$result) {
        throw new Exception('Failed to update profile');
    }

    // Custom color for notification types
    $notifType = 'updates';
    $notifIcon = 'fas fa-user-edit text-blue-600';
    $notifColor = 'bg-orange-100';
    
    pushNotification(
        $pdo,
        $user_id,
        'Profile Updated',
        'Your profile information was updated successfully.',
        $notifType,
        $notifIcon,
        $notifColor,
        'View Profile'
    );

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
} catch (Exception $e) {
    error_log('Update profile error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
