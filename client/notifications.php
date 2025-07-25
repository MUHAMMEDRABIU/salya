<?php
require_once 'initialize.php';
require_once 'util/util.php';
require_once 'partials/headers.php';

$user_id = $_SESSION['user_id'];
$notifications = getAllNotifications($pdo, $user_id);

// Count unread notifications
$unread_count = count(array_filter($notifications, function ($n) {
    return empty($n['read']) || $n['read'] == 0;
}));

?>

<body class="bg-custom-gray min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <button id="backBtn" class="p-3 hover:bg-white rounded-xl transition-all duration-300 floating-card">
                <svg class="w-6 h-6 text-custom-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <h1 class="text-2xl font-bold text-custom-dark">Notifications</h1>

            <button id="markAllReadBtn" class="text-custom-accent font-semibold hover:opacity-80 transition-opacity duration-200">
                Mark all read
            </button>
        </div>

        <!-- Notification Categories -->
        <div class="flex space-x-4 mb-8 overflow-x-auto pb-2">
            <button class="notification-filter active bg-custom-accent text-white px-6 py-3 rounded-2xl font-semibold whitespace-nowrap transition-all duration-300" data-filter="all">
                All
            </button>
            <button class="notification-filter bg-white text-custom-dark px-6 py-3 rounded-2xl font-semibold whitespace-nowrap hover:bg-custom-accent hover:text-white transition-all duration-300" data-filter="orders">
                Orders
            </button>
            <button class="notification-filter bg-white text-custom-dark px-6 py-3 rounded-2xl font-semibold whitespace-nowrap hover:bg-custom-accent hover:text-white transition-all duration-300" data-filter="promotions">
                Promotions
            </button>
            <button class="notification-filter bg-white text-custom-dark px-6 py-3 rounded-2xl font-semibold whitespace-nowrap hover:bg-custom-accent hover:text-white transition-all duration-300" data-filter="updates">
                Updates
            </button>
        </div>
        
        <!-- Notifications List -->
        <div class="space-y-4" id="notificationsList">
            <?php foreach ($notifications as $n): ?>
                <div class="notification-item bg-white rounded-2xl p-6 floating-card animate-fade-in"
                    data-category="<?php echo htmlspecialchars($n['type']); ?>"
                    data-read="<?php echo $n['read'] ? 'true' : 'false'; ?>">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 <?php echo htmlspecialchars($n['color'] ?? 'bg-gray-100'); ?>">
                            <i class="<?php echo htmlspecialchars($n['icon'] ?? 'fas fa-bell'); ?> text-xl <?php echo htmlspecialchars($n['color'] ?? 'text-gray-600'); ?>"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-bold text-custom-dark"><?php echo htmlspecialchars($n['title']); ?></h3>
                                <?php if (empty($n['read'])): ?>
                                    <div class="w-3 h-3 bg-custom-accent rounded-full flex-shrink-0"></div>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-600 mb-3"><?php echo htmlspecialchars($n['message']); ?></p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500"><?php echo date('M d, Y H:i', strtotime($n['time'])); ?></span>
                                <?php if (!empty($n['action_label'])): ?>
                                    <button class="text-sm font-semibold text-custom-accent hover:opacity-80 transition-opacity">
                                        <?php echo htmlspecialchars($n['action_label']); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Empty State (default markup, hidden if notifications exist) -->
        <div id="emptyState" class="text-center py-16<?php echo empty($notifications) ? '' : ' hidden'; ?>">
            <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 01-7.5-7.5H12a7.5 7.5 0 017.5 7.5v5z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-custom-dark mb-2">No Notifications</h3>
            <p class="text-gray-500">You're all caught up! Check back later for new updates.</p>
        </div>

    </div>
    <!-- Bottom navigation include -->
    <?php include 'partials/bottom-nav.php'; ?>
    <script>
        // Notification management
        let notifications = [];
        let currentFilter = 'all';

        // DOM elements
        const notificationsList = document.getElementById('notificationsList');
        const emptyState = document.getElementById('emptyState');
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        const backBtn = document.getElementById('backBtn');
        const filterButtons = document.querySelectorAll('.notification-filter');

        // Initialize notifications from DOM
        function initializeNotifications() {
            const notificationItems = document.querySelectorAll('.notification-item');
            notifications = Array.from(notificationItems).map((item, index) => ({
                id: index,
                element: item,
                category: item.dataset.category,
                isRead: item.dataset.read === 'true'
            }));

            updateNotificationDisplay();
        }

        // Filter notifications
        function filterNotifications(category) {
            currentFilter = category;

            // Update filter buttons
            filterButtons.forEach(btn => {
                if (btn.dataset.filter === category) {
                    btn.classList.add('active', 'bg-custom-accent', 'text-white');
                    btn.classList.remove('bg-white', 'text-custom-dark');
                } else {
                    btn.classList.remove('active', 'bg-custom-accent', 'text-white');
                    btn.classList.add('bg-white', 'text-custom-dark');
                }
            });

            // Show/hide notifications
            notifications.forEach(notification => {
                if (category === 'all' || notification.category === category) {
                    notification.element.style.display = 'block';
                    // Add stagger animation
                    setTimeout(() => {
                        notification.element.classList.add('animate-fade-in');
                    }, notifications.indexOf(notification) * 50);
                } else {
                    notification.element.style.display = 'none';
                    notification.element.classList.remove('animate-fade-in');
                }
            });

            updateEmptyState();
        }

        // Mark notification as read
        function markAsRead(notificationElement) {
            const unreadIndicator = notificationElement.querySelector('.w-3.h-3.bg-custom-accent');
            const title = notificationElement.querySelector('h3');

            if (unreadIndicator) {
                unreadIndicator.remove();
                title.classList.remove('text-custom-dark', 'font-bold');
                title.classList.add('text-gray-600', 'font-semibold');

                // Update data attribute
                notificationElement.dataset.read = 'true';

                // Update notifications array
                const notification = notifications.find(n => n.element === notificationElement);
                if (notification) {
                    notification.isRead = true;
                }
            }
        }

        // Mark all notifications as read
        function markAllAsRead() {
            notifications.forEach(notification => {
                if (!notification.isRead) {
                    markAsRead(notification.element);
                }
            });

            // Add feedback animation
            markAllReadBtn.style.transform = 'scale(0.95)';
            setTimeout(() => {
                markAllReadBtn.style.transform = 'scale(1)';
            }, 150);
        }

        // Update empty state visibility
        function updateEmptyState() {
            const visibleNotifications = notifications.filter(n =>
                (currentFilter === 'all' || n.category === currentFilter) &&
                n.element.style.display !== 'none'
            );

            if (visibleNotifications.length === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        // Update notification display
        function updateNotificationDisplay() {
            const unreadCount = notifications.filter(n => !n.isRead).length;

            if (unreadCount === 0) {
                markAllReadBtn.style.opacity = '0.5';
                markAllReadBtn.style.pointerEvents = 'none';
            } else {
                markAllReadBtn.style.opacity = '1';
                markAllReadBtn.style.pointerEvents = 'auto';
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', () => {
            initializeNotifications();

            // Filter button listeners
            filterButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterNotifications(btn.dataset.filter);
                });
            });

            // Mark all read button
            markAllReadBtn.addEventListener('click', markAllAsRead);

            // Back button
            backBtn.addEventListener('click', () => {
                // In a real app, this would navigate back
                window.history.back();
            });

            // Click on notification to mark as read
            notifications.forEach(notification => {
                notification.element.addEventListener('click', () => {
                    markAsRead(notification.element);
                    updateNotificationDisplay();
                });
            });

            // Add hover effects to notification items
            notifications.forEach(notification => {
                notification.element.addEventListener('mouseenter', () => {
                    notification.element.style.transform = 'translateY(-2px)';
                });

                notification.element.addEventListener('mouseleave', () => {
                    notification.element.style.transform = 'translateY(0)';
                });
            });
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.history.back();
            }

            // Number keys for quick filter
            const filterMap = {
                '1': 'all',
                '2': 'orders',
                '3': 'promotions',
                '4': 'updates'
            };

            if (filterMap[e.key]) {
                filterNotifications(filterMap[e.key]);
            }
        });

        // Auto-refresh notifications (simulate real-time updates)
        setInterval(() => {
            // In a real app, this would fetch new notifications from the server
            console.log('Checking for new notifications...');
        }, 30000);

        // Add notification interaction animations
        function addNotificationInteractions() {
            const actionButtons = document.querySelectorAll('.notification-item button');

            actionButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.stopPropagation(); // Prevent marking as read when clicking action buttons

                    // Add click animation
                    button.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        button.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        }

        // Initialize interactions
        document.addEventListener('DOMContentLoaded', addNotificationInteractions);
    </script>
</body>

</html>