<?php
require __DIR__ . '/initialize.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['user_id'])) {
        throw new Exception("User ID is required.");
    }

    $userId = (int) $input['user_id'];

    // Fetch current status
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }

    $newStatus = $user['status'] === 'Active' ? 'Suspended' : 'Active';

    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $userId]);

    echo json_encode(['success' => true, 'message' => "User status updated to $newStatus"]);
} catch (Exception $e) {
    error_log("[STATUS TOGGLE ERROR] " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
