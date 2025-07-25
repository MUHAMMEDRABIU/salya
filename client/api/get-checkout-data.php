<?php
require_once '../initialize.php';
header('Content-Type: application/json');

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $user['id'];

try {
    // Fetch the latest checkout for this user
    $stmt = $pdo->prepare("SELECT * FROM checkouts WHERE id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $data ?: null
    ]);
} catch (Throwable $e) {
    error_log('Get checkout error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error.'
    ]);
}
