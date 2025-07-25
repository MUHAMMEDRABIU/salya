<?php
require __DIR__ . '/initialize.php';
require_once 'util/util.php';

    $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $cartCount = array_sum(array_column($cart_items, 'quantity'));

// Mock data for orders - replace with your actual data source
$orders = [
    [
        'id' => 'ORD-001',
        'date' => '2024-01-15',
        'status' => 'delivered',
        'total' => 15500,
        'items_count' => 3,
        'items' => [
            ['name' => 'Frozen Chicken Wings', 'quantity' => 2, 'price' => 5000],
            ['name' => 'Fish Fillets', 'quantity' => 1, 'price' => 3500],
            ['name' => 'Turkey Slices', 'quantity' => 1, 'price' => 7000]
        ]
    ],
    [
        'id' => 'ORD-002',
        'date' => '2024-01-14',
        'status' => 'processing',
        'total' => 8500,
        'items_count' => 2,
        'items' => [
            ['name' => 'Frozen Prawns', 'quantity' => 1, 'price' => 6000],
            ['name' => 'Ice Cream', 'quantity' => 1, 'price' => 2500]
        ]
    ],
    [
        'id' => 'ORD-003',
        'date' => '2024-01-13',
        'status' => 'pending',
        'total' => 12000,
        'items_count' => 4,
        'items' => [
            ['name' => 'Frozen Beef', 'quantity' => 1, 'price' => 8000],
            ['name' => 'Chicken Breast', 'quantity' => 2, 'price' => 2000]
        ]
    ],
    [
        'id' => 'ORD-004',
        'date' => '2024-01-12',
        'status' => 'cancelled',
        'total' => 5500,
        'items_count' => 1,
        'items' => [
            ['name' => 'Frozen Vegetables', 'quantity' => 3, 'price' => 1833]
        ]
    ]
];

require_once 'partials/headers.php';
?>
</head>

