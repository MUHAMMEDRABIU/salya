<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/util/utilities.php';
require __DIR__ . '/../config/constants.php';

// Fetch user data from the database
$users = getUsersData($pdo);
// fetch user stats
$userStats = getAllUsersStats($pdo);

require __DIR__ . '/partials/headers.php';
?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>
        <!-- Users Content -->
        <main class="p-6">
            <!-- Users Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($userStats['total_users']) ?></p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Users</p>
                            <p class="text-2xl font-bold text-green-600"><?= htmlspecialchars($userStats['active_users']) ?></p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <i data-lucide="user-check" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">New This Month</p>
                            <p class="text-2xl font-bold text-orange-600"><?= htmlspecialchars($userStats['new_users_this_month']) ?></p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg">
                            <i data-lucide="user-plus" class="w-6 h-6 text-orange-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Loyal Users</p>
                            <p class="text-2xl font-bold text-purple-600"><?= htmlspecialchars($userStats['loyal_users']) ?></p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <i data-lucide="crown" class="w-6 h-6 text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">User Management</h3>
                        <div class="flex items-center space-x-4">
                            <select class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option>All Users</option>
                                <option>Active</option>
                                <option>Inactive</option>
                                <option>Premium</option>
                            </select>
                            <!-- <button class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                                <i data-lucide="user-plus" class="w-4 h-4 mr-2 inline"></i>
                                Add User
                            </button> -->
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <?php
                                            // Generate user avatar URL with fallback
                                            $userAvatarFile = !empty($user['avatar']) && $user['avatar'] !== DEFAULT_USER_AVATAR
                                                ? $user['avatar']
                                                : DEFAULT_USER_AVATAR;
                                            $userAvatarUrl = USER_AVATAR_URL . htmlspecialchars($userAvatarFile);
                                            ?>
                                            <img src="<?php echo $userAvatarUrl; ?>" alt="User" class="w-10 h-10 rounded-full mr-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                                                <div class="text-sm text-gray-500 capitalize"><?= htmlspecialchars($user['role']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= (int) $user['order_count'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo CURRENCY_SYMBOL; ?><?= number_format((float) $user['total_spent'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $user['status'] === 'Active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                            <?= htmlspecialchars($user['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="view-user.php?id=<?= (int) $user['id'] ?>" class="text-xs bg-gray-100 px-3 rounded py-1 text-orange-600 hover:text-orange-900 mr-3">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing 1 to 10 of 2,847 users
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</button>
                            <button class="px-3 py-1 text-sm text-white bg-orange-500 border border-orange-500 rounded">1</button>
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">2</button>
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">3</button>
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</button>
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