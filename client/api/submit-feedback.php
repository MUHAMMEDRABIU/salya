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

    $type = trim($data['type'] ?? '');
    $message = trim($data['message'] ?? '');

    if (empty($type) || empty($message)) {
        throw new Exception('Feedback type and message are required');
    }

    $validTypes = ['bug', 'suggestion', 'complaint', 'praise', 'other'];
    if (!in_array($type, $validTypes)) {
        throw new Exception('Invalid feedback type');
    }

    if (strlen($message) > 1000) {
        throw new Exception('Message is too long. Maximum 1000 characters allowed.');
    }

    // Get user info for context
    $stmt = $pdo->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Create feedback table if it doesn't exist
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS user_feedback (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            type ENUM('bug', 'suggestion', 'complaint', 'praise', 'other') NOT NULL,
            message TEXT NOT NULL,
            status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ";
    $pdo->exec($createTableSQL);

    // Insert feedback
    $stmt = $pdo->prepare("
        INSERT INTO user_feedback (user_id, type, message, created_at, updated_at)
        VALUES (?, ?, ?, NOW(), NOW())
    ");
    
    $result = $stmt->execute([$user_id, $type, $message]);

    if (!$result) {
        throw new Exception('Failed to submit feedback');
    }

    $feedbackId = $pdo->lastInsertId();

    // Send email notification to admin (optional)
    $subject = "New Feedback: " . ucfirst($type);
    $emailMessage = "
        New feedback received from: {$user['first_name']} {$user['last_name']} ({$user['email']})
        
        Type: " . ucfirst($type) . "
        Message: $message
        
        Feedback ID: $feedbackId
        Submitted: " . date('Y-m-d H:i:s') . "
    ";

    // You can implement email sending here
    // mail('admin@salya.com', $subject, $emailMessage);

    // Log feedback for admin review
    error_log("New feedback received - Type: $type, User ID: $user_id, Feedback ID: $feedbackId");

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your feedback! We will review it and get back to you if needed.',
        'feedback_id' => $feedbackId
    ]);

} catch (Exception $e) {
    error_log('Submit feedback error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>