<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/util/utilities.php';
require __DIR__ . '/../config/constants.php';

$orderNumber = $_GET['order_number'] ?? '';
$order = getOrderByNumber($pdo, $orderNumber);
if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get customer data
$customer = getUserById($pdo, $order['user_id']);
if (!$customer) {
    $customer = [
        'id' => $order['user_id'],
        'first_name' => 'Unknown',
        'last_name' => 'Customer',
        'email' => 'N/A',
        'phone' => 'N/A',
        'avatar' => null
    ];
}

// Get order items (multiple products per order)
$orderItems = getOrderItems($pdo, $order['id']);

require __DIR__ . '/partials/headers.php';
?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>

        <!-- Order Details Content -->
        <main class="p-6">
            <!-- Header Section (keep existing) -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <a href="orders.php" class="text-gray-600 hover:text-gray-900 transition-colors">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
                            <p class="text-gray-600">
                                <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                Placed on <?php echo date('F d, Y \a\t g:i A', strtotime($order['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <?php
                        $status = strtolower($order['status']);
                        $statusConfig = [
                            'delivered' => ['bg-green-100 text-green-800 border-green-200', 'package-check'],
                            'processing' => ['bg-yellow-100 text-yellow-800 border-yellow-200', 'clock'],
                            'pending_payment' => ['bg-orange-100 text-orange-800 border-orange-200', 'clock'],
                            'confirmed' => ['bg-blue-100 text-blue-800 border-blue-200', 'check-circle'],
                            'shipped' => ['bg-blue-100 text-blue-800 border-blue-200', 'truck'],
                            'cancelled' => ['bg-red-100 text-red-800 border-red-200', 'x-circle'],
                        ];
                        $config = $statusConfig[$status] ?? ['bg-gray-100 text-gray-800 border-gray-200', 'help-circle'];
                        ?>
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold border <?php echo $config[0]; ?>">
                            <i data-lucide="<?php echo $config[1]; ?>" class="w-4 h-4 mr-2"></i>
                            <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i data-lucide="package" class="w-5 h-5 mr-2 text-gray-600"></i>
                                Order Items (<?php echo count($orderItems); ?> items)
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <?php if (empty($orderItems)): ?>
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i data-lucide="package" class="w-8 h-8 text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500">No items found for this order</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($orderItems as $item): ?>
                                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                            <?php
                                            // Generate product image URL with fallback
                                            $productImage = !empty($item['product_image']) && $item['product_image'] !== DEFAULT_PRODUCT_IMAGE
                                                ? PRODUCT_IMAGE_URL . htmlspecialchars($item['product_image'])
                                                : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE;
                                            ?>
                                            <img src="<?php echo $productImage; ?>"
                                                alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                class="w-16 h-16 rounded-lg object-cover"
                                                onerror="this.src='<?php echo PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE; ?>';">

                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                                <div class="flex items-center space-x-4 mt-2">
                                                    <span class="text-sm text-gray-500 flex items-center">
                                                        <i data-lucide="hash" class="w-3 h-3 mr-1"></i>
                                                        Qty: <?php echo intval($item['quantity']); ?>
                                                    </span>
                                                    <span class="text-sm text-gray-500 flex items-center">
                                                        <i data-lucide="tag" class="w-3 h-3 mr-1"></i>
                                                        Unit: <?php echo CURRENCY_SYMBOL; ?><?php echo number_format(floatval($item['unit_price']), 2); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-gray-900 flex items-center justify-end">
                                                    <i data-lucide="dollar-sign" class="w-4 h-4 mr-1 text-green-600"></i>
                                                    <?php echo CURRENCY_SYMBOL; ?><?php echo number_format(floatval($item['total_price']), 2); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Order Summary -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 flex items-center">
                                            <i data-lucide="calculator" class="w-4 h-4 mr-2"></i>
                                            Subtotal
                                        </span>
                                        <span class="text-gray-900"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format(floatval($order['subtotal']), 2); ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 flex items-center">
                                            <i data-lucide="truck" class="w-4 h-4 mr-2"></i>
                                            Delivery Fee
                                        </span>
                                        <span class="text-gray-900"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format(floatval($order['delivery_fee']), 2); ?></span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold pt-3 border-t border-gray-200">
                                        <span class="text-gray-900 flex items-center">
                                            <i data-lucide="dollar-sign" class="w-5 h-5 mr-2 text-green-600"></i>
                                            Total
                                        </span>
                                        <span class="text-gray-900"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format(floatval($order['total_amount']), 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i data-lucide="clock" class="w-5 h-5 mr-2 text-gray-600"></i>
                                Order Timeline
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i data-lucide="check" class="w-5 h-5 text-green-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Order Placed</p>
                                        <p class="text-sm text-gray-500"><?php echo date('M d, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
                                        <p class="text-sm text-gray-600 mt-1">Order has been successfully placed</p>
                                    </div>
                                </div>

                                <?php if ($order['status'] !== 'pending'): ?>
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">Processing</p>
                                            <p class="text-sm text-gray-500"><?php echo date('M d, Y \a\t g:i A', strtotime($order['updated_at'])); ?></p>
                                            <p class="text-sm text-gray-600 mt-1">Order is being processed</p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (in_array($order['status'], ['shipped', 'delivered'])): ?>
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i data-lucide="truck" class="w-5 h-5 text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">Shipped</p>
                                            <p class="text-sm text-gray-500"><?php echo date('M d, Y \a\t g:i A', strtotime($order['shipped_at'] ?? $order['updated_at'])); ?></p>
                                            <p class="text-sm text-gray-600 mt-1">Order has been shipped</p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($order['status'] === 'delivered'): ?>
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <i data-lucide="package-check" class="w-5 h-5 text-green-600"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">Delivered</p>
                                            <p class="text-sm text-gray-500"><?php echo date('M d, Y \a\t g:i A', strtotime($order['delivered_at'] ?? $order['updated_at'])); ?></p>
                                            <p class="text-sm text-gray-600 mt-1">Order has been delivered successfully</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i data-lucide="credit-card" class="w-5 h-5 mr-2 text-gray-600"></i>
                                Payment Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1 flex items-center">
                                        <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                                        Payment Method
                                    </p>
                                    <p class="text-sm text-gray-900"><?php echo ucfirst($order['payment_method'] ?? 'Card Payment'); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1 flex items-center">
                                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                                        Payment Status
                                    </p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                        <?php echo ucfirst($order['payment_status'] ?? 'Paid'); ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1 flex items-center">
                                        <i data-lucide="hash" class="w-4 h-4 mr-2"></i>
                                        Transaction ID
                                    </p>
                                    <p class="text-sm text-gray-900 font-mono"><?php echo $order['transaction_id'] ?? 'TXN_' . strtoupper(substr(md5($order['id']), 0, 8)); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1 flex items-center">
                                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                        Payment Date
                                    </p>
                                    <p class="text-sm text-gray-900"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (keep existing customer info, shipping, etc.) -->
                <div class="space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i data-lucide="user" class="w-5 h-5 mr-2 text-gray-600"></i>
                                Customer Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center space-x-4 mb-4">
                                <?php if ($customer['avatar']): ?>
                                    <?php
                                    // Generate user avatar URL with fallback
                                    $userAvatarFile = !empty($customer['avatar']) && $customer['avatar'] !== DEFAULT_USER_AVATAR
                                        ? $customer['avatar']
                                        : DEFAULT_USER_AVATAR;
                                    $userAvatarUrl = USER_AVATAR_URL . htmlspecialchars($userAvatarFile);
                                    ?>
                                    <img src="<?php echo $userAvatarUrl; ?>" alt="Customer" class="w-12 h-12 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i data-lucide="user" class="w-6 h-6 text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h4 class="font-semibold text-gray-900">
                                        <?php echo htmlspecialchars(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')); ?>
                                    </h4>
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <i data-lucide="mail" class="w-3 h-3 mr-1"></i>
                                        <?php echo htmlspecialchars($customer['email'] ?? 'N/A'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 flex items-center">
                                        <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                        Phone
                                    </p>
                                    <p class="text-sm text-gray-900"><?php echo htmlspecialchars($customer['phone'] ?? 'Not provided'); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 flex items-center">
                                        <i data-lucide="hash" class="w-4 h-4 mr-2"></i>
                                        Customer ID
                                    </p>
                                    <p class="text-sm text-gray-900">#<?php echo $customer['id']; ?></p>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="view-user.php?id=<?= htmlspecialchars($customer['id']); ?>" class="w-full bg-gray-100 block text-center text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium flex items-center justify-center">
                                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                                    View Customer Profile
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i data-lucide="truck" class="w-5 h-5 mr-2 text-gray-600"></i>
                                Shipping Address
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-sm">
                                <p class="font-medium text-gray-900 flex items-center">
                                    <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                    <?php echo htmlspecialchars($order['shipping_name'] ?? ($customer['first_name'] . ' ' . $customer['last_name'])); ?>
                                </p>
                                <p class="text-gray-600 flex items-start">
                                    <i data-lucide="map-pin" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                                    <?php echo htmlspecialchars($order['shipping_address'] ?? 'No address provided'); ?>
                                </p>
                                <p class="text-gray-600 flex items-center">
                                    <i data-lucide="globe" class="w-4 h-4 mr-2"></i>
                                    <?php echo htmlspecialchars($order['shipping_city'] ?? ''); ?>,
                                    <?php echo htmlspecialchars($order['shipping_state'] ?? ''); ?>
                                    <?php echo htmlspecialchars($order['shipping_postal_code'] ?? ''); ?>
                                </p>
                                <p class="text-gray-600 flex items-center">
                                    <i data-lucide="flag" class="w-4 h-4 mr-2"></i>
                                    <?php echo htmlspecialchars($order['shipping_country'] ?? 'Nigeria'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i data-lucide="credit-card" class="w-5 h-5 mr-2 text-gray-600"></i>
                                Billing Address
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-sm">
                                <p class="font-medium text-gray-900 flex items-center">
                                    <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                    <?php echo htmlspecialchars($order['billing_name'] ?? ($customer['first_name'] . ' ' . $customer['last_name'])); ?>
                                </p>
                                <p class="text-gray-600 flex items-start">
                                    <i data-lucide="map-pin" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                                    <?php echo htmlspecialchars($order['billing_address'] ?? $order['shipping_address'] ?? 'Same as shipping address'); ?>
                                </p>
                                <p class="text-gray-600 flex items-center">
                                    <i data-lucide="globe" class="w-4 h-4 mr-2"></i>
                                    <?php echo htmlspecialchars($order['billing_city'] ?? $order['shipping_city'] ?? ''); ?>,
                                    <?php echo htmlspecialchars($order['billing_state'] ?? $order['shipping_state'] ?? ''); ?>
                                    <?php echo htmlspecialchars($order['billing_postal_code'] ?? $order['shipping_postal_code'] ?? ''); ?>
                                </p>
                                <p class="text-gray-600 flex items-center">
                                    <i data-lucide="flag" class="w-4 h-4 mr-2"></i>
                                    <?php echo htmlspecialchars($order['billing_country'] ?? $order['shipping_country'] ?? 'Nigeria'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i data-lucide="settings" class="w-5 h-5 mr-2 text-gray-600"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button class="w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors text-sm font-medium flex items-center justify-center">
                                <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                Send Email to Customer
                            </button>
                            <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium flex items-center justify-center">
                                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                                Print Order Details
                            </button>
                            <button class="w-full bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors text-sm font-medium flex items-center justify-center">
                                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                                Update Order Status
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <script src="js/script.js"></script>
</body>

</html>