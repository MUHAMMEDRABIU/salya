<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 30");
$notifications = [];
$borderMap = [
    'order' => 'border-l-orange-500',
    'stock' => 'border-l-red-500',
    'user' => 'border-l-blue-500',
];
$bgMap = [
    'order' => 'bg-orange-100',
    'stock' => 'bg-red-100',
    'user' => 'bg-blue-100',
    'system' => 'bg-gray-100',
    'report' => 'bg-green-100',
    'payment' => 'bg-green-100',
];
$dotMap = [
    'order' => 'bg-orange-500',
    'stock' => 'bg-red-500',
    'user' => 'bg-blue-500',
];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $created = strtotime($row['created_at']);
    $now = time();
    $diff = $now - $created;
    if ($diff < 60) {
        $timeAgo = $diff . ' seconds ago';
    } elseif ($diff < 3600) {
        $timeAgo = floor($diff / 60) . ' minutes ago';
    } elseif ($diff < 86400) {
        $timeAgo = floor($diff / 3600) . ' hours ago';
    } else {
        $timeAgo = date('M d, Y h:i A', $created);
    }
    $notifications[] = [
        'type' => $row['type'],
        'title' => $row['title'],
        'message' => $row['message'],
        'icon' => $row['icon'],
        'color' => $row['color'],
        'time' => $timeAgo,
        'border' => $borderMap[$row['type']] ?? '',
        'bg' => $bgMap[$row['type']] ?? 'bg-gray-100',
        'dot' => $dotMap[$row['type']] ?? '',
    ];
}
echo json_encode($notifications);
