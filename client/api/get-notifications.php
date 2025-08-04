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
    // Create notifications table if it doesn't exist
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS user_notifications (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ";
    $pdo->exec($createTableSQL);

    // Get unread notifications for the user
    $stmt = $pdo->prepare("
        SELECT id, title, message, type, created_at
        FROM user_notifications 
        WHERE user_id = ? AND is_read = 0 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark notifications as read
    if (!empty($notifications)) {
        $notificationIds = array_column($notifications, 'id');
        $placeholders = str_repeat('?,', count($notificationIds) - 1) . '?';
        $stmt = $pdo->prepare("UPDATE user_notifications SET is_read = 1 WHERE id IN ($placeholders)");
        $stmt->execute($notificationIds);
    }

    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);

} catch (Exception $e) {
    error_log('Get notifications error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'notifications' => []
    ]);
}
?>