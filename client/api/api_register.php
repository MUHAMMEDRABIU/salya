<?php
require __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $fullName = $data['fullName'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
        exit();
    }

    // Split fullName into first_name and last_name
    $nameParts = preg_split('/\s+/', trim($fullName));
    if (count($nameParts) === 1) {
        $first_name = $nameParts[0];
        $last_name = '';
    } elseif (count($nameParts) === 2) {
        $first_name = $nameParts[0];
        $last_name = $nameParts[1];
    } else {
        $first_name = $nameParts[0];
        $last_name = implode(' ', array_slice($nameParts, 1));
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $phone, $hashedPassword]);

    echo json_encode(['success' => true, 'message' => 'Registration successful.']);
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while processing your request.']);
} catch (Exception $e) {
    error_log('General error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}
