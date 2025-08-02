<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/util/utilities.php';
require __DIR__ . '/partials/headers.php';

// Fetch dashboard stats
$stats = getDashboardStats($pdo);
$recentOrders = getRecentOrders($pdo, 5);
$topProducts = getTopProducts($pdo, 3);

?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>
        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo number_format($stats['total_orders'] ?? 0); ?></p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <i data-lucide="shopping-cart" class="w-6 h-6 text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Revenue</p>
                            <p class="text-3xl font-bold text-gray-900">₦<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Users</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo number_format($stats['active_users'] ?? 0); ?></p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg">
                            <i data-lucide="users" class="w-6 h-6 text-orange-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Products</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo number_format($stats['total_products'] ?? 0); ?></p>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <i data-lucide="star" class="w-6 h-6 text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Sales Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Sales Overview</h3>
                    <div class="relative h-64">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <!-- Orders Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Orders by Category</h3>
                    <div class="relative h-64">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders & Top Products -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
                        <a href="orders.php" class="text-orange-600 hover:text-orange-700 font-medium text-sm">View all orders →</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php if (empty($recentOrders)): ?>
                                <div class="text-center text-gray-500 py-8">No recent orders found.</div>
                            <?php else: ?>
                                <?php
                                // Fetch user names for recent orders without JOINs
                                $userIds = array_unique(array_column($recentOrders, 'user_id'));
                                $userNames = [];
                                if ($userIds) {
                                    // Fix: Ensure array is numerically indexed
                                    $userIds = array_values($userIds);
                                    $in  = str_repeat('?,', count($userIds) - 1) . '?';
                                    $userStmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE id IN ($in)");
                                    $userStmt->execute($userIds);
                                    foreach ($userStmt->fetchAll(PDO::FETCH_ASSOC) as $user) {
                                        $userNames[$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
                                    }
                                }
                                ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                                <i data-lucide="package" class="w-5 h-5 text-orange-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">#<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></p>
                                                <p class="text-sm text-gray-500">
                                                    <?php
                                                    $uid = $order['user_id'];
                                                    echo isset($userNames[$uid]) ? htmlspecialchars($userNames[$uid]) : 'Unknown User';
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">₦<?php echo number_format($order['total'] ?? 0, 2); ?></p>
                                            <?php
                                            $status = strtolower($order['status']);
                                            $statusColors = [
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'processing' => 'bg-yellow-100 text-yellow-800',
                                                'shipped' => 'bg-blue-100 text-blue-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $color; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Top Products</h3>
                        <a href="products.php" class="text-orange-600 hover:text-orange-700 font-medium text-sm">View all products →</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php if (empty($topProducts)): ?>
                                <div class="text-center text-gray-500 py-8">No top products found.</div>
                            <?php else: ?>
                                <?php
                                $maxOrders = max(array_column($topProducts, 'orders_count'));
                                ?>
                                <?php foreach ($topProducts as $product): ?>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <img src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-12 h-12 rounded-lg object-cover">
                                            <div>
                                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo (int)$product['orders_count']; ?> orders</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">₦<?php echo number_format($product['total_revenue'] ?? 0, 2); ?></p>
                                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                                <?php
                                                $width = 0;
                                                if ($maxOrders > 0) {
                                                    $width = min(100, ($product['orders_count'] / $maxOrders) * 100);
                                                }
                                                ?>
                                                <div class="bg-orange-500 h-2 rounded-full" style="width: <?php echo $width; ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <script src="js/script.js"></script>
    <script src="js/custom-charts.js"></script>
    <script src="js/analytics.js"></script>
    <script>
        // Initialize Charts
        document.addEventListener("DOMContentLoaded", function() {
            // Sales Chart
            const salesCtx = document.getElementById("salesChart").getContext("2d");
            new Chart(salesCtx, {
                type: "line",
                data: {
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                    datasets: [{
                        label: "Sales",
                        data: [12000, 19000, 15000, 25000, 22000, 30000],
                        borderColor: "#F97316",
                        backgroundColor: "rgba(249, 115, 22, 0.1)",
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                    }, ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(0, 0, 0, 0.1)",
                            },
                            ticks: {
                                callback: function(value) {
                                    return "$" + value.toLocaleString();
                                },
                            },
                        },
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                    },
                },
            });

            // Orders Chart
            const ordersCtx = document.getElementById("ordersChart").getContext("2d");
            new Chart(ordersCtx, {
                type: "doughnut",
                data: {
                    labels: ["Chicken", "Fish", "Turkey"],
                    datasets: [{
                        data: [45, 35, 20],
                        backgroundColor: ["#F97316", "#3B82F6", "#EF4444"],
                        borderWidth: 0,
                    }, ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                            },
                        },
                    },
                },
            });
        });
    </script>
</body>

</html>