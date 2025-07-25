<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/util/utilities.php';
require __DIR__ . '/partials/headers.php';

$orderNumber = $_GET['order_number'] ?? '';
$order = getOrderByNumber($pdo, $orderNumber);
if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get related data
$customer = getUserById($pdo, $order['user_id']);
$product = getProductById($pdo, $order['product_id']);
?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>

        <!-- Order Details Content -->
        <main class="p-6">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="dashboard.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-orange-600">
                                <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                                <a href="orders.php" class="ml-1 text-sm font-medium text-gray-700 hover:text-orange-600 md:ml-2">Orders</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">#<?= htmlspecialchars($order['order_number']) ?></span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <a href="orders.php" class="text-gray-600 hover:text-gray-900 transition-colors">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Order #<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></h1>
                            <p class="text-gray-600">Placed on <?php echo date('F d, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <?php
                        $status = strtolower($order['status']);
                        $statusColors = [
                            'delivered' => 'bg-green-100 text-green-800 border-green-200',
                            'processing' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'pending' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'shipped' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                        $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        ?>
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold border <?php echo $color; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                        <!-- <button class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                            <i data-lucide="edit" class="w-4 h-4 mr-2 inline"></i>
                            Edit Order
                        </button> -->
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Order Items</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="Product" class="w-16 h-16 rounded-lg object-cover">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($product['name']); ?></h4>
                                        <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <span class="text-sm text-gray-500">Quantity: <?php echo $order['quantity'] ?? 1; ?></span>
                                            <span class="text-sm text-gray-500">Unit Price: ₦<?php echo number_format($product['price'], 2); ?></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">₦<?php echo number_format($order['total_amount'], 2); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="text-gray-900">₦<?php echo number_format($order['total_amount'] - ($order['shipping_fee'] ?? 0), 2); ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Shipping</span>
                                        <span class="text-gray-900">₦<?php echo number_format($order['shipping_fee'] ?? 0, 2); ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Tax</span>
                                        <span class="text-gray-900">₦<?php echo number_format($order['tax_amount'] ?? 0, 2); ?></span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold pt-3 border-t border-gray-200">
                                        <span class="text-gray-900">Total</span>
                                        <span class="text-gray-900">₦<?php echo number_format($order['total_amount'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Order Timeline</h3>
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
                            <h3 class="text-lg font-semibold text-gray-800">Payment Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Payment Method</p>
                                    <p class="text-sm text-gray-900"><?php echo ucfirst($order['payment_method'] ?? 'Card Payment'); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Payment Status</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?php echo ucfirst($order['payment_status'] ?? 'Paid'); ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Transaction ID</p>
                                    <p class="text-sm text-gray-900 font-mono"><?php echo $order['transaction_id'] ?? 'TXN_' . strtoupper(substr(md5($order['id']), 0, 8)); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Payment Date</p>
                                    <p class="text-sm text-gray-900"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Customer Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center space-x-4 mb-4">
                                <img src="../assets/uploads/<?php echo $customer['avatar']; ?>"
                                    alt="Customer" class="w-12 h-12 rounded-full object-cover">
                                <div>
                                    <h4 class="font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>
                                    </h4>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($customer['email']); ?></p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Phone</p>
                                    <p class="text-sm text-gray-900"><?php echo htmlspecialchars($customer['phone'] ?? 'Not provided'); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Customer ID</p>
                                    <p class="text-sm text-gray-900">#<?php echo $customer['id']; ?></p>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="view-user.php?id=<?= htmlspecialchars($customer['id']); ?>" class="w-full bg-gray-100 block text-center text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                    View Customer Profile
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Shipping Address</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-sm">
                                <p class="font-medium text-gray-900">
                                    <?php echo htmlspecialchars($order['shipping_name'] ?? $customer['first_name'] . ' ' . $customer['last_name']); ?>
                                </p>
                                <p class="text-gray-600"><?php echo htmlspecialchars($order['shipping_address'] ?? 'No address provided'); ?></p>
                                <p class="text-gray-600">
                                    <?php echo htmlspecialchars($order['shipping_city'] ?? ''); ?>,
                                    <?php echo htmlspecialchars($order['shipping_state'] ?? ''); ?>
                                    <?php echo htmlspecialchars($order['shipping_postal_code'] ?? ''); ?>
                                </p>
                                <p class="text-gray-600"><?php echo htmlspecialchars($order['shipping_country'] ?? 'Nigeria'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Billing Address</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-sm">
                                <p class="font-medium text-gray-900">
                                    <?php echo htmlspecialchars($order['billing_name'] ?? $customer['first_name'] . ' ' . $customer['last_name']); ?>
                                </p>
                                <p class="text-gray-600"><?php echo htmlspecialchars($order['billing_address'] ?? $order['shipping_address'] ?? 'Same as shipping address'); ?></p>
                                <p class="text-gray-600">
                                    <?php echo htmlspecialchars($order['billing_city'] ?? $order['shipping_city'] ?? ''); ?>,
                                    <?php echo htmlspecialchars($order['billing_state'] ?? $order['shipping_state'] ?? ''); ?>
                                    <?php echo htmlspecialchars($order['billing_postal_code'] ?? $order['shipping_postal_code'] ?? ''); ?>
                                </p>
                                <p class="text-gray-600"><?php echo htmlspecialchars($order['billing_country'] ?? $order['shipping_country'] ?? 'Nigeria'); ?></p>
                            </div>
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