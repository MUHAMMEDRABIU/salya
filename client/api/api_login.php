<?php
session_start();
require __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

try {
    // Decode JSON payload safely
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!is_array($data)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request format.']);
        exit;
    }

    $email = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = trim($data['password'] ?? '');

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    // Look up user securely
    $stmt = $pdo->prepare("
        SELECT id, password_hash 
        FROM users 
        WHERE email = :email 
        LIMIT 1
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check credentials
    if (!$user || !password_verify($password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }

    // Regenerate session ID to prevent fixation attacks
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];

    echo json_encode(['success' => true, 'message' => 'Login successful.']);
} catch (PDOException $e) {
    error_log('[LOGIN ERROR] ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again later.']);
} catch (Throwable $e) {
    error_log('[GENERAL ERROR] ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unexpected error occurred.']);
}
