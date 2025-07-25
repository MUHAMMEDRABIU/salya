<?php
require_once __DIR__ . '/../initialize.php';

header('Content-Type: application/json');

// Only allow POST and GET
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Fetch all settings (for populating forms)
    try {
        $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['key']] = $row['value'];
        }
        echo json_encode([
            'success' => true,
            'settings' => $settings
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch settings'
        ]);
    }
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['section']) || !isset($input['data']) || !is_array($input['data'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request'
        ]);
        exit;
    }

    $section = $input['section'];
    $data = $input['data'];

try {
    $pdo->beginTransaction();

    foreach ($data as $key => $value) {
        // Hash new password if updating security settings
        if ($section === 'security' && $key === 'new_password') {
            $value = password_hash($value, PASSWORD_DEFAULT);
            $key = 'Salaya'; // Store under a consistent key
        }
        $stmt = $pdo->prepare("REPLACE INTO settings (`key`, `value`) VALUES (?, ?)");
        $stmt->execute([$key, $value]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => ucfirst($section) . ' settings updated successfully'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update settings'
    ]);
}
exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Invalid request method'
]);
exit;