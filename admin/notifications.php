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
                <div id="notificationsList"></div>
                <div id="notificationsEmpty" class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 text-center text-gray-500 hidden">
                    <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                    <div class="font-semibold mb-1">No notifications found.</div>
                    <div class="text-sm">You're all caught up!</div>
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
    <script>
        // Fetch and render notifications dynamically
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.flex.items-center.space-x-4 > button');
            const notificationsList = document.getElementById('notificationsList');
            const notificationsEmpty = document.getElementById('notificationsEmpty');
            let notifications = [];

            function renderNotifications(filterType = 'all') {
                notificationsList.innerHTML = '';
                let filtered = (filterType === 'all') ? notifications : notifications.filter(n => n.type === filterType);
                if (filtered.length === 0) {
                    notificationsList.style.display = 'none';
                    notificationsEmpty.style.display = '';
                } else {
                    notificationsList.style.display = '';
                    notificationsEmpty.style.display = 'none';
                    filtered.forEach(notif => {
                        const card = document.createElement('div');
                        card.className = `bg-white rounded-lg shadow-sm p-6 border border-gray-200 ${notif.border}`;
                        card.setAttribute('data-type', notif.type);
                        card.innerHTML = `
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="${notif.bg} p-2 rounded-lg">
                                    <i data-lucide="${notif.icon}" class="w-5 h-5 text-${notif.color}-600"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">${notif.title}</h3>
                                    <p class="text-gray-600 mt-1">${notif.message}</p>
                                    <p class="text-sm text-gray-500 mt-2">${notif.time}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                ${notif.dot ? `<span class="w-2 h-2 ${notif.dot} rounded-full"></span>` : ''}
                            </div>
                        </div>
                    `;
                        notificationsList.appendChild(card);
                    });
                    if (window.lucide) lucide.createIcons();
                }
            }

            // Fetch notifications from API
            function fetchAndRenderNotifications() {
                fetch('api/fetch-notifications.php', { cache: 'no-store' })
                    .then(res => res.json())
                    .then(data => {
                        notifications = Array.isArray(data) ? data : [];
                        renderNotifications('all');
                    });
            }
            fetchAndRenderNotifications();

            // Optionally, re-render on filter change to always use latest notifications
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
                        renderNotifications('all');
                    } else if (type === 'orders') {
                        renderNotifications('order');
                    } else if (type === 'system') {
                        renderNotifications('system');
                    } else if (type === 'users') {
                        renderNotifications('user');
                    }
                });
            });

            // Filter logic
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
                        renderNotifications('all');
                    } else if (type === 'orders') {
                        renderNotifications('order');
                    } else if (type === 'system') {
                        renderNotifications('system');
                    } else if (type === 'users') {
                        renderNotifications('user');
                    }
                });
            });
        });
    </script>
</body>

</html>