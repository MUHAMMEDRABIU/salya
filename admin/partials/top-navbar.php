<?php
require_once __DIR__ . '/../../config/constants.php';

// Generate admin avatar URL using constants
$adminAvatarFile = $admin['avatar'] ?? DEFAULT_ADMIN_AVATAR;
$adminAvatarUrl = ADMIN_AVATAR_URL . htmlspecialchars($adminAvatarFile);

// Full admin name
$adminFullName = htmlspecialchars(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? ''));
$adminEmail = htmlspecialchars($admin['email'] ?? '');
?>
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-6">
        <div class="flex items-center">
            <button id="menuToggle" class="lg:hidden mr-4 text-gray-600 hover:text-gray-900">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-xl font-semibold text-gray-800"><?php echo $page_title ?? 'Dashboard'; ?></h1>
        </div>

        <div class="flex items-center space-x-4">
            <div class="relative hidden md:block">
                <input type="text" placeholder="Search <?php echo strtolower($page_title ?? 'dashboard'); ?>..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <i data-lucide="search" class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"></i>
            </div>

            <button class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors" onclick="window.location.href='notifications.php'">
                <i data-lucide="bell" class="w-6 h-6"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
            </button>

            <!-- User Dropdown -->
            <div class="relative">
                <button id="userDropdown" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                    <img id="navbarAvatar" src="<?php echo $adminAvatarUrl; ?>" alt="User" class="w-8 h-8 rounded-full">
                    <span id="navbarName" class="hidden md:block text-sm font-medium text-gray-700">
                        <?php echo $adminFullName; ?>
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 transition-transform duration-200" id="dropdownIcon"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="userDropdownMenu" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50 opacity-0 invisible transform scale-95 transition-all duration-200 origin-top-right">
                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <img id="navbarDropdownAvatar" src="<?php echo $adminAvatarUrl; ?>" alt="User" class="w-10 h-10 rounded-full">
                            <div>
                                <p id="navbarDropdownName" class="text-sm font-medium text-gray-900">
                                    <?php echo $adminFullName; ?>
                                </p>
                                <p id="navbarDropdownEmail" class="text-xs text-gray-400">
                                    <?php echo $adminEmail; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-1">
                        <a href="profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="user" class="w-4 h-4 mr-3 text-gray-400"></i>
                            My Profile
                        </a>
                        <a href="settings.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="settings" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Account Settings
                        </a>
                        <a href="preferences.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="sliders" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Preferences
                        </a>
                    </div>

                    <div class="border-t border-gray-100 py-1">
                        <a href="help.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="help-circle" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Help & Support
                        </a>
                        <a href="activity-log.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="activity" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Activity Log
                        </a>
                    </div>

                    <div class="border-t border-gray-100 py-1">
                        <button onclick="handleSignOut()" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                            Sign Out
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<div id="signOutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden backdrop-blur-sm">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Sign Out</h2>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to sign out?</p>
        <div class="flex justify-end space-x-4">
            <button id="cancelSignOut" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancel</button>
            <button id="confirmSignOut" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Sign Out</button>
        </div>
    </div>
</div>