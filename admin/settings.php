<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/util/utilities.php';
require __DIR__ . '/partials/headers.php';
?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>
        <!-- Settings Content -->
        <main class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Settings Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6 settings-card-hover">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Settings</h2>
                            <nav class="space-y-2" id="settingsNavigation">
                                <a href="#general" class="settings-nav-link flex items-center px-3 py-2 text-orange-600 bg-orange-50 rounded-lg settings-nav-active" data-section="general">
                                    <i data-lucide="settings" class="w-4 h-4 mr-3"></i>
                                    General
                                </a>
                                <a href="#notifications" class="settings-nav-link flex items-center px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-lg" data-section="notifications">
                                    <i data-lucide="bell" class="w-4 h-4 mr-3"></i>
                                    Notifications
                                </a>
                                <a href="#security" class="settings-nav-link flex items-center px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-lg" data-section="security">
                                    <i data-lucide="shield" class="w-4 h-4 mr-3"></i>
                                    Security
                                </a>
                                <a href="#maintenance" class="settings-nav-link flex items-center px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-lg" data-section="maintenance">
                                    <i data-lucide="wrench" class="w-4 h-4 mr-3"></i>
                                    Maintenance
                                </a>
                                <a href="#system-logs" class="settings-nav-link flex items-center px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-lg" data-section="system-logs">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-3"></i>
                                    System Logs
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- General Settings -->
                    <div id="general" class="settings-section bg-white rounded-lg shadow-sm border border-gray-200 settings-card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">General Settings</h3>
                        </div>
                        <form id="generalSettingsForm" class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                                <input type="text" name="business_name" class="settings-input-focus w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Business Email</label>
                                <input type="email" name="business_email" class="settings-input-focus w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phone_number" class="settings-input-focus w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <textarea name="business_address" rows="3" class="settings-input-focus w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                                <select name="timezone" class="settings-input-focus w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200">
                                    <option value="Eastern Time (ET)">Eastern Time (ET)</option>
                                    <option value="Central Time (CT)">Central Time (CT)</option>
                                    <option value="Mountain Time (MT)">Mountain Time (MT)</option>
                                    <option value="Pacific Time (PT)">Pacific Time (PT)</option>
                                </select>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="settings-btn-hover bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-all duration-200 flex items-center">
                                    <span class="settings-btn-text">Save Changes</span>
                                    <i data-lucide="loader" class="settings-btn-loading w-4 h-4 ml-2 hidden"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Notification Settings -->
                    <div id="notifications" class="settings-section settings-section-hidden bg-white rounded-lg shadow-sm border border-gray-200 settings-card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Notification Settings</h3>
                        </div>
                        <form id="notificationsSettingsForm" class="p-6 space-y-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Email Notifications</h4>
                                        <p class="text-sm text-gray-500">Receive notifications via email</p>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" class="sr-only settings-toggle-input" name="email_notifications" id="email-notifications">
                                        <label for="email-notifications" class="settings-toggle-switch flex items-center h-6 w-11 cursor-pointer rounded-full bg-gray-300 transition-all duration-300">
                                            <span class="sr-only">Enable email notifications</span>
                                            <span class="settings-toggle-thumb h-5 w-5 bg-white rounded-full transition-transform duration-300" style="transform: translateX(2px);"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Order Notifications</h4>
                                        <p class="text-sm text-gray-500">Get notified when new orders are placed</p>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" class="sr-only settings-toggle-input" name="order_notifications" id="order-notifications">
                                        <label for="order-notifications" class="settings-toggle-switch flex items-center h-6 w-11 cursor-pointer rounded-full bg-gray-300 transition-all duration-300">
                                            <span class="sr-only">Enable order notifications</span>
                                            <span class="settings-toggle-thumb h-5 w-5 bg-white rounded-full transition-transform duration-300" style="transform: translateX(2px);"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Low Stock Alerts</h4>
                                        <p class="text-sm text-gray-500">Get alerted when products are running low</p>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" class="sr-only settings-toggle-input" name="stock_alerts" id="stock-alerts">
                                        <label for="stock-alerts" class="settings-toggle-switch flex items-center h-6 w-11 cursor-pointer rounded-full bg-gray-300 transition-all duration-300">
                                            <span class="sr-only">Enable stock alerts</span>
                                            <span class="settings-toggle-thumb h-5 w-5 bg-white rounded-full transition-transform duration-300" style="transform: translateX(2px);"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Marketing Updates</h4>
                                        <p class="text-sm text-gray-500">Receive updates about new features and promotions</p>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" class="sr-only settings-toggle-input" name="marketing_updates" id="marketing-updates">
                                        <label for="marketing-updates" class="settings-toggle-switch flex items-center h-6 w-11 cursor-pointer rounded-full bg-gray-300 transition-all duration-300">
                                            <span class="sr-only">Enable marketing updates</span>
                                            <span class="settings-toggle-thumb h-5 w-5 bg-white rounded-full transition-transform duration-300" style="transform: translateX(2px);"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="settings-btn-hover bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-all duration-200 flex items-center">
                                    <span class="settings-btn-text">Save Changes</span>
                                    <i data-lucide="loader" class="settings-btn-loading w-4 h-4 ml-2 hidden"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Settings -->
                    <div id="security" class="settings-section settings-section-hidden bg-white rounded-lg shadow-sm border border-gray-200 settings-card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Security Settings</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Security settings content (no password update) -->
                            <div class="text-gray-500 text-sm">Password update is now managed in the profile page.</div>
                        </div>
                    </div>

                    <!-- Maintenance Settings -->
                    <div id="maintenance" class="settings-section settings-section-hidden bg-white rounded-lg shadow-sm border border-gray-200 settings-card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">System Maintenance</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="border border-gray-200 rounded-lg p-4 settings-card-hover">
                                    <h4 class="font-medium text-gray-900 mb-2">Clear Cache</h4>
                                    <p class="text-sm text-gray-500 mb-4">Clear application cache to improve performance</p>
                                    <button class="settings-btn-hover bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                        Clear Cache
                                    </button>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4 settings-card-hover">
                                    <h4 class="font-medium text-gray-900 mb-2">Database Backup</h4>
                                    <p class="text-sm text-gray-500 mb-4">Create a backup of your database</p>
                                    <button class="settings-btn-hover bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                                        Create Backup
                                    </button>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4 settings-card-hover">
                                    <h4 class="font-medium text-gray-900 mb-2">System Update</h4>
                                    <p class="text-sm text-gray-500 mb-4">Check for and install system updates</p>
                                    <button class="settings-btn-hover bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors">
                                        Check Updates
                                    </button>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4 settings-card-hover">
                                    <h4 class="font-medium text-gray-900 mb-2">System Health</h4>
                                    <p class="text-sm text-gray-500 mb-4">Check system performance and health</p>
                                    <button class="settings-btn-hover bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600 transition-colors">
                                        Run Diagnostics
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Logs Settings -->
                    <div id="system-logs" class="settings-section settings-section-hidden bg-white rounded-lg shadow-sm border border-gray-200 settings-card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">System Logs</h3>
                        </div>
                        <div class="p-6">
                            <div class="mb-4">
                                <select class="settings-input-focus px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option>All Logs</option>
                                    <option>Error Logs</option>
                                    <option>Access Logs</option>
                                    <option>Admin Actions</option>
                                </select>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 h-64 overflow-y-auto font-mono text-sm">
                                <div class="space-y-2">
                                    <div class="text-green-600">[2024-01-15 10:30:25] INFO: User login successful - admin@frozenfoods.com</div>
                                    <div class="text-blue-600">[2024-01-15 10:28:15] INFO: Settings updated - General settings</div>
                                    <div class="text-yellow-600">[2024-01-15 10:25:10] WARNING: Low stock alert - Product ID: 123</div>
                                    <div class="text-red-600">[2024-01-15 10:20:05] ERROR: Failed login attempt - invalid credentials</div>
                                    <div class="text-green-600">[2024-01-15 10:15:30] INFO: Order placed - Order ID: #ORD-2024-001</div>
                                    <div class="text-blue-600">[2024-01-15 10:10:20] INFO: Database backup completed successfully</div>
                                    <div class="text-green-600">[2024-01-15 10:05:15] INFO: Cache cleared by admin</div>
                                    <div class="text-yellow-600">[2024-01-15 10:00:00] WARNING: High server load detected</div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center mt-4">
                                <button class="text-orange-600 hover:text-orange-700 font-medium settings-btn-hover">
                                    Download Logs
                                </button>
                                <button class="text-red-600 hover:text-red-700 font-medium settings-btn-hover">
                                    Clear Logs
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <script src="js/script.js"></script>
    <script src="../assets/js/toast.js"></script>
    <script>
        // Settings Management - Vanilla JavaScript without classes
        let currentSettingsSection = 'general';

        // Initialize settings when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initSettingsNavigation();
            initSettingsForms();
            initSettingsToggleSwitches();
            loadSettingsFromServer();

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Navigation handling
        function initSettingsNavigation() {
            const navLinks = document.querySelectorAll('.settings-nav-link');

            navLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionId = link.getAttribute('data-section');
                    switchSettingsSection(sectionId);
                });
            });

            // Handle hash navigation
            if (window.location.hash) {
                const sectionId = window.location.hash.substring(1);
                switchSettingsSection(sectionId);
            }
        }

        // Switch between sections with animation
        function switchSettingsSection(sectionId) {
            if (sectionId === currentSettingsSection) return;

            const navLinks = document.querySelectorAll('.settings-nav-link');
            const targetSection = document.getElementById(sectionId);

            if (!targetSection) return;

            // Update navigation active state
            navLinks.forEach(function(link) {
                const isActive = link.getAttribute('data-section') === sectionId;
                if (isActive) {
                    link.classList.add('settings-nav-active');
                    link.classList.remove('text-gray-600');
                } else {
                    link.classList.remove('settings-nav-active');
                    link.classList.add('text-gray-600');
                }
            });

            // Hide current section with animation
            const currentSectionElement = document.getElementById(currentSettingsSection);
            if (currentSectionElement) {
                currentSectionElement.style.opacity = '0';
                currentSectionElement.style.transform = 'translateX(-20px)';

                setTimeout(function() {
                    currentSectionElement.classList.add('settings-section-hidden');

                    // Show target section with animation
                    targetSection.classList.remove('settings-section-hidden');
                    targetSection.style.opacity = '0';
                    targetSection.style.transform = 'translateX(20px)';

                    requestAnimationFrame(function() {
                        targetSection.style.opacity = '1';
                        targetSection.style.transform = 'translateX(0)';
                    });
                }, 150);
            } else {
                targetSection.classList.remove('settings-section-hidden');
                targetSection.style.opacity = '1';
                targetSection.style.transform = 'translateX(0)';
            }

            currentSettingsSection = sectionId;
            window.location.hash = '#' + sectionId;
        }

        // Form handling
        function initSettingsForms() {
            const forms = ['generalSettingsForm', 'notificationsSettingsForm', 'securitySettingsForm'];

            forms.forEach(function(formId) {
                const form = document.getElementById(formId);
                if (form) {
                    form.addEventListener('submit', handleSettingsFormSubmit);
                }
            });
        }

        // Handle form submission with async/await and section-specific payloads
        async function handleSettingsFormSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const button = form.querySelector('button[type="submit"]');
            const btnText = button.querySelector('.settings-btn-text');
            const btnLoading = button.querySelector('.settings-btn-loading');

            // Determine section based on form ID
            let section = '';
            if (form.id === 'generalSettingsForm') section = 'general';
            else if (form.id === 'notificationsSettingsForm') section = 'notifications';
            else if (form.id === 'securitySettingsForm') section = 'security';

            setSettingsLoadingState(button, btnText, btnLoading, true);

            // Prepare payload for only the relevant section
            const formData = new FormData(form);
            let payload = {};
            for (let [key, value] of formData.entries()) {
                payload[key] = value;
            }
            // For notification checkboxes, ensure boolean values
            if (section === 'notifications') {
                const checkboxes = form.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    payload[checkbox.name] = checkbox.checked;
                });
            }

            try {
                const response = await fetch('api/settings-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        section,
                        data: payload
                    })
                });
                const result = await response.json();
                if (result.success) {
                    showToasted(result.message, 'success');
                    if (section === 'security') form.reset();
                } else {
                    showToasted(result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToasted('An error occurred while saving settings', 'error');
            } finally {
                setSettingsLoadingState(button, btnText, btnLoading, false);
            }
        }

        // Loading state management
        function setSettingsLoadingState(button, btnText, btnLoading, isLoading) {
            button.disabled = isLoading;
            btnText.style.opacity = isLoading ? '0.7' : '1';

            if (btnLoading) {
                if (isLoading) {
                    btnLoading.classList.remove('hidden');
                } else {
                    btnLoading.classList.add('hidden');
                }
            }

            if (isLoading) {
                button.classList.add('opacity-75', 'cursor-not-allowed');
            } else {
                button.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        }

        // Toggle switches handling
        function initSettingsToggleSwitches() {
            const toggles = document.querySelectorAll('.settings-toggle-input');

            toggles.forEach(function(toggle) {
                const label = toggle.nextElementSibling;
                const thumb = label.querySelector('.settings-toggle-thumb');

                // Set initial state
                updateSettingsToggleState(toggle, label, thumb);

                // Add change listener
                toggle.addEventListener('change', function() {
                    updateSettingsToggleState(toggle, label, thumb);
                });
            });
        }

        // Update toggle state
        function updateSettingsToggleState(toggle, label, thumb) {
            if (toggle.checked) {
                label.classList.remove('bg-gray-300');
                label.classList.add('bg-orange-500');
                thumb.style.transform = 'translateX(20px)';
            } else {
                label.classList.remove('bg-orange-500');
                label.classList.add('bg-gray-300');
                thumb.style.transform = 'translateX(2px)';
            }
        }

        // Utility function to fetch settings from the server
        async function fetchSettingsData() {
            try {
                const response = await fetch('api/settings-handler.php', {
                    method: 'GET'
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const result = await response.json();
                if (result.success) {
                    return result.settings;
                } else {
                    throw new Error(result.message || 'Failed to load settings');
                }
            } catch (error) {
                console.error('Error fetching settings:', error);
                showToasted('Failed to load settings', 'error');
                return null;
            }
        }

        // Load settings from server and populate forms
        async function loadSettingsFromServer() {
            const settings = await fetchSettingsData();
            if (settings) {
                populateSettingsData(settings);
            }
        }

        // Populate form data
        function populateSettingsData(settings) {
            // Populate general form
            const generalForm = document.getElementById('generalSettingsForm');
            if (generalForm) {
                ['business_name', 'business_email', 'phone_number', 'business_address', 'timezone'].forEach(function(key) {
                    const input = generalForm.querySelector('[name="' + key + '"]');
                    if (input && settings[key] !== undefined) {
                        input.value = settings[key];
                    }
                });
            }

            // Update toggle switches for notifications
            const toggles = document.querySelectorAll('.settings-toggle-input');
            toggles.forEach(function(toggle) {
                const settingKey = toggle.name;
                if (settings.hasOwnProperty(settingKey)) {
                    toggle.checked = Boolean(settings[settingKey]);
                    const label = toggle.nextElementSibling;
                    const thumb = label.querySelector('.settings-toggle-thumb');
                    updateSettingsToggleState(toggle, label, thumb);
                }
            });
        }
    </script>
</body>

</html>