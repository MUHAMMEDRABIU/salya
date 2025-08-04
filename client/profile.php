<?php
require_once __DIR__ . '/initialize.php';
require_once __DIR__ . '/util/util.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user profile and statistics
$user = getUserProfile($pdo, $user_id);
$userStats = getUserStatistics($pdo, $user_id);

// Get cart count for logged in users
$cartCount = 0;
try {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cartCount = (int)($result['total_items'] ?? 0);
} catch (Exception $e) {
    error_log("Error getting cart count in profile: " . $e->getMessage());
    $cartCount = 0;
}

// Get user preferences
function getUserPreferences($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                push_notifications,
                email_updates,
                language,
                theme
            FROM user_preferences 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $preferences = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set defaults if no preferences found
        if (!$preferences) {
            return [
                'push_notifications' => 1,
                'email_updates' => 0,
                'language' => 'en',
                'theme' => 'light'
            ];
        }

        return $preferences;
    } catch (Exception $e) {
        error_log("Error getting user preferences: " . $e->getMessage());
        return [
            'push_notifications' => 1,
            'email_updates' => 0,
            'language' => 'en',
            'theme' => 'light'
        ];
    }
}

// Get user addresses
function getUserAddresses($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                id,
                address_name,
                full_address,
                city,
                state,
                postal_code,
                is_default,
                created_at
            FROM user_addresses 
            WHERE user_id = ? 
            ORDER BY is_default DESC, created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting user addresses: " . $e->getMessage());
        return [];
    }
}

$preferences = getUserPreferences($pdo, $user_id);
$addresses = getUserAddresses($pdo, $user_id);

require_once 'partials/headers.php';
?>

