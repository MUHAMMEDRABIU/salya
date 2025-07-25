<?php
// Sample user settings (in real app, this would come from database)
$user_settings = [
    'email_notifications' => true,
    'sms_notifications' => true,
    'marketing_notifications' => false,
    'order_updates' => true,
    'promotional_emails' => false,
    'delivery_notifications' => true,
    'language' => 'en',
    'currency' => 'NGN',
    'timezone' => 'Africa/Lagos'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Frozen Foods</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#F97316',
                        gray: '#f6f7fc',
                        dark: '#201f20',
                        secondary: '#ff7272'
                    },
                    fontFamily: {
                        'dm': ['DM Sans', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray font-dm">
    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0">
        <div class="p-6">
            <h2 class="text-xl font-bold text-dark">Frozen Foods</h2>
        </div>
        <nav class="mt-6">
            <a href="dashboard.php" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-home mr-3"></i>
                Dashboard
            </a>
            <a href="orders.php" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-shopping-cart mr-3"></i>
                Orders
            </a>
            <a href="favorites.php" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-heart mr-3"></i>
                Favorites
            </a>
            <a href="profile.php" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-user mr-3"></i>
                Profile
            </a>
        </nav>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-4 py-4">
                <div class="flex items-center">
                    <button id="menu-toggle" class="lg:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="ml-4 text-xl font-semibold text-dark">Settings</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <i class="fas fa-bell text-gray-600 text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-r from-accent to-secondary rounded-full border-2 border-white shadow-md flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Settings Content -->
        <div class="p-6 max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-dark mb-2">Account Settings</h2>
                <p class="text-gray-600">Manage your account preferences and settings</p>
            </div>

            <!-- Settings Sections -->
            <div class="space-y-6">
                <!-- Notification Settings -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-dark mb-4 flex items-center">
                        <i class="fas fa-bell mr-2 text-accent"></i>
                        Notification Preferences
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-dark">Email Notifications</h4>
                                <p class="text-sm text-gray-600">Receive order updates and important information via email</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" <?php echo $user_settings['email_notifications'] ? 'checked' : ''; ?> data-setting="email_notifications">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-accent peer-focus:ring-opacity-25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-dark">SMS Notifications</h4>
                                <p class="text-sm text-gray-600">Get delivery updates and order confirmations via SMS</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" <?php echo $user_settings['sms_notifications'] ? 'checked' : ''; ?> data-setting="sms_notifications">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-accent peer-focus:ring-opacity-25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-dark">Marketing Communications</h4>
                                <p class="text-sm text-gray-600">Receive promotional offers and special deals</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" <?php echo $user_settings['marketing_notifications'] ? 'checked' : ''; ?> data-setting="marketing_notifications">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-accent peer-focus:ring-opacity-25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-dark">Order Updates</h4>
                                <p class="text-sm text-gray-600">Get notified about order status changes</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" <?php echo $user_settings['order_updates'] ? 'checked' : ''; ?> data-setting="order_updates">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-accent peer-focus:ring-opacity-25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-dark mb-4 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-accent"></i>
                        Security & Privacy
                    </h3>
                    <div class="space-y-4">
                        <button id="change-password-btn" class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="text-left">
                                <h4 class="font-medium text-dark">Change Password</h4>
                                <p class="text-sm text-gray-600">Update your account password</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>
                        <button id="two-factor-btn" class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="text-left">
                                <h4 class="font-medium text-dark">Two-Factor Authentication</h4>
                                <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-red-600 mr-2">Disabled</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </button>
                        <button id="login-activity-btn" class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="text-left">
                                <h4 class="font-medium text-dark">Login Activity</h4>
                                <p class="text-sm text-gray-600">View recent login activity and sessions</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>
                    </div>
                </div>

                <!-- App Preferences -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-dark mb-4 flex items-center">
                        <i class="fas fa-cog mr-2 text-accent"></i>
                        App Preferences
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-dark">Language</h4>
                                <p class="text-sm text-gray-600">Choose your preferred language</p>
                            </div>
                            <select class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent focus:border-transparent" data-setting="language">
                                <option value="en" <?php echo $user_settings['language'] === 'en' ? 'selected' : ''; ?>>English</option>
                                <option value="ha" <?php echo $user_settings['language'] === 'ha' ? 'selected' : ''; ?>>Hausa</option>
                                <option value="yo" <?php echo $user_settings['language'] === 'yo' ? 'selected' : ''; ?>>Yoruba</option>
                                <option value="ig" <?php echo $user_settings['language'] === 'ig' ? 'selected' : ''; ?>>Igbo</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-dark">Currency</h4>
                                <p class="text-sm text-gray-600">Display prices in your preferred currency</p>
                            </div>
                            <select class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent focus:border-transparent" data-setting="currency">
                                <option value="NGN" <?php echo $user_settings['currency'] === 'NGN' ? 'selected' : ''; ?>>Nigerian Naira (₦)</option>
                                <option value="USD" <?php echo $user_settings['currency'] === 'USD' ? 'selected' : ''; ?>>US Dollar ($)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-dark">Timezone</h4>
                                <p class="text-sm text-gray-600">Set your local timezone</p>
                            </div>
                            <select class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent focus:border-transparent" data-setting="timezone">
                                <option value="Africa/Lagos" <?php echo $user_settings['timezone'] === 'Africa/Lagos' ? 'selected' : ''; ?>>Lagos (WAT)</option>
                                <option value="Africa/Abuja" <?php echo $user_settings['timezone'] === 'Africa/Abuja' ? 'selected' : ''; ?>>Abuja (WAT)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Data & Privacy -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-dark mb-4 flex items-center">
                        <i class="fas fa-database mr-2 text-accent"></i>
                        Data & Privacy
                    </h3>
                    <div class="space-y-4">
                        <button id="download-data-btn" class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="text-left">
                                <h4 class="font-medium text-dark">Download Your Data</h4>
                                <p class="text-sm text-gray-600">Get a copy of your account data</p>
                            </div>
                            <i class="fas fa-download text-gray-400"></i>
                        </button>
                        <button id="delete-account-btn" class="w-full flex items-center justify-between p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <div class="text-left">
                                <h4 class="font-medium text-red-600">Delete Account</h4>
                                <p class="text-sm text-red-500">Permanently delete your account and all data</p>
                            </div>
                            <i class="fas fa-trash text-red-400"></i>
                        </button>
                    </div>
                </div>

                <!-- Logout Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-dark mb-4 flex items-center">
                        <i class="fas fa-sign-out-alt mr-2 text-accent"></i>
                        Session
                    </h3>
                    <button id="logout-btn" class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="text-center">
                <i class="fas fa-sign-out-alt text-red-600 text-4xl mb-4"></i>
                <h3 class="text-lg font-semibold text-dark mb-2">Confirm Logout</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to logout? You'll need to sign in again to access your account.</p>
                <div class="flex space-x-3">
                    <button id="confirm-logout-btn" class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors">
                        Yes, Logout
                    </button>
                    <button id="cancel-logout-btn" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize settings page
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebar();
            initializeSettingsToggles();
            initializeSettingsActions();
            initializeLogoutModal();
        });

        // Sidebar functionality
        function initializeSidebar() {
            const menuToggle = document.getElementById('menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (!menuToggle || !sidebar || !overlay) return;

            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            });

            overlay.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }

        // Settings toggles functionality
        function initializeSettingsToggles() {
            const toggles = document.querySelectorAll('input[type="checkbox"][data-setting]');
            const selects = document.querySelectorAll('select[data-setting]');
            
            // Handle checkbox toggles
            toggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const setting = this.getAttribute('data-setting');
                    const value = this.checked;
                    
                    // Save setting
                    saveSetting(setting, value);
                    
                    // Show feedback
                    const settingName = this.closest('.flex').querySelector('h4').textContent;
                    showNotification(`${settingName} ${value ? 'enabled' : 'disabled'}`, 'success');
                });
            });
            
            // Handle select changes
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    const setting = this.getAttribute('data-setting');
                    const value = this.value;
                    
                    // Save setting
                    saveSetting(setting, value);
                    
                    // Show feedback
                    const settingName = this.closest('.flex').querySelector('h4').textContent;
                    showNotification(`${settingName} updated`, 'success');
                });
            });
        }

        // Settings actions functionality
        function initializeSettingsActions() {
            // Change password
            document.getElementById('change-password-btn').addEventListener('click', function() {
                showNotification('Redirecting to password change...', 'info');
                // In real app, redirect to password change page or open modal
            });

            // Two-factor authentication
            document.getElementById('two-factor-btn').addEventListener('click', function() {
                showNotification('Opening two-factor authentication setup...', 'info');
                // In real app, redirect to 2FA setup
            });

            // Login activity
            document.getElementById('login-activity-btn').addEventListener('click', function() {
                showNotification('Loading login activity...', 'info');
                // In real app, show login activity modal or page
            });

            // Download data
            document.getElementById('download-data-btn').addEventListener('click', function() {
                showNotification('Preparing your data download...', 'info');
                // In real app, initiate data export
            });

            // Delete account
            document.getElementById('delete-account-btn').addEventListener('click', function() {
                if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                    showNotification('Account deletion initiated. Please check your email for confirmation.', 'warning');
                    // In real app, initiate account deletion process
                }
            });
        }

        // Logout modal functionality
        function initializeLogoutModal() {
            const logoutBtn = document.getElementById('logout-btn');
            const logoutModal = document.getElementById('logout-modal');
            const confirmLogoutBtn = document.getElementById('confirm-logout-btn');
            const cancelLogoutBtn = document.getElementById('cancel-logout-btn');

            // Open logout modal
            logoutBtn.addEventListener('click', function() {
                logoutModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });

            // Cancel logout
            cancelLogoutBtn.addEventListener('click', function() {
                closeLogoutModal();
            });

            // Confirm logout
            confirmLogoutBtn.addEventListener('click', function() {
                // Show loading state
                confirmLogoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Logging out...';
                confirmLogoutBtn.disabled = true;
                
                // Simulate logout process
                setTimeout(() => {
                    showNotification('Logged out successfully', 'success');
                    // In real app, redirect to login page
                    // window.location.href = 'login.php';
                    
                    closeLogoutModal();
                    confirmLogoutBtn.innerHTML = 'Yes, Logout';
                    confirmLogoutBtn.disabled = false;
                }, 2000);
            });

            // Close modal on backdrop click
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    closeLogoutModal();
                }
            });
        }

        // Close logout modal
        function closeLogoutModal() {
            document.getElementById('logout-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Save setting function
        function saveSetting(setting, value) {
            // In real app, make API call to save setting
            // For demo, save to localStorage
            let settings = JSON.parse(localStorage.getItem('userSettings') || '{}');
            settings[setting] = value;
            localStorage.setItem('userSettings', JSON.stringify(settings));
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform translate-x-full transition-transform duration-300 ${getNotificationColor(type)}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        function getNotificationColor(type) {
            switch (type) {
                case 'success': return 'bg-green-500';
                case 'error': return 'bg-red-500';
                case 'warning': return 'bg-yellow-500';
                default: return 'bg-blue-500';
            }
        }
    </script>
</body>
</html>