<body class="bg-gray font-dm pb-24 overflow-x-hidden">
    <!-- Pull to Refresh Indicator -->
    <div id="pull-indicator" class="pull-indicator fixed top-0 left-0 right-0 bg-accent text-white text-center py-3 z-50">
        <i class="fas fa-sync-alt mr-2"></i>
        <span>Release to refresh</span>
    </div>

    <!-- Main Content -->
    <main class="px-4 pt-6 space-y-6 animate-fade-in">
        <!-- Page Header -->
        <?php include 'partials/top-nav.php'; ?>

        <!-- Order Stats -->
        <div class="grid grid-cols-2 gap-4 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="bg-white rounded-2xl p-4 floating-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Orders</p>
                        <p class="text-2xl font-bold text-dark"><?php echo count($orders); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-accent text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-4 floating-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Spent</p>
                        <p class="text-2xl font-bold text-dark">₦<?php echo number_format(array_sum(array_column($orders, 'total'))); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-naira-sign text-green-600 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="overflow-x-auto hide-scrollbar animate-slide-up" style="animation-delay: 0.2s;">
            <div class="flex space-x-3 pb-2">
                <button class="filter-button active px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap bg-accent text-white shadow-lg hover:shadow-xl transform hover:scale-105" data-status="all">
                    All Orders
                </button>
                <button class="filter-button px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap bg-white text-gray-600 shadow-md hover:shadow-lg hover:bg-gray-50 transform hover:scale-105" data-status="delivered">
                    Delivered
                </button>
                <button class="filter-button px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap bg-white text-gray-600 shadow-md hover:shadow-lg hover:bg-gray-50 transform hover:scale-105" data-status="processing">
                    Processing
                </button>
                <button class="filter-button px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap bg-white text-gray-600 shadow-md hover:shadow-lg hover:bg-gray-50 transform hover:scale-105" data-status="pending">
                    Pending
                </button>
            </div>
        </div>

        <!-- Orders List -->
        <div id="orders-container" class="space-y-4 animate-slide-up" style="animation-delay: 0.3s;">
            <?php foreach ($orders as $index => $order):
                $statusInfo = getStatusInfo($order['status']);
            ?>
                <div class="order-card bg-white rounded-3xl shadow-lg overflow-hidden animate-scale-in"
                    data-status="<?php echo $order['status']; ?>"
                    style="animation-delay: <?php echo $index * 0.1; ?>s;">

                    <!-- Order Header -->
                    <div class="p-6 cursor-pointer" onclick="toggleOrderDetails('<?php echo $order['id']; ?>')">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-bold text-dark text-lg"><?php echo $order['id']; ?></h3>
                                <p class="text-gray-500 text-sm"><?php echo date('M d, Y', strtotime($order['date'])); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-accent">₦<?php echo number_format($order['total']); ?></p>
                                <p class="text-gray-500 text-sm"><?php echo $order['items_count']; ?> items</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?php echo $statusInfo['color'] . ' ' . $statusInfo['bg']; ?>">
                                <i class="<?php echo $statusInfo['icon']; ?> mr-2 text-xs"></i>
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                            <button class="expand-btn w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                                <i class="fas fa-chevron-down text-gray-600 text-sm transition-transform duration-300"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Order Details (Expandable) -->
                    <div id="details-<?php echo $order['id']; ?>" class="order-details">
                        <div class="px-6 pb-6 border-t border-gray-100">
                            <div class="pt-4 space-y-3">
                                <h4 class="font-semibold text-dark mb-3">Order Items</h4>
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="flex items-center justify-between py-2">
                                        <div class="flex-1">
                                            <p class="font-medium text-dark text-sm"><?php echo $item['name']; ?></p>
                                            <p class="text-gray-500 text-xs">Qty: <?php echo $item['quantity']; ?></p>
                                        </div>
                                        <span class="font-semibold text-accent">₦<?php echo number_format($item['price']); ?></span>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Action Buttons -->
                                <div class="flex space-x-3 mt-6">
                                    <?php if ($order['status'] === 'delivered'): ?>
                                        <button class="flex-1 bg-accent text-white py-3 rounded-2xl font-semibold hover:bg-orange-600 transition-colors">
                                            Reorder
                                        </button>
                                        <button class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-2xl font-semibold hover:bg-gray-200 transition-colors">
                                            Rate Order
                                        </button>
                                    <?php elseif ($order['status'] === 'processing'): ?>
                                        <button class="flex-1 bg-blue-500 text-white py-3 rounded-2xl font-semibold hover:bg-blue-600 transition-colors">
                                            Track Order
                                        </button>
                                        <button class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-2xl font-semibold hover:bg-gray-200 transition-colors">
                                            Contact Support
                                        </button>
                                    <?php elseif ($order['status'] === 'pending'): ?>
                                        <button class="flex-1 bg-red-500 text-white py-3 rounded-2xl font-semibold hover:bg-red-600 transition-colors">
                                            Cancel Order
                                        </button>
                                        <button class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-2xl font-semibold hover:bg-gray-200 transition-colors">
                                            Modify Order
                                        </button>
                                    <?php else: ?>
                                        <button class="flex-1 bg-accent text-white py-3 rounded-2xl font-semibold hover:bg-orange-600 transition-colors">
                                            Reorder
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty State (Hidden by default) -->
        <div id="empty-state" class="hidden text-center py-12 animate-fade-in">
            <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-bag text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-dark mb-2">No Orders Found</h3>
            <p class="text-gray-500 mb-6">You haven't placed any orders yet</p>
            <a href="index.php" class="bg-accent text-white px-8 py-3 rounded-2xl font-semibold hover:bg-orange-600 transition-colors inline-block">
                Start Shopping
            </a>
        </div>
    </main>

    <!-- Bottom navigation include -->
    <?php include 'partials/bottom-nav.php'; ?>

    <script>
        // Premium mobile interactions with enhanced animations
        document.addEventListener('DOMContentLoaded', function() {
            // Order details toggle functionality
            window.toggleOrderDetails = function(orderId) {
                const detailsElement = document.getElementById(`details-${orderId}`);
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

            // Filter functionality
            const filterButtons = document.querySelectorAll('.filter-button');
            const orderCards = document.querySelectorAll('.order-card');
            const emptyState = document.getElementById('empty-state');
            const ordersContainer = document.getElementById('orders-container');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const status = this.dataset.status;

                    // Update active button
                    filterButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-accent', 'text-white');
                        btn.classList.add('bg-white', 'text-gray-600');
                    });

                    this.classList.add('active', 'bg-accent', 'text-white');
                    this.classList.remove('bg-white', 'text-gray-600');

                    // Add bounce animation
                    this.classList.add('animate-bounce-gentle');
                    setTimeout(() => {
                        this.classList.remove('animate-bounce-gentle');
                    }, 600);

                    // Filter orders
                    let visibleCount = 0;
                    orderCards.forEach((card, index) => {
                        const cardStatus = card.dataset.status;

                        if (status === 'all' || cardStatus === status) {
                            card.style.display = 'block';
                            card.style.animationDelay = `${index * 0.1}s`;
                            card.classList.add('animate-scale-in');
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // Show/hide empty state
                    if (visibleCount === 0) {
                        ordersContainer.style.display = 'none';
                        emptyState.classList.remove('hidden');
                    } else {
                        ordersContainer.style.display = 'block';
                        emptyState.classList.add('hidden');
                    }
                });
            });

            // Refresh functionality
            const refreshBtn = document.getElementById('refresh-btn');
            refreshBtn.addEventListener('click', function() {
                // Add rotation animation
                const icon = this.querySelector('i');
                icon.style.animation = 'spin 1s linear infinite';

                // Simulate refresh
                setTimeout(() => {
                    icon.style.animation = '';

                    // Add success feedback
                    this.classList.add('bg-green-500');
                    icon.classList.remove('fa-sync-alt');
                    icon.classList.add('fa-check');
                    icon.style.color = 'white';

                    setTimeout(() => {
                        this.classList.remove('bg-green-500');
                        icon.classList.remove('fa-check');
                        icon.classList.add('fa-sync-alt');
                        icon.style.color = '';
                    }, 1000);
                }, 2000);
            });

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
                        pullIndicator.style.transform = 'translateY(-100%)';
                        pullIndicator.style.opacity = '0';
                        pullIndicator.classList.remove('visible');

                        // Reset after animation
                        setTimeout(() => {
                            pullIndicator.innerHTML = '<i class="fas fa-arrow-down mr-2"></i><span>Pull to refresh</span>';
                        }, 300);
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

            // Enhanced bottom navigation with premium interactions
            const navItems = document.querySelectorAll('.nav-item');

            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Add ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(249, 115, 22, 0.3)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.left = '50%';
                    ripple.style.top = '50%';
                    ripple.style.width = '60px';
                    ripple.style.height = '60px';
                    ripple.style.marginLeft = '-30px';
                    ripple.style.marginTop = '-30px';

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });

                // Add hover effects for desktop
                item.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('nav-item-active')) {
                        const icon = this.querySelector('.nav-icon');
                        icon.style.transform = 'translateY(-2px) scale(1.05)';
                    }
                });

                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('nav-item-active')) {
                        const icon = this.querySelector('.nav-icon');
                        icon.style.transform = 'translateY(0) scale(1)';
                    }
                });
            });

            // Stagger animation for order cards
            const orderCards2 = document.querySelectorAll('.order-card');
            orderCards2.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Add CSS for ripple and spin animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>

</html>