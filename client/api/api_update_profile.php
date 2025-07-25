<?php
require __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

session_start();

try {
    // Only allow logged-in users
    if (empty($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $fullName = trim($data['fullName'] ?? '');
    $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = preg_replace('/[^\d+]/', '', trim($data['phone'] ?? ''));

    if (empty($fullName) || empty($email) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }

    // Split full name into first and last name
    $nameParts = preg_split('/\s+/', $fullName);
    if (count($nameParts) === 1) {
        $first_name = $nameParts[0];
        $last_name = '';
    } else {
        $first_name = $nameParts[0];
        $last_name = implode(' ', array_slice($nameParts, 1));
    }

    // Check if email is already used by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email is already in use by another account.']);
        exit;
    }

    // Update user in database
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $email, $phone, $_SESSION['user_id']]);

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
} catch (Exception $e) {
    error_log('Profile update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
} 