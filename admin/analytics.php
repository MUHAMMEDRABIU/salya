<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/util/utilities.php';
require __DIR__ . '/../config/constants.php';

$analytics = getAnalyticsData($pdo);

require __DIR__ . '/partials/headers.php';
?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>
        <!-- Analytics Content -->
        <main class="p-6">
            <!-- Filters Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <h3 class="text-lg font-semibold text-gray-800">Analytics Overview</h3>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-600">Date Range:</label>
                            <select id="dateRange" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="7">Last 7 days</option>
                                <option value="30" selected>Last 30 days</option>
                                <option value="90">Last 90 days</option>
                                <option value="365">Last year</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-600">Category:</label>
                            <select id="categoryFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="all">All Categories</option>
                                <option value="chicken">Chicken</option>
                                <option value="seafood">Seafood</option>
                                <option value="beef">Beef</option>
                                <option value="vegetables">Vegetables</option>
                            </select>
                        </div>
                        <button class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors flex items-center">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                            Export Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p id="revenueCounter" class="text-2xl font-bold text-gray-900" data-target="<?= $analytics['total_revenue'] ?>"><?php echo CURRENCY_SYMBOL; ?>0.00</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($analytics['total_orders'] ?? 0) ?></p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <i data-lucide="shopping-cart" class="w-6 h-6 text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($analytics['active_users'] ?? 0) ?></p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Avg Order Value</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo CURRENCY_SYMBOL; ?><?= number_format($analytics['avg_order_value'] ?? 0, 2) ?></p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg">
                            <i data-lucide="credit-card" class="w-6 h-6 text-orange-600"></i>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Sales Overview Chart -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Sales Overview</h3>
                        <div class="flex items-center space-x-2">
                            <button class="text-sm text-gray-500 hover:text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-50">Daily</button>
                            <button class="text-sm text-white bg-orange-500 px-3 py-1 rounded-lg">Weekly</button>
                            <button class="text-sm text-gray-500 hover:text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-50">Monthly</button>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <!-- Orders by Category Chart -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Orders by Category</h3>
                        <button class="text-sm text-gray-500 hover:text-gray-700">
                            <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <div class="h-80">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Revenue Trends Chart -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Revenue Trends</h3>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Revenue</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Orders</span>
                        </div>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Analytics Tables -->
            <!-- Top Selling Products -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Top Selling Products</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php foreach ($analytics['top_products'] as $product): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <?php
                                        // Generate product image URL with fallback
                                        $productImage = !empty($product['image']) && $product['image'] !== DEFAULT_PRODUCT_IMAGE
                                            ? PRODUCT_IMAGE_URL . htmlspecialchars($product['image'])
                                            : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE;
                                        ?>
                                        <img src="<?php echo $productImage; ?>" alt="Product" class="w-10 h-10 rounded-lg object-cover">
                                        <div>
                                            <p class="font-medium text-gray-900"><?= htmlspecialchars($product['name']) ?></p>
                                            <p class="text-sm text-gray-500"><?= $product['total_sold'] ?> sold</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900"><?php echo CURRENCY_SYMBOL; ?><?= number_format($product['total_amount'] ?? 0, 2) ?></p>
                                        <p class="text-sm text-green-600">+<?= rand(1, 25) ?>%</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Peak Order Times -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Peak Order Times</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php foreach ($analytics['peak_times'] as $slot): ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                            <i data-lucide="clock" class="w-5 h-5 text-orange-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900"><?= $slot['label'] ?></p>
                                            <p class="text-sm text-gray-500"><?= $slot['tag'] ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900"><?= $slot['count'] ?> orders</p>
                                        <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="bg-orange-500 h-2 rounded-full" style="width: <?= $slot['percentage'] ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <script src="js/script.js"></script>
    <script src="js/analytics.js"></script>
    <script>
        function animateCounter(id, targetValue, prefix = "<?php echo CURRENCY_SYMBOL; ?>", duration = 2000) {
            const el = document.getElementById(id);
            const start = 0;
            const startTime = performance.now();

            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = targetValue * progress;
                el.textContent = prefix + current.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            }

            requestAnimationFrame(update);
        }

        window.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('revenueCounter');
            const target = parseFloat(el.getAttribute('data-target')) || 0;
            animateCounter('revenueCounter', target, '<?php echo CURRENCY_SYMBOL; ?>');

            // Set global currency symbol for charts
            window.CURRENCY_SYMBOL = '<?php echo CURRENCY_SYMBOL; ?>';
        });
    </script>

</body>

</html>