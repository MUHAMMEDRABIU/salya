<?php require __DIR__ . '/partials/headers.php'; ?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>
        <!-- Notifications Content -->
        <main class="p-6">
            <!-- Notification Filters -->
            <div class="mb-6">
                <div class="flex items-center space-x-4">
                    <button class="bg-orange-500 text-white px-4 py-2 rounded-lg">All</button>
                    <button class="bg-white text-gray-700 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Orders</button>
                    <button class="bg-white text-gray-700 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">System</button>
                    <button class="bg-white text-gray-700 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Users</button>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="space-y-4">
                <!-- New Order Notification -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 border-l-4 border-l-orange-500">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="bg-orange-100 p-2 rounded-lg">
                                <i data-lucide="shopping-cart" class="w-5 h-5 text-orange-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">New Order Received</h3>
                                <p class="text-gray-600 mt-1">Order #ORD-1234 from John Smith for $45.99</p>
                                <p class="text-sm text-gray-500 mt-2">2 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 border-l-4 border-l-red-500">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="bg-red-100 p-2 rounded-lg">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">Low Stock Alert</h3>
                                <p class="text-gray-600 mt-1">Turkey Wings is running low (only 5 items left)</p>
                                <p class="text-sm text-gray-500 mt-2">15 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- New User Registration -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 border-l-4 border-l-blue-500">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <i data-lucide="user-plus" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">New User Registration</h3>
                                <p class="text-gray-600 mt-1">Sarah Johnson just signed up for an account</p>
                                <p class="text-sm text-gray-500 mt-2">1 hour ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- System Update -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="bg-gray-100 p-2 rounded-lg">
                                <i data-lucide="settings" class="w-5 h-5 text-gray-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">System Update Complete</h3>
                                <p class="text-gray-600 mt-1">Dashboard has been updated to version 2.1.0</p>
                                <p class="text-sm text-gray-500 mt-2">3 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-gray-400 hover:text-gray-600">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Daily Sales Report -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i data-lucide="bar-chart-3" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">Daily Sales Report</h3>
                                <p class="text-gray-600 mt-1">Today's sales: $1,250.75 (23 orders)</p>
                                <p class="text-sm text-gray-500 mt-2">Yesterday, 6:00 PM</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-gray-400 hover:text-gray-600">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Processed -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i data-lucide="credit-card" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">Payment Processed</h3>
                                <p class="text-gray-600 mt-1">Payment of $89.50 received from Mike Johnson</p>
                                <p class="text-sm text-gray-500 mt-2">2 days ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-gray-400 hover:text-gray-600">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="mt-8 text-center">
                <button class="bg-white text-gray-700 px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                    Load More Notifications
                </button>
            </div>
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <script src="js/script.js"></script>
</body>

</html>