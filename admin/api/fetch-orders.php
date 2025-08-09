<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/../config/constants.php';
require __DIR__ . '/util/utilities.php';

$orders = getAllOrders($pdo);
$data = [];

foreach ($orders as $order) {
    $user = getUserById($pdo, $order['user_id']);
    $statusColors = [
        'delivered' => 'bg-green-100 text-green-800',
        'processing' => 'bg-yellow-100 text-yellow-800',
        'pending' => 'bg-orange-100 text-orange-800',
        'shipped' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
    $status = strtolower($order['status']);
    $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';

    $data[] = [
        'order_id' => '#' . htmlspecialchars($order['order_number'] ?? $order['id']),
        'customer' => '
            <div class="flex items-center">
                <img src="' . USER_AVATAR_URL . ($user['avatar'] ?: DEFAULT_USER_AVATAR) . '" class="w-8 h-8 rounded-full mr-3">
                <div>
                    <div class="text-sm font-medium text-gray-900">' . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . '</div>
                    <div class="text-sm text-gray-500">' . htmlspecialchars($user['email']) . '</div>
                </div>
            </div>',
        'reference' => htmlspecialchars($order['payment_reference'] ?? 'N/A'),
        'amount' => CURRENCY_SYMBOL . number_format($order['total_amount'], 2),
        'status' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $color . '">' . ucfirst($order['status']) . '</span>',
        'timestamp' => date('M d, Y, g:i A', strtotime($order['created_at'])),
        'actions' => '<a href="view-order.php?order_number=' . urlencode($order['order_number']) . '" class="text-xs bg-gray-100 px-3 rounded py-1 text-orange-600 hover:text-orange-900">View</a>'
    ];
}

echo json_encode(['data' => $data]);
