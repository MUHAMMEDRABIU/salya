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
                <?php
                // Fetch notifications from DB
                require_once __DIR__ . '/../config/database.php';
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
                $stmt = $pdo->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 30");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Calculate time ago
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
                ?>
                <?php if (empty($notifications)): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 text-center text-gray-500">
                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="font-semibold mb-1">No notifications found.</div>
                        <div class="text-sm">You're all caught up!</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 <?php echo $notif['border']; ?>" data-type="<?php echo $notif['type']; ?>">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4">
                                    <div class="<?php echo $notif['bg']; ?> p-2 rounded-lg">
                                        <i data-lucide="<?php echo $notif['icon']; ?>" class="w-5 h-5 text-<?php echo $notif['color']; ?>-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900"><?php echo $notif['title']; ?></h3>
                                        <p class="text-gray-600 mt-1"><?php echo $notif['message']; ?></p>
                                        <p class="text-sm text-gray-500 mt-2"><?php echo $notif['time']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <?php if (!empty($notif['dot'])): ?>
                                        <span class="w-2 h-2 <?php echo $notif['dot']; ?> rounded-full"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
    <script>
        // Notification filtering
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.flex.items-center.space-x-4 > button');
            const notificationCards = document.querySelectorAll('.space-y-4 > div.bg-white[data-type]');
            const emptyState = document.querySelector('.space-y-4 > .text-center');

            function showAll() {
                let anyVisible = false;
                notificationCards.forEach(card => {
                    card.style.display = '';
                    anyVisible = true;
                });
                if (emptyState) emptyState.style.display = 'none';
            }

            function filterByType(type) {
                let anyVisible = false;
                notificationCards.forEach(card => {
                    if (card.getAttribute('data-type') === type) {
                        card.style.display = '';
                        anyVisible = true;
                    } else {
                        card.style.display = 'none';
                    }
                });
                if (emptyState) emptyState.style.display = anyVisible ? 'none' : '';
                if (!anyVisible && emptyState) emptyState.style.display = '';
            }

            filterButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterButtons.forEach(b => {
                        b.classList.remove('bg-orange-500', 'text-white');
                        b.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-300', 'hover:bg-gray-50');
                    });
                    btn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-300', 'hover:bg-gray-50');
                    btn.classList.add('bg-orange-500', 'text-white');
                    let type = btn.textContent.trim().toLowerCase();
                    if (type === 'all') {
                        showAll();
                    } else if (type === 'orders') {
                        filterByType('order');
                    } else if (type === 'system') {
                        filterByType('system');
                    } else if (type === 'users') {
                        filterByType('user');
                    }
                });
            });
        });
    </script>
</body>

</html>