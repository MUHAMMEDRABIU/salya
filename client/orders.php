<?php
require __DIR__ . '/initialize.php';
require_once 'util/util.php';
require __DIR__ . '/../config/constants.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart count for logged in users
$cartCount = 0;
try {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cartCount = (int)($result['total_items'] ?? 0);
} catch (Exception $e) {
    error_log("Error getting cart count in orders: " . $e->getMessage());
    $cartCount = 0;
}

// Get selected status from URL parameter
$selectedStatus = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';

// Get all orders for the user
function getUserOrders($pdo, $user_id, $status = 'all')
{
    try {
        $sql = "
            SELECT 
                o.id,
                o.order_number,
                o.status,
                o.total_amount,
                o.subtotal,
                o.delivery_fee,
                o.created_at,
                o.updated_at,
                o.payment_status,
                o.shipping_name,
                o.shipping_address,
                o.shipping_city,
                o.shipping_state,
                COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ?
        ";

        $params = [$user_id];

        if ($status !== 'all') {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }

        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get order items for each order
        foreach ($orders as &$order) {
            $itemStmt = $pdo->prepare("
                SELECT 
                    oi.product_name as name,
                    oi.quantity,
                    oi.price,
                    oi.subtotal,
                    oi.product_image as image
                FROM order_items oi
                WHERE oi.order_id = ?
            ");
            $itemStmt->execute([$order['id']]);
            $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $orders;
    } catch (PDOException $e) {
        error_log("Error fetching user orders: " . $e->getMessage());
        return [];
    }
}

$orders = getUserOrders($pdo, $user_id, $selectedStatus);

// Calculate stats
$totalOrders = count(getUserOrders($pdo, $user_id, 'all'));
$totalSpent = 0;
$allOrders = getUserOrders($pdo, $user_id, 'all');
foreach ($allOrders as $order) {
    if ($order['status'] !== 'cancelled') {
        $totalSpent += $order['total_amount'];
    }
}

require_once 'partials/headers.php';
?>
</head>

<body class="bg-gray-50 font-dm pb-24 overflow-x-hidden">
    <!-- Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-orange-500 opacity-5 rounded-full filter blur-3xl animate-float"></div>
        <div class="absolute top-1/2 -left-32 w-64 h-64 bg-purple-500 opacity-5 rounded-full filter blur-3xl animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 right-1/4 w-48 h-48 bg-orange-500 opacity-8 rounded-full filter blur-3xl animate-float" style="animation-delay: 2s;"></div>
    </div>

    <!-- Pull to Refresh Indicator -->
    <div id="pull-indicator" class="pull-indicator fixed top-0 left-0 right-0 bg-orange-500 text-white text-center py-3 z-50 transform -translate-y-full transition-transform duration-300">
        <i class="fas fa-sync-alt mr-2"></i>
        <span>Release to refresh</span>
    </div>

    <!-- Main Content -->
    <main class="relative z-10">
        <div class="container mx-auto px-4 pt-6 space-y-6">
            <!-- Page Header -->
            <?php include 'partials/top-nav.php'; ?>

            <!-- Hero Section -->
            <div class="bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 rounded-3xl p-6 md:p-8 text-white mb-8 relative overflow-hidden animate-slide-up">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10">
                    <div class="max-w-2xl">
                        <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold mb-4">Your Orders</h1>
                        <p class="text-orange-100 text-sm md:text-base mb-6">Track and manage all your frozen food orders in one place.</p>
                        <div class="flex flex-wrap items-center gap-4 md:gap-6 text-orange-100">
                            <div class="flex items-center">
                                <i class="fas fa-box mr-2"></i>
                                <span class="text-xs md:text-sm">Order History</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-truck mr-2"></i>
                                <span class="text-xs md:text-sm">Real-time Tracking</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-redo mr-2"></i>
                                <span class="text-xs md:text-sm">Easy Reorder</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-slide-up" style="animation-delay: 0.1s;">
                <div class="bg-white rounded-2xl p-4 md:p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Orders</p>
                            <p class="text-xl md:text-2xl font-bold text-gray-900"><?php echo $totalOrders; ?></p>
                        </div>
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-100 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-orange-500 text-lg"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 md:p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Spent</p>
                            <p class="text-xl md:text-2xl font-bold text-gray-900"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($totalSpent); ?></p>
                        </div>
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-naira-sign text-green-600 text-lg"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 md:p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Delivered</p>
                            <p class="text-xl md:text-2xl font-bold text-gray-900">
                                <?php
                                $delivered = count(getUserOrders($pdo, $user_id, 'delivered'));
                                echo $delivered;
                                ?>
                            </p>
                        </div>
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-lg"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 md:p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Processing</p>
                            <p class="text-xl md:text-2xl font-bold text-gray-900">
                                <?php
                                $processing = count(getUserOrders($pdo, $user_id, 'processing'));
                                echo $processing;
                                ?>
                            </p>
                        </div>
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-2xl p-4 md:p-6 shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0 lg:space-x-6">
                    <!-- Search Bar -->
                    <div class="flex-1 lg:max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.35-4.35" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                id="search-input"
                                placeholder="Search orders by number or item..."
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>

                    <!-- Date Filter -->
                    <div class="relative">
                        <select id="date-filter" class="appearance-none bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pr-12 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent min-w-[140px] cursor-pointer">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Filter Tabs (Responsive Horizontal Scroll) -->
            <div class="animate-slide-up" style="animation-delay: 0.3s;">
                <div class="overflow-x-auto hide-scrollbar">
                    <div class="flex space-x-3 pb-2 min-w-max px-1">
                        <a href="?status=all"
                            class="status-tab <?php echo $selectedStatus === 'all' ? 'active' : ''; ?> px-4 md:px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300">
                            <i class="fas fa-list mr-2"></i>
                            All Orders
                        </a>
                        <a href="?status=confirmed"
                            class="status-tab <?php echo $selectedStatus === 'confirmed' ? 'active' : ''; ?> px-4 md:px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirmed
                        </a>
                        <a href="?status=processing"
                            class="status-tab <?php echo $selectedStatus === 'processing' ? 'active' : ''; ?> px-4 md:px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300">
                            <i class="fas fa-clock mr-2"></i>
                            Processing
                        </a>
                        <a href="?status=shipped"
                            class="status-tab <?php echo $selectedStatus === 'shipped' ? 'active' : ''; ?> px-4 md:px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300">
                            <i class="fas fa-truck mr-2"></i>
                            Shipped
                        </a>
                        <a href="?status=delivered"
                            class="status-tab <?php echo $selectedStatus === 'delivered' ? 'active' : ''; ?> px-4 md:px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300">
                            <i class="fas fa-check-double mr-2"></i>
                            Delivered
                        </a>
                        <a href="?status=cancelled"
                            class="status-tab <?php echo $selectedStatus === 'cancelled' ? 'active' : ''; ?> px-4 md:px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300">
                            <i class="fas fa-times-circle mr-2"></i>
                            Cancelled
                        </a>
                    </div>
                </div>
            </div>

            <!-- Results Info -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-slide-up" style="animation-delay: 0.4s;">
                <div class="flex-1">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
                        <?php echo $selectedStatus === 'all' ? 'All Orders' : ucfirst($selectedStatus) . ' Orders'; ?>
                    </h2>
                    <p class="text-gray-600 text-sm md:text-base">
                        Showing <?php echo count($orders); ?> orders
                        <?php if ($selectedStatus !== 'all'): ?>
                            with status "<?php echo ucfirst($selectedStatus); ?>"
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Responsive Refresh Button -->
                <div class="flex-shrink-0">
                    <button id="refresh-btn" class="flex items-center justify-center px-4 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl text-gray-700 transition-all duration-300 hover:scale-105 min-w-[44px] sm:min-w-auto">
                        <i class="fas fa-sync-alt text-sm sm:mr-2"></i>
                        <span class="hidden sm:inline text-sm font-medium">Refresh</span>
                    </button>
                </div>
            </div>

            <!-- Orders List -->
            <div id="orders-container" class="space-y-4 animate-slide-up" style="animation-delay: 0.5s;">
                <?php if (empty($orders)): ?>
                    <!-- Empty State -->
                    <div class="text-center py-16 animate-fade-in">
                        <div class="w-24 h-24 md:w-32 md:h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shopping-bag text-gray-400 text-2xl md:text-3xl"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-3">No Orders Found</h3>
                        <p class="text-gray-600 text-sm md:text-base mb-6 max-w-md mx-auto">
                            <?php if ($selectedStatus !== 'all'): ?>
                                You don't have any <?php echo $selectedStatus; ?> orders yet.
                            <?php else: ?>
                                You haven't placed any orders yet. Start shopping to see your orders here!
                            <?php endif; ?>
                        </p>
                        <a href="dashboard.php" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Start Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $index => $order):
                        $statusInfo = getStatusInfo($order['status']);
                    ?>
                        <div class="order-card bg-white rounded-3xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 animate-scale-in"
                            data-status="<?php echo $order['status']; ?>"
                            data-order-number="<?php echo strtolower($order['order_number']); ?>"
                            data-items="<?php echo strtolower(implode(' ', array_column($order['items'], 'name'))); ?>"
                            style="animation-delay: <?php echo $index * 0.1; ?>s;">

                            <!-- Order Header -->
                            <div class="p-4 md:p-6 cursor-pointer" onclick="toggleOrderDetails('<?php echo $order['order_number']; ?>')">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 text-base md:text-lg"><?php echo $order['order_number']; ?></h3>
                                        <p class="text-gray-500 text-xs md:text-sm">
                                            <?php echo date('M d, Y \a\t g:i A', strtotime($order['created_at'])); ?>
                                        </p>
                                        <?php if (!empty($order['shipping_city'])): ?>
                                            <p class="text-gray-400 text-xs mt-1">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                <?php echo $order['shipping_city'] . ', ' . $order['shipping_state']; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="text-xl md:text-2xl font-bold text-orange-500"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($order['total_amount']); ?></p>
                                        <p class="text-gray-500 text-xs md:text-sm"><?php echo $order['items_count']; ?> item<?php echo $order['items_count'] > 1 ? 's' : ''; ?></p>
                                        <?php if ($order['payment_status'] === 'verified'): ?>
                                            <p class="text-green-600 text-xs mt-1">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Paid
                                            </p>
                                        <?php elseif ($order['payment_status'] === 'pending'): ?>
                                            <p class="text-yellow-600 text-xs mt-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                Payment Pending
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs md:text-sm font-semibold <?php echo $statusInfo['color'] . ' ' . $statusInfo['bg']; ?> w-fit">
                                        <i class="<?php echo $statusInfo['icon']; ?> mr-2 text-xs"></i>
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                    <button class="expand-btn w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors self-end sm:self-center">
                                        <i class="fas fa-chevron-down text-gray-600 text-sm transition-transform duration-300"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Order Details (Expandable) -->
                            <div id="details-<?php echo $order['order_number']; ?>" class="order-details">
                                <div class="px-4 md:px-6 pb-4 md:pb-6 border-t border-gray-100">
                                    <div class="pt-4 space-y-4">
                                        <h4 class="font-semibold text-gray-900 mb-3 text-sm md:text-base">Order Items</h4>

                                        <!-- Order Items Grid -->
                                        <div class="space-y-3">
                                            <?php foreach ($order['items'] as $item): ?>
                                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                                                    <?php if (!empty($item['image'])): ?>
                                                        <?php
                                                        // Generate product image URL with fallback
                                                        $productImage = !empty($item['image']) && $item['image'] !== DEFAULT_PRODUCT_IMAGE
                                                            ? PRODUCT_IMAGE_URL . htmlspecialchars($item['image'])
                                                            : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE;
                                                        ?>
                                                        <img src="<?php echo $productImage; ?>"
                                                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                            class="w-12 h-12 md:w-16 md:h-16 rounded-lg object-cover flex-shrink-0">
                                                    <?php else: ?>
                                                        <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-300 rounded-lg flex items-center justify-center flex-shrink-0">
                                                            <i class="fas fa-image text-gray-400"></i>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-medium text-gray-900 text-sm md:text-base truncate">
                                                            <?php echo htmlspecialchars($item['name']); ?>
                                                        </p>
                                                        <p class="text-gray-500 text-xs md:text-sm">
                                                            Qty: <?php echo $item['quantity']; ?> × <?php echo CURRENCY_SYMBOL; ?><?php echo number_format($item['price']); ?>
                                                        </p>
                                                    </div>

                                                    <div class="text-right flex-shrink-0">
                                                        <span class="font-semibold text-orange-500 text-sm md:text-base">
                                                            <?php echo CURRENCY_SYMBOL; ?><?php echo number_format($item['subtotal']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <!-- Order Summary -->
                                        <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Subtotal:</span>
                                                <span class="text-gray-900"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($order['subtotal']); ?></span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Delivery Fee:</span>
                                                <span class="text-gray-900"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($order['delivery_fee']); ?></span>
                                            </div>
                                            <div class="flex justify-between text-base font-semibold pt-2 border-t border-gray-200">
                                                <span class="text-gray-900">Total:</span>
                                                <span class="text-orange-500"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($order['total_amount']); ?></span>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6">
                                            <?php if ($order['status'] === 'delivered'): ?>
                                                <button onclick="reorderItems('<?php echo $order['order_number']; ?>')"
                                                    class="w-full bg-orange-500 text-white py-3 rounded-2xl font-semibold hover:bg-orange-600 transition-all duration-300 transform hover:scale-105 text-sm md:text-base">
                                                    <i class="fas fa-redo mr-2"></i>
                                                    Reorder
                                                </button>
                                                <button onclick="rateOrder('<?php echo $order['order_number']; ?>')"
                                                    class="w-full bg-gray-100 text-gray-700 py-3 rounded-2xl font-semibold hover:bg-gray-200 transition-all duration-300 transform hover:scale-105 text-sm md:text-base">
                                                    <i class="fas fa-star mr-2"></i>
                                                    Rate Order
                                                </button>
                                            <?php elseif ($order['status'] === 'processing' || $order['status'] === 'confirmed'): ?>
                                                <button onclick="trackOrder('<?php echo $order['order_number']; ?>')"
                                                    class="w-full bg-blue-500 text-white py-3 rounded-2xl font-semibold hover:bg-blue-600 transition-all duration-300 transform hover:scale-105 text-sm md:text-base">
                                                    <i class="fas fa-truck mr-2"></i>
                                                    Track Order
                                                </button>
                                                <button onclick="contactSupport('<?php echo $order['order_number']; ?>')"
                                                    class="w-full bg-gray-100 text-gray-700 py-3 rounded-2xl font-semibold hover:bg-gray-200 transition-all duration-300 transform hover:scale-105 text-sm md:text-base">
                                                    <i class="fas fa-headset mr-2"></i>
                                                    Contact Support
                                                </button>
                                            <?php elseif ($order['status'] === 'pending_payment'): ?>
                                                <button onclick="cancelOrder('<?php echo $order['order_number']; ?>')"
                                                    class="w-full bg-red-500 text-white py-3 rounded-2xl font-semibold hover:bg-red-600 transition-all duration-300 transform hover:scale-105 text-sm md:text-base">
                                                    <i class="fas fa-times mr-2"></i>
                                                    Cancel Order
                                                </button>
                                                <button onclick="retryPayment('<?php echo $order['order_number']; ?>')"
                                                    class="w-full bg-orange-500 text-white py-3 rounded-2xl font-semibold hover:bg-orange-600 transition-all duration-300 transform hover:scale-105 text-sm md:text-base">
                                                    <i class="fas fa-credit-card mr-2"></i>
                                                    Complete Payment
                                                </button>
                                            <?php else: ?>
                                                <button onclick="reorderItems('<?php echo $order['order_number']; ?>')"
                                                    class="w-full bg-orange-500 text-white py-3 rounded-2xl font-semibold hover:bg-orange-600 transition-all duration-300 transform hover:scale-105 text-sm md:text-base">
                                                    <i class="fas fa-redo mr-2"></i>
                                                    Reorder
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Bottom navigation include -->
    <?php include 'partials/bottom-nav.php'; ?>

    <!-- Scripts -->
    <script src="../assets/js/toast.js"></script>
    <script src="js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize cart count
            updateCartCount();

            // Order details toggle functionality
            window.toggleOrderDetails = function(orderNumber) {
                const detailsElement = document.getElementById(`details-${orderNumber}`);
                const expandBtn = detailsElement.parentElement.querySelector('.expand-btn i');

                if (detailsElement.classList.contains('expanded')) {
                    detailsElement.classList.remove('expanded');
                    expandBtn.style.transform = 'rotate(0deg)';
                } else {
                    // Close all other expanded details
                    document.querySelectorAll('.order-details.expanded').forEach(el => {
                        el.classList.remove('expanded');
                        el.parentElement.querySelector('.expand-btn i').style.transform = 'rotate(0deg)';
                    });

                    detailsElement.classList.add('expanded');
                    expandBtn.style.transform = 'rotate(180deg)';
                }
            };

            // Search functionality
            const searchInput = document.getElementById('search-input');
            const orderCards = document.querySelectorAll('.order-card');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();

                    orderCards.forEach(card => {
                        const orderNumber = card.getAttribute('data-order-number');
                        const items = card.getAttribute('data-items');
                        const isVisible = orderNumber.includes(searchTerm) || items.includes(searchTerm);

                        if (isVisible) {
                            card.style.display = 'block';
                            card.classList.add('animate-fade-in');
                        } else {
                            card.style.display = 'none';
                            card.classList.remove('animate-fade-in');
                        }
                    });

                    // Show empty state if no results
                    const visibleCards = Array.from(orderCards).filter(card => card.style.display !== 'none').length;
                    const emptyState = document.getElementById('empty-state');
                    const ordersContainer = document.getElementById('orders-container');

                    if (visibleCards === 0 && searchTerm) {
                        // Create dynamic empty state for search
                        ordersContainer.innerHTML = `
                            <div class="text-center py-16 animate-fade-in">
                                <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-search text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">No Orders Found</h3>
                                <p class="text-gray-600 mb-6">No orders match your search for "${searchTerm}"</p>
                                <button onclick="document.getElementById('search-input').value=''; document.getElementById('search-input').dispatchEvent(new Event('input'))" 
                                        class="bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition-colors">
                                    Clear Search
                                </button>
                            </div>
                        `;
                    } else if (visibleCards === 0 && !searchTerm) {
                        // Restore original empty state
                        location.reload();
                    }
                });
            }

            // Date filter functionality
            const dateFilter = document.getElementById('date-filter');
            if (dateFilter) {
                dateFilter.addEventListener('change', function() {
                    // You can implement date filtering here
                    console.log('Date filter changed to:', this.value);
                    // For now, just show a toast
                    showToasted(`Filtering orders for: ${this.options[this.selectedIndex].text}`, 'info');
                });
            }

            // Refresh functionality
            const refreshBtn = document.getElementById('refresh-btn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    const text = this.querySelector('span');

                    // Add refreshing state
                    this.classList.add('refreshing');
                    this.disabled = true;
                    icon.classList.add('animate-spin');

                    if (text) {
                        text.textContent = 'Refreshing...';
                    }

                    // Simulate refresh with proper timing
                    setTimeout(() => {
                        // Success state
                        this.classList.remove('refreshing');
                        this.classList.add('success');
                        icon.classList.remove('fa-sync-alt', 'animate-spin');
                        icon.classList.add('fa-check');

                        if (text) {
                            text.textContent = 'Refreshed!';
                        }

                        showToasted('Orders refreshed successfully!', 'success');

                        // Reset to normal state
                        setTimeout(() => {
                            this.classList.remove('success');
                            this.disabled = false;
                            icon.classList.remove('fa-check');
                            icon.classList.add('fa-sync-alt');

                            if (text) {
                                text.textContent = 'Refresh';
                            }
                        }, 1500);
                    }, 2000);
                });
            }

            // Pull to refresh functionality
            let startY = 0;
            let currentY = 0;
            let isPulling = false;
            const pullIndicator = document.getElementById('pull-indicator');
            const threshold = 100;

            document.addEventListener('touchstart', function(e) {
                if (window.scrollY === 0) {
                    startY = e.touches[0].clientY;
                    isPulling = true;
                }
            });

            document.addEventListener('touchmove', function(e) {
                if (!isPulling) return;

                currentY = e.touches[0].clientY;
                const pullDistance = currentY - startY;

                if (pullDistance > 0 && pullDistance < threshold * 2) {
                    e.preventDefault();
                    const opacity = Math.min(pullDistance / threshold, 1);
                    pullIndicator.style.transform = `translateY(${Math.min(pullDistance - 100, 0)}px)`;
                    pullIndicator.style.opacity = opacity;

                    if (pullDistance > threshold) {
                        pullIndicator.classList.add('visible');
                        pullIndicator.innerHTML = '<i class="fas fa-sync-alt mr-2"></i><span>Release to refresh</span>';
                    } else {
                        pullIndicator.classList.remove('visible');
                        pullIndicator.innerHTML = '<i class="fas fa-arrow-down mr-2"></i><span>Pull to refresh</span>';
                    }
                }
            });

            document.addEventListener('touchend', function(e) {
                if (!isPulling) return;

                const pullDistance = currentY - startY;

                if (pullDistance > threshold) {
                    // Trigger refresh
                    pullIndicator.innerHTML = '<i class="fas fa-sync-alt fa-spin mr-2"></i><span>Refreshing...</span>';

                    setTimeout(() => {
                        location.reload(); // Actual refresh
                    }, 1500);
                } else {
                    pullIndicator.style.transform = 'translateY(-100%)';
                    pullIndicator.style.opacity = '0';
                    pullIndicator.classList.remove('visible');
                }

                isPulling = false;
                startY = 0;
                currentY = 0;
            });

            // Stagger animation for order cards
            orderCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });

        // Action button functions
        window.reorderItems = async function(orderNumber) {
            try {
                showToasted('Adding items to cart...', 'info');

                // Simulate API call to reorder
                setTimeout(() => {
                    showToasted('Items added to cart successfully!', 'success');
                    updateCartCount();
                }, 1500);
            } catch (error) {
                showToasted('Failed to reorder items', 'error');
            }
        };

        window.trackOrder = function(orderNumber) {
            showToasted(`Tracking order ${orderNumber}...`, 'info');
            // Implement order tracking
        };

        window.cancelOrder = async function(orderNumber) {
            if (confirm('Are you sure you want to cancel this order?')) {
                try {
                    showToasted('Cancelling order...', 'info');

                    // Simulate API call
                    setTimeout(() => {
                        showToasted('Order cancelled successfully', 'success');
                        location.reload();
                    }, 1500);
                } catch (error) {
                    showToasted('Failed to cancel order', 'error');
                }
            }
        };

        window.rateOrder = function(orderNumber) {
            showToasted('Opening rating dialog...', 'info');
            // Implement rating functionality
        };

        window.contactSupport = function(orderNumber) {
            showToasted('Connecting to support...', 'info');
            // Implement support contact
        };

        window.retryPayment = function(orderNumber) {
            showToasted('Redirecting to payment...', 'info');
            setTimeout(() => {
                window.location.href = `checkout.php?retry=${orderNumber}`;
            }, 1000);
        };
    </script>
</body>

</html>