<body class="bg-gray-50 font-dm pb-24 overflow-x-hidden">
    <!-- Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-orange-500 opacity-5 rounded-full filter blur-3xl animate-float"></div>
        <div class="absolute top-1/2 -left-32 w-64 h-64 bg-purple-500 opacity-5 rounded-full filter blur-3xl animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 right-1/4 w-48 h-48 bg-orange-500 opacity-8 rounded-full filter blur-3xl animate-float" style="animation-delay: 2s;"></div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Main Content -->
    <main class="relative z-10 container mx-auto px-4 pt-6 space-y-6 animate-fade-in">
        <!-- Header -->
        <?php include 'partials/top-nav.php'; ?>

        <!-- Profile Header -->
        <div class="bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 rounded-3xl p-6 md:p-8 text-white floating-card animate-slide-up relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6">
                    <div class="profile-avatar relative">
                        <?php if (!empty($user['avatar']) && file_exists("../assets/img/" . $user['avatar'])): ?>
                            <img src="../assets/img/<?php echo htmlspecialchars($user['avatar']); ?>"
                                alt="Profile"
                                class="w-20 h-20 md:w-24 md:h-24 rounded-full object-cover border-4 border-white/20">
                        <?php else: ?>
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-white/20 flex items-center justify-center border-4 border-white/20">
                                <i class="fas fa-user text-white text-2xl md:text-3xl"></i>
                            </div>
                        <?php endif; ?>

                        <button onclick="openAvatarUpload()" class="absolute bottom-0 right-0 w-6 h-6 md:w-8 md:h-8 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-camera text-gray-600 text-xs md:text-sm"></i>
                        </button>
                    </div>

                    <div class="flex-1 text-center sm:text-left">
                        <h2 class="text-xl md:text-2xl lg:text-3xl font-bold mb-2">
                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                        </h2>
                        <p class="text-orange-100 text-sm md:text-base mb-3">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </p>
                        <?php if (!empty($user['phone'])): ?>
                            <p class="text-orange-100 text-sm mb-3">
                                <i class="fas fa-phone mr-2"></i>
                                <?php echo htmlspecialchars($user['phone']); ?>
                            </p>
                        <?php endif; ?>
                        <div class="flex items-center justify-center sm:justify-start text-orange-100 text-xs md:text-sm">
                            <i class="fas fa-calendar mr-2"></i>
                            <span>Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                        </div>
                    </div>

                    <button onclick="openEditProfileModal()"
                        class="w-10 h-10 md:w-12 md:h-12 bg-white/20 backdrop-blur-sm rounded-2xl border border-white/30 flex items-center justify-center hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-edit text-white text-sm md:text-base"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="stats-card bg-white rounded-2xl p-4 md:p-6 floating-card text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shopping-bag text-blue-600 text-lg md:text-xl"></i>
                </div>
                <p class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
                    <?php echo number_format($userStats['total_orders'] ?? 0); ?>
                </p>
                <p class="text-gray-500 text-xs md:text-sm">Total Orders</p>
            </div>

            <div class="stats-card bg-white rounded-2xl p-4 md:p-6 floating-card text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-naira-sign text-green-600 text-lg md:text-xl"></i>
                </div>
                <p class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
                    ₦<?php echo number_format(($userStats['total_amount'] ?? 0) / 1000, 1); ?>k
                </p>
                <p class="text-gray-500 text-xs md:text-sm">Total Spent</p>
            </div>

            <div class="stats-card bg-white rounded-2xl p-4 md:p-6 floating-card text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-star text-orange-600 text-lg md:text-xl"></i>
                </div>
                <p class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
                    <?php echo number_format($user['loyalty_points'] ?? 0); ?>
                </p>
                <p class="text-gray-500 text-xs md:text-sm">Loyalty Points</p>
            </div>

            <div class="stats-card bg-white rounded-2xl p-4 md:p-6 floating-card text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-trophy text-purple-600 text-lg md:text-xl"></i>
                </div>
                <p class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
                    <?php
                    $level = 'Bronze';
                    $totalSpent = $userStats['total_amount'] ?? 0;
                    if ($totalSpent >= 100000) $level = 'Gold';
                    elseif ($totalSpent >= 50000) $level = 'Silver';
                    echo $level;
                    ?>
                </p>
                <p class="text-gray-500 text-xs md:text-sm">Member Level</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 animate-slide-up" style="animation-delay: 0.2s;">
            <button onclick="window.location.href='orders.php'"
                class="bg-white rounded-2xl p-4 text-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
                <p class="font-semibold text-gray-900 text-sm">My Orders</p>
            </button>

            <button onclick="window.location.href='cart.php'"
                class="bg-white rounded-2xl p-4 text-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1 relative">
                <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                            <?php echo $cartCount; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <p class="font-semibold text-gray-900 text-sm">My Cart</p>
            </button>

            <button onclick="openAddressModal()"
                class="bg-white rounded-2xl p-4 text-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                </div>
                <p class="font-semibold text-gray-900 text-sm">Addresses</p>
            </button>

            <button onclick="openContactModal()"
                class="bg-white rounded-2xl p-4 text-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-headset text-purple-600 text-xl"></i>
                </div>
                <p class="font-semibold text-gray-900 text-sm">Support</p>
            </button>
        </div>

        <!-- Account Section -->
        <div class="animate-slide-up" style="animation-delay: 0.3s;">
            <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-4 px-2">Account Settings</h3>
            <div class="bg-white rounded-3xl overflow-hidden floating-card shadow-lg">
                <button onclick="openEditProfileModal()" class="menu-item w-full flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-blue-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-user text-blue-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Edit Profile</p>
                            <p class="text-gray-500 text-xs md:text-sm">Update your personal information</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openAddressModal()" class="menu-item w-full flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-green-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-map-marker-alt text-green-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Delivery Addresses</p>
                            <p class="text-gray-500 text-xs md:text-sm">
                                <?php echo count($addresses); ?> address<?php echo count($addresses) !== 1 ? 'es' : ''; ?> saved
                            </p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openPaymentModal()" class="menu-item w-full flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-purple-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-credit-card text-purple-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Payment Methods</p>
                            <p class="text-gray-500 text-xs md:text-sm">Manage your payment options</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openSecurityModal()" class="menu-item w-full flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-red-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-shield-alt text-red-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Security</p>
                            <p class="text-gray-500 text-xs md:text-sm">Password and security settings</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>
            </div>
        </div>

        <!-- Preferences Section -->
        <div class="animate-slide-up" style="animation-delay: 0.4s;">
            <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-4 px-2">Preferences</h3>
            <div class="bg-white rounded-3xl overflow-hidden floating-card shadow-lg">
                <div class="menu-item flex items-center justify-between p-4 md:p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-yellow-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-bell text-yellow-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Push Notifications</p>
                            <p class="text-gray-500 text-xs md:text-sm">Get notified about orders and offers</p>
                        </div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" <?php echo $preferences['push_notifications'] ? 'checked' : ''; ?>
                            onchange="updatePreference('push_notifications', this.checked)">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="border-t border-gray-100"></div>

                <div class="menu-item flex items-center justify-between p-4 md:p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-indigo-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-envelope text-indigo-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Email Updates</p>
                            <p class="text-gray-500 text-xs md:text-sm">Receive promotional emails and newsletters</p>
                        </div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" <?php echo $preferences['email_updates'] ? 'checked' : ''; ?>
                            onchange="updatePreference('email_updates', this.checked)">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="border-t border-gray-100"></div>

                <button onclick="openLanguageModal()" class="menu-item w-full flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-teal-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-globe text-teal-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Language</p>
                            <p class="text-gray-500 text-xs md:text-sm">
                                <?php
                                $languages = ['en' => 'English (US)', 'yo' => 'Yoruba', 'ha' => 'Hausa', 'ig' => 'Igbo'];
                                echo $languages[$preferences['language']] ?? 'English (US)';
                                ?>
                            </p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>
            </div>
        </div>

        <!-- Support Section -->
        <div class="animate-slide-up" style="animation-delay: 0.5s;">
            <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-4 px-2">Support & Information</h3>
            <div class="bg-white rounded-3xl overflow-hidden floating-card shadow-lg">
                <button onclick="openHelpModal()" class="menu-item w-full flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-orange-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-question-circle text-orange-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Help Center</p>
                            <p class="text-gray-500 text-xs md:text-sm">Get help and support</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openContactModal()" class="menu-item w-full flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-pink-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-headset text-pink-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Contact Us</p>
                            <p class="text-gray-500 text-xs md:text-sm">Reach out to our support team</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openFeedbackModal()" class="menu-item w-full flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-blue-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-comment-alt text-blue-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Send Feedback</p>
                            <p class="text-gray-500 text-xs md:text-sm">Share your experience with us</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openAboutModal()" class="menu-item w-full flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-gray-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle text-gray-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">About Salya</p>
                            <p class="text-gray-500 text-xs md:text-sm">Version 1.0.0</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>
            </div>
        </div>

        <!-- Sign Out Button -->
        <div class="animate-slide-up" style="animation-delay: 0.6s;">
            <button onclick="openSignOutModal()"
                class="w-full bg-red-50 border border-red-200 text-red-600 py-4 md:py-5 rounded-2xl font-semibold hover:bg-red-100 transition-all duration-300 transform hover:scale-[1.02]">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Sign Out
            </button>
        </div>
    </main>

    <!-- Bottom navigation -->
    <?php include 'partials/bottom-nav.php'; ?>

    <!-- Modals will be included here -->
    <?php include 'partials/profile-modals.php'; ?>

    <!-- Scripts -->
    <script src="../assets/js/toast.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Global variables
        let currentUser = <?php echo json_encode($user); ?>;
        let userPreferences = <?php echo json_encode($preferences); ?>;
        let userAddresses = <?php echo json_encode($addresses); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            initializeProfile();
            initializeAnimations();
            initializeInteractions();
        });

        function initializeProfile() {
            // Update cart count
            updateCartCount();

            // Initialize preferences
            updatePreferencesUI();

            // Set up real-time updates
            setupRealtimeUpdates();
        }

        function initializeAnimations() {
            // Stagger animation for menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.05}s`;
            });

            // Add floating animation
            const floatingCards = document.querySelectorAll('.floating-card');
            floatingCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate-float-up');
            });
        }

        function initializeInteractions() {
            // Add ripple effect to clickable elements
            const clickableElements = document.querySelectorAll('button, .menu-item');
            clickableElements.forEach(element => {
                element.addEventListener('click', createRippleEffect);
            });

            // Add hover effects for stats cards
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        }

        function createRippleEffect(e) {
            const button = e.currentTarget;
            const rect = button.getBoundingClientRect();
            const ripple = document.createElement('div');

            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            button.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        }

        // Modal functionality
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                setTimeout(() => {
                    const content = modal.querySelector('.modal-content');
                    if (content) {
                        content.classList.add('animate-modal-in');
                    }
                }, 10);
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                const content = modal.querySelector('.modal-content');

                if (content) {
                    content.classList.remove('animate-modal-in');
                    content.classList.add('animate-modal-out');
                }

                setTimeout(() => {
                    modal.classList.add('hidden');
                    if (content) {
                        content.classList.remove('animate-modal-out');
                    }
                    document.body.style.overflow = '';
                }, 300);
            }
        }

        // Specific modal functions
        function openEditProfileModal() {
            openModal('editProfileModal');
        }

        function openAddressModal() {
            openModal('addressModal');
        }

        function openPaymentModal() {
            openModal('paymentModal');
        }

        function openSecurityModal() {
            openModal('securityModal');
        }

        function openLanguageModal() {
            openModal('languageModal');
        }

        function openHelpModal() {
            openModal('helpModal');
        }

        function openContactModal() {
            openModal('contactModal');
        }

        function openFeedbackModal() {
            openModal('feedbackModal');
        }

        function openAboutModal() {
            openModal('aboutModal');
        }

        function openSignOutModal() {
            openModal('signOutModal');
        }

        function openAvatarUpload() {
            openModal('avatarUploadModal');
        }

        // Update preferences
        async function updatePreference(key, value) {
            try {
                showToasted('Updating preference...', 'info');

                const response = await fetch('api/update-preferences.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        preference: key,
                        value: value
                    })
                });

                const result = await response.json();

                if (result.success) {
                    userPreferences[key] = value;
                    showToasted('Preference updated successfully!', 'success');
                } else {
                    throw new Error(result.message || 'Failed to update preference');
                }
            } catch (error) {
                console.error('Error updating preference:', error);
                showToasted('Failed to update preference', 'error');

                // Revert the checkbox state
                const checkbox = document.querySelector(`input[onchange*="${key}"]`);
                if (checkbox) {
                    checkbox.checked = !value;
                }
            }
        }

        function updatePreferencesUI() {
            // Update UI elements based on current preferences
            Object.keys(userPreferences).forEach(key => {
                const element = document.querySelector(`input[onchange*="${key}"]`);
                if (element) {
                    element.checked = userPreferences[key];
                }
            });
        }

        // Profile update
        async function updateProfile(formData) {
            try {
                const response = await fetch('api/update-profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    // Update local user data
                    Object.assign(currentUser, formData);

                    // Update UI
                    updateProfileUI();

                    showToasted('Profile updated successfully!', 'success');
                    closeModal('editProfileModal');
                } else {
                    throw new Error(result.message || 'Failed to update profile');
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                showToasted('Failed to update profile', 'error');
            }
        }

        function updateProfileUI() {
            // Update profile name in header
            const nameElement = document.querySelector('h2');
            if (nameElement) {
                nameElement.textContent = `${currentUser.first_name} ${currentUser.last_name}`;
            }

            // Update email
            const emailElements = document.querySelectorAll('.text-orange-100');
            emailElements.forEach(el => {
                if (el.textContent.includes('@')) {
                    el.textContent = currentUser.email;
                }
            });
        }

        // Real-time updates
        function setupRealtimeUpdates() {
            // Update cart count periodically
            setInterval(updateCartCount, 30000); // Every 30 seconds

            // Check for new notifications
            setInterval(checkNotifications, 60000); // Every minute
        }

        async function checkNotifications() {
            try {
                const response = await fetch('api/get-notifications.php');
                const result = await response.json();

                if (result.success && result.notifications.length > 0) {
                    // Handle new notifications
                    result.notifications.forEach(notification => {
                        showToasted(notification.message, notification.type);
                    });
                }
            } catch (error) {
                console.error('Error checking notifications:', error);
            }
        }

        // Enhanced sign out functionality
        async function signOut() {
            const signOutBtn = document.querySelector('#signOutModal button:last-child');
            const originalText = signOutBtn.innerHTML;

            // Show loading state
            signOutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing out...';
            signOutBtn.disabled = true;

            try {
                // Clear any local storage data
                localStorage.removeItem('cart_items');
                localStorage.removeItem('user_preferences');
                localStorage.removeItem('recent_searches');

                // Clear session storage
                sessionStorage.clear();

                const response = await fetch('api/logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showToasted('Signed out successfully', 'success');

                    // Clear cart count from UI
                    const cartBadge = document.getElementById('cartCount');
                    if (cartBadge) {
                        cartBadge.textContent = '0';
                        cartBadge.parentElement.style.display = 'none';
                    }

                    // Add fade out effect before redirect
                    document.body.style.transition = 'opacity 0.5s ease-out';
                    document.body.style.opacity = '0';

                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 500);
                } else {
                    throw new Error(result.message || 'Failed to sign out');
                }
            } catch (error) {
                console.error('Sign out error:', error);

                // Restore button state
                signOutBtn.innerHTML = originalText;
                signOutBtn.disabled = false;

                // Show error message
                showToasted('Failed to sign out. Please try again.', 'error');

                // Fallback: redirect to logout page directly
                setTimeout(() => {
                    window.location.href = 'logout.php';
                }, 2000);
            }
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                const modalId = e.target.id;
                closeModal(modalId);
            }
        });

        // Add custom CSS animations
        const style = document.createElement('style');
        style.textContent = `
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            
            .animate-float-up {
                animation: floatUp 0.8s ease-out forwards;
            }
            
            .animate-fade-in {
                animation: fadeIn 0.6s ease-out forwards;
            }
            
            .animate-slide-up {
                animation: slideUp 0.8s ease-out forwards;
            }
            
            .animate-modal-in {
                animation: modalIn 0.3s ease-out forwards;
            }
            
            .animate-modal-out {
                animation: modalOut 0.3s ease-in forwards;
            }
            
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(249, 115, 22, 0.3);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            
            @keyframes floatUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes modalIn {
                from {
                    opacity: 0;
                    transform: scale(0.9) translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }
            
            @keyframes modalOut {
                from {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
                to {
                    opacity: 0;
                    transform: scale(0.9) translateY(-20px);
                }
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
            
            /* Switch styles */
            .switch {
                position: relative;
                display: inline-block;
                width: 48px;
                height: 28px;
            }
            
            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }
            
            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 28px;
            }
            
            .slider:before {
                position: absolute;
                content: "";
                height: 20px;
                width: 20px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }
            
            input:checked + .slider {
                background-color: #f97316;
            }
            
            input:checked + .slider:before {
                transform: translateX(20px);
            }
            
            /* Responsive improvements */
            @media (max-width: 640px) {
                .container {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }
                
                .grid-cols-2 {
                    gap: 0.75rem;
                }
                
                .stats-card {
                    padding: 1rem;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>