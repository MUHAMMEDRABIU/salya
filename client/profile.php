<?php
require_once __DIR__ . '/initialize.php';
require_once __DIR__ . '/util/util.php';
require_once 'partials/headers.php';

$user = getUserProfile($pdo, $user_id = 1);
$userStats = getUserStatistics($pdo, $user_id = 1);
?>

<body class="bg-gray font-dm pb-24 overflow-x-hidden">
    <!-- Main Content -->
    <main class="px-4 pt-6 space-y-6 animate-fade-in">
        <!-- Profile Header -->
        <div class="gradient-bg rounded-3xl p-6 text-white floating-card animate-slide-up">
            <div class="flex items-center space-x-4">
                <div class="profile-avatar rounded-full position-relative">
                    <img src="../assets/img/<?php echo $user['avatar']; ?>" alt="Profile" class="w-20 h-20 rounded-full object-cover">
                    <!-- data lucid for camera icon -->
                    <div class="absolute bottom-0 right-0 w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-camera text-gray-600 text-xs"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-1"><?php echo $user['first_name'] . '&nbsp;' . $user['last_name']; ?></h2>
                    <p class="text-orange-100 text-sm mb-2"><?php echo $user['email']; ?></p>
                    <div class="flex items-center text-orange-100 text-xs">
                        <i class="fas fa-calendar mr-1"></i>
                        <span>Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
                <button onclick="openEditProfileModal()" class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-2xl border border-white/30 flex items-center justify-center hover:bg-white/30 transition-colors">
                    <i class="fas fa-edit text-white text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-3 gap-3 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="stats-card bg-white rounded-2xl p-4 floating-card text-center">
                <div class="w-10 h-10 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-shopping-bag text-blue-600 text-lg"></i>
                </div>
                <p class="text-2xl font-bold text-dark"><?php echo $userStats['total_orders'] ?? 0; ?></p>
                <p class="text-gray-500 text-xs">Orders</p>
            </div>
            <div class="stats-card bg-white rounded-2xl p-4 floating-card text-center">
                <div class="w-10 h-10 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-naira-sign text-green-600 text-lg"></i>
                </div>
                <p class="text-2xl font-bold text-dark">₦<?php echo number_format(($userStats['total_amount'] ?? 0) / 1000); ?>k</p>
                <p class="text-gray-500 text-xs">Spent</p>
            </div>
            <div class="stats-card bg-white rounded-2xl p-4 floating-card text-center">
                <div class="w-10 h-10 bg-accent/10 rounded-2xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-star text-accent text-lg"></i>
                </div>
                <p class="text-2xl font-bold text-dark"><?php echo number_format($user['loyalty_points'] ?? 0); ?></p>
                <p class="text-gray-500 text-xs">Points</p>
            </div>
        </div>

        <!-- Account Section -->
        <div class="animate-slide-up" style="animation-delay: 0.2s;">
            <h3 class="text-lg font-bold text-dark mb-4 px-2">Account</h3>
            <div class="bg-white rounded-3xl overflow-hidden floating-card">
                <button onclick="openEditProfileModal()" class="menu-item w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-user text-blue-600 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-dark">Edit Profile</p>
                            <p class="text-gray-500 text-sm">Update your personal information</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openAddressModal()" class="menu-item w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-map-marker-alt text-green-600 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-dark">Delivery Address</p>
                            <p class="text-gray-500 text-sm">Manage your delivery locations</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openPaymentModal()" class="menu-item w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-credit-card text-purple-600 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-dark">Payment Methods</p>
                            <p class="text-gray-500 text-sm">Manage your payment options</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>
            </div>
        </div>

        <!-- Preferences Section -->
        <div class="animate-slide-up" style="animation-delay: 0.3s;">
            <h3 class="text-lg font-bold text-dark mb-4 px-2">Preferences</h3>
            <div class="bg-white rounded-3xl overflow-hidden floating-card">
                <div class="menu-item flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-bell text-yellow-600 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-dark">Push Notifications</p>
                            <p class="text-gray-500 text-sm">Get notified about orders</p>
                        </div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="border-t border-gray-100"></div>

                <div class="menu-item flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-envelope text-indigo-600 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-dark">Email Updates</p>
                            <p class="text-gray-500 text-sm">Receive promotional emails</p>
                        </div>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="border-t border-gray-100"></div>

                <button onclick="openLanguageModal()" class="menu-item w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-teal-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-globe text-teal-600 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-dark">Language</p>
                            <p class="text-gray-500 text-sm">English (US)</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>
            </div>
        </div>

        <!-- Support Section -->
        <div class="animate-slide-up" style="animation-delay: 0.4s;">
            <h3 class="text-lg font-bold text-dark mb-4 px-2">Support</h3>
            <div class="bg-white rounded-3xl overflow-hidden floating-card">
                <button onclick="openHelpModal()" class="menu-item w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-question-circle text-orange-600 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-dark">Help Center</p>
                            <p class="text-gray-500 text-sm">Get help and support</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openContactModal()" class="menu-item w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-pink-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-headset text-pink-600 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-dark">Contact Us</p>
                            <p class="text-gray-500 text-sm">Reach out to our team</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div class="border-t border-gray-100"></div>

                <button onclick="openAboutModal()" class="menu-item w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle text-gray-600 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-dark">About</p>
                            <p class="text-gray-500 text-sm">App version 1.0.0</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>
            </div>
        </div>

        <!-- Sign Out Button -->
        <div class="animate-slide-up" style="animation-delay: 0.5s;">
            <button onclick="openSignOutModal()" class="w-full bg-red-50 border border-red-200 text-red-600 py-4 rounded-2xl font-semibold hover:bg-red-100 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Sign Out
            </button>
        </div>
    </main>
    <!-- Bottom navigation include -->
    <?php include 'partials/bottom-nav.php'; ?>
    <!-- Sign Out Modal -->
    <div id="signOutModal" class="fixed inset-0 modal-overlay z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-content bg-white rounded-3xl p-6 w-full max-w-sm animate-modal-in">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-2">Sign Out</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to sign out of your account?</p>
                    <div class="flex space-x-3">
                        <button onclick="closeModal('signOutModal')" class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-2xl font-semibold hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button onclick="signOut()" class="flex-1 bg-red-500 text-white py-3 rounded-2xl font-semibold hover:bg-red-600 transition-colors">
                            Sign Out
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="fixed inset-0 modal-overlay z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-content bg-white rounded-3xl p-6 w-full max-w-md animate-modal-in">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-dark">Edit Profile</h3>
                    <button onclick="closeModal('editProfileModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times text-gray-600 text-sm"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Full Name</label>
                        <input type="text" value="<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-accent focus:border-accent transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Email</label>
                        <input type="email" value="<?php echo $user['email']; ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-accent focus:border-accent transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Phone</label>
                        <input type="tel" value="<?php echo $user['phone']; ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-accent focus:border-accent transition-colors">
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeModal('editProfileModal')" class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-2xl font-semibold hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 bg-accent text-white py-3 rounded-2xl font-semibold hover:bg-orange-600 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Address Modal -->
    <div id="addressModal" class="fixed inset-0 modal-overlay z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-content bg-white rounded-3xl p-6 w-full max-w-md animate-modal-in">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-dark">Delivery Address</h3>
                    <button onclick="closeModal('addressModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times text-gray-600 text-sm"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-dark">Home</h4>
                            <span class="bg-accent text-white text-xs px-2 py-1 rounded-full">Default</span>
                        </div>
                        <p class="text-gray-600 text-sm">123 Main Street, Lagos, Nigeria</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-dark">Office</h4>
                            <button class="text-accent text-sm font-semibold">Edit</button>
                        </div>
                        <p class="text-gray-600 text-sm">456 Business Ave, Victoria Island, Lagos</p>
                    </div>
                    <button class="w-full bg-accent text-white py-3 rounded-2xl font-semibold hover:bg-orange-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Address
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 modal-overlay z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-content bg-white rounded-3xl p-6 w-full max-w-md animate-modal-in">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-dark">Payment Methods</h3>
                    <button onclick="closeModal('paymentModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times text-gray-600 text-sm"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <i class="fab fa-cc-visa text-blue-600 text-2xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-dark">**** 1234</h4>
                                    <p class="text-gray-600 text-sm">Expires 12/25</p>
                                </div>
                            </div>
                            <span class="bg-accent text-white text-xs px-2 py-1 rounded-full">Default</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <i class="fab fa-cc-mastercard text-red-600 text-2xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-dark">**** 5678</h4>
                                    <p class="text-gray-600 text-sm">Expires 08/26</p>
                                </div>
                            </div>
                            <button class="text-accent text-sm font-semibold">Edit</button>
                        </div>
                    </div>
                    <button class="w-full bg-accent text-white py-3 rounded-2xl font-semibold hover:bg-orange-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Card
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Language Modal -->
    <div id="languageModal" class="fixed inset-0 modal-overlay z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-content bg-white rounded-3xl p-6 w-full max-w-sm animate-modal-in">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-dark">Select Language</h3>
                    <button onclick="closeModal('languageModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times text-gray-600 text-sm"></i>
                    </button>
                </div>
                <div class="space-y-2">
                    <button class="w-full flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition-colors">
                        <span class="font-semibold text-dark">English (US)</span>
                        <i class="fas fa-check text-accent"></i>
                    </button>
                    <button class="w-full flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition-colors">
                        <span class="text-gray-600">Yoruba</span>
                    </button>
                    <button class="w-full flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition-colors">
                        <span class="text-gray-600">Hausa</span>
                    </button>
                    <button class="w-full flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition-colors">
                        <span class="text-gray-600">Igbo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <div id="helpModal" class="fixed inset-0 modal-overlay z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-content bg-white rounded-3xl p-6 w-full max-w-md animate-modal-in">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-dark">Help Center</h3>
                    <button onclick="closeModal('helpModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times text-gray-600 text-sm"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    <button class="w-full flex items-center p-3 rounded-2xl hover:bg-gray-50 transition-colors text-left">
                        <i class="fas fa-question-circle text-blue-600 text-lg mr-3"></i>
                        <span class="text-dark">Frequently Asked Questions</span>
                    </button>
                    <button class="w-full flex items-center p-3 rounded-2xl hover:bg-gray-50 transition-colors text-left">
                        <i class="fas fa-book text-green-600 text-lg mr-3"></i>
                        <span class="text-dark">User Guide</span>
                    </button>
                    <button class="w-full flex items-center p-3 rounded-2xl hover:bg-gray-50 transition-colors text-left">
                        <i class="fas fa-video text-purple-600 text-lg mr-3"></i>
                        <span class="text-dark">Video Tutorials</span>
                    </button>
                    <button class="w-full flex items-center p-3 rounded-2xl hover:bg-gray-50 transition-colors text-left">
                        <i class="fas fa-bug text-red-600 text-lg mr-3"></i>
                        <span class="text-dark">Report a Problem</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactModal" class="fixed inset-0 modal-overlay z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-content bg-white rounded-3xl p-6 w-full max-w-md animate-modal-in">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-dark">Contact Us</h3>
                    <button onclick="closeModal('contactModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times text-gray-600 text-sm"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-phone text-green-600 text-lg mr-3"></i>
                            <span class="font-semibold text-dark">Phone</span>
                        </div>
                        <p class="text-gray-600">+234 800 123 4567</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-envelope text-blue-600 text-lg mr-3"></i>
                            <span class="font-semibold text-dark">Email</span>
                        </div>
                        <p class="text-gray-600">support@frozenfoods.com</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fab fa-whatsapp text-green-600 text-lg mr-3"></i>
                            <span class="font-semibold text-dark">WhatsApp</span>
                        </div>
                        <p class="text-gray-600">+234 800 123 4567</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Modal -->
    <div id="aboutModal" class="fixed inset-0 modal-overlay z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-content bg-white rounded-3xl p-6 w-full max-w-md animate-modal-in">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-dark">About</h3>
                    <button onclick="closeModal('aboutModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times text-gray-600 text-sm"></i>
                    </button>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-accent to-secondary rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-snowflake text-white text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-dark mb-2">Frozen Foods</h4>
                    <p class="text-gray-600 mb-4">Version 1.0.0</p>
                    <p class="text-gray-600 text-sm mb-6">Your trusted partner for fresh frozen foods delivered right to your doorstep.</p>
                    <div class="space-y-2 text-sm text-gray-500">
                        <p>© 2024 Frozen Foods Ltd.</p>
                        <p>All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Add animation
            setTimeout(() => {
                const content = modal.querySelector('.modal-content');
                content.classList.add('animate-modal-in');
            }, 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('.modal-content');

            content.classList.remove('animate-modal-in');
            content.classList.add('animate-modal-out');

            setTimeout(() => {
                modal.classList.add('hidden');
                content.classList.remove('animate-modal-out');
                document.body.style.overflow = '';
            }, 300);
        }

        // Specific modal functions
        function openSignOutModal() {
            openModal('signOutModal');
        }

        function openEditProfileModal() {
            openModal('editProfileModal');
        }

        function openAddressModal() {
            openModal('addressModal');
        }

        function openPaymentModal() {
            openModal('paymentModal');
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

        function openAboutModal() {
            openModal('aboutModal');
        }

        // Sign out functionality
        function signOut() {
            // Add loading state
            const signOutBtn = document.querySelector('#signOutModal button:last-child');
            const originalText = signOutBtn.innerHTML;
            signOutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing out...';
            signOutBtn.disabled = true;

            // Simulate sign out process
            setTimeout(() => {
                // Redirect to login page or handle sign out
                window.location.href = 'logout.php';
            }, 1500);
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                const modalId = e.target.id;
                closeModal(modalId);
            }
        });

        // Enhanced bottom navigation with premium interactions
        document.addEventListener('DOMContentLoaded', function() {
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

            // Stagger animation for menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.05}s`;
            });

            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });

        // Form submission handling
        document.querySelector('#editProfileModal form').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            submitBtn.disabled = true;

            // Gather form data
            const form = this;
            const fullName = form.querySelector('input[type="text"]').value.trim();
            const email = form.querySelector('input[type="email"]').value.trim();
            const phone = form.querySelector('input[type="tel"]').value.trim();

            fetch('../api/api_update_profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        fullName,
                        email,
                        phone
                    })
                })
                .then(res => res.json())
                .then(data => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    if (data.success) {
                        closeModal('editProfileModal');
                        showToast('Profile updated successfully!', 'success');
                        // Optionally update UI with new values
                        document.querySelector('.flex-1 h2').innerHTML = fullName;
                        document.querySelector('.flex-1 .text-orange-100').innerHTML = email;
                    } else {
                        showToast(data.message || 'Update failed', 'error');
                    }
                })
                .catch(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    showToast('Network error. Please try again.', 'error');
                });
        });

        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-20 left-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-4 rounded-2xl shadow-lg transform -translate-y-full opacity-0 transition-all duration-300 z-50`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-xl mr-3"></i>
                    <span class="font-semibold">${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            // Show toast
            setTimeout(() => {
                toast.style.transform = 'translateY(0)';
                toast.style.opacity = '1';
            }, 100);

            // Hide after 3 seconds
            setTimeout(() => {
                toast.style.transform = 'translateY(-100%)';
                toast.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
    </script>
</body>

</html>