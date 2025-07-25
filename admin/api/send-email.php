<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/../../helpers/sendMail.php';

header('Content-Type: application/json');

try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
        exit();
    }

    // Decode JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['user_id'] ?? null;
    $subject = trim($data['subject'] ?? '');
    $message = trim($data['message'] ?? '');

    // Validate input
    if (!$userId || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Fetch user's email
    $stmt = $pdo->prepare("SELECT email, first_name FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit();
    }

    // Send email using reusable sendMail function
    $emailSent = sendMail($user['email'], $user['first_name'], $subject, $message);

    if ($emailSent) {
        echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
    }
} catch (Exception $e) {
    error_log("[EMAIL API ERROR] " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
}
