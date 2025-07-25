<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/util/utilities.php';
require __DIR__ . '/partials/headers.php';

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    header('Location: users.php');
    exit;
}

// Fetch user details
$user = getUserById($pdo, $user_id);
if (!$user) {
    header('Location: users.php');
    exit;
}

// Fetch user's recent orders
$userOrders = getUserOrders($pdo, $user_id, 5);
// fetch user wallet balance
$userWallet = getUserWallet($pdo, $user_id);

$orders = $userOrders['orders'];
?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>

        <!-- User Details Content -->
        <main class="p-6">
            <!-- Breadcrumb -->
            <div class="mb-8">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="dashboard.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-orange-600">
                                <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                                <a href="users.php" class="ml-1 text-sm font-medium text-gray-700 hover:text-orange-600 md:ml-2">Users</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- User Profile Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="relative">
                    <!-- Cover Photo -->
                    <div class="h-48 bg-gradient-to-r from-orange-500 via-orange-600 to-orange-700 rounded-t-lg relative overflow-hidden">
                        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        <button class="absolute top-4 right-4 bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-lg transition-all duration-200 backdrop-blur-sm">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Profile Info -->
                    <div class="relative px-6 py-4 pb-6">
                        <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-6">
                            <!-- Avatar -->
                            <div class="relative -mt-16 mb-4 sm:mb-0">
                                <img src="../assets/uploads/<?= htmlspecialchars($user['avatar']) ?>"
                                    alt="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>"
                                    class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                            </div>

                            <!-- User Info -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h1 class="text-2xl font-bold text-gray-900" id="fullName">
                                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                    </h1>
                                    <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <?= htmlspecialchars(ucwords($user['role'])) ?>
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i data-lucide="calendar" class="w-4 h-4 mr-1"></i>
                                        Admin since <?= date('M Y', strtotime($user['created_at'])) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center space-x-3 mt-4 sm:mt-0">
                                <button id="editProfileBtn" onclick="openEditUserModal()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center shadow-sm">
                                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                                    Edit Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Stats & Info Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- User Statistics -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Wallet Balance -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Wallet Balance</p>
                                    <p class="text-2xl font-bold text-orange-600">₦<?= number_format((float)$userWallet['balance'], 2) ?></p>
                                </div>
                                <div class="bg-orange-50 p-3 rounded-lg">
                                    <i data-lucide="wallet" class="w-6 h-6 text-orange-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total Spent -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Spent</p>
                                    <p class="text-2xl font-bold text-gray-500">₦<?= number_format((float)$userOrders['total_spent'], 2) ?></p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <i data-lucide="dollar-sign" class="w-6 h-6 text-gray-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total Orders -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                                    <p class="text-2xl font-bold text-blue-600"><?= (int)$userOrders['order_count'] ?></p>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <i data-lucide="shopping-bag" class="w-6 h-6 text-blue-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Recent Orders -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($userOrders)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                                <i data-lucide="shopping-bag" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                                <p>No orders found</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($orders as $order): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    #<?= htmlspecialchars($order['order_number']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    ₦<?= number_format((float)$order['total_amount'], 2) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        <?php
                                                        switch ($order['status']) {
                                                            case 'delivered':
                                                                echo 'bg-green-100 text-green-800';
                                                                break;
                                                            case 'pending':
                                                                echo 'bg-yellow-100 text-yellow-800';
                                                                break;
                                                            case 'cancelled':
                                                                echo 'bg-red-100 text-red-800';
                                                                break;
                                                            default:
                                                                echo 'bg-gray-100 text-gray-800';
                                                        }
                                                        ?>">
                                                        <?= htmlspecialchars(ucfirst($order['status'])) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- User Information Sidebar -->
                <div class="space-y-6">
                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i data-lucide="mail" class="w-5 h-5 text-gray-400 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['email']) ?></p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="phone" class="w-5 h-5 text-gray-400 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Phone</p>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="map-pin" class="w-5 h-5 text-gray-400 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Address</p>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['address'] ?? 'N/A') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Details -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Details</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">User ID</p>
                                <p class="text-sm font-medium text-gray-900">#<?= htmlspecialchars($user['id']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Last Login</p>
                                <p class="text-sm font-medium text-gray-900">
                                    <?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Verification status</p>
                                <p class="text-sm font-medium <?= $user['verified'] ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $user['verified'] ? 'Verified' : 'Not Verified' ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <button id="openEmailBtn" class="w-full text-left px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors flex items-center">
                                <i data-lucide="mail" class="w-4 h-4 mr-3 text-gray-400"></i>
                                Send Email
                            </button>
                            <button id="openPasswordBtn" class="w-full text-left px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors flex items-center">
                                <i data-lucide="lock" class="w-4 h-4 mr-3 text-gray-400"></i>
                                Reset Password
                            </button>
                            <button id="openStatusBtn" class="w-full text-left px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors flex items-center">
                                <i data-lucide="ban" class="w-4 h-4 mr-3 text-gray-400"></i>
                                <?= $user['status'] === 'Active' ? 'Suspend Account' : 'Activate Account' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="editModalContent">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Edit User</h2>
                        <p class="text-sm text-gray-600 mt-1">Update user information and settings</p>
                    </div>
                    <button id="closeEditModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="editUserForm" class="p-6 space-y-6" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                <!-- Profile Picture -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Profile Picture</h3>
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <img id="avatarPreview" src="../assets/uploads/<?= htmlspecialchars($user['avatar']) ?>"
                                alt="User avatar" class="w-20 h-20 rounded-full object-cover border border-gray-300">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Upload New Avatar</label>
                            <input type="file" name="avatar" accept="image/*" id="avatarInput"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 5MB</p>
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                        <textarea name="address" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 resize-none"
                            placeholder="Enter full address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Account Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                            <select name="role"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white">
                                <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select name="status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white">
                                <option value="Active" <?= $user['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= $user['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="Suspended" <?= $user['status'] === 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Email Verified</label>
                            <p class="text-xs text-gray-500">Mark this user's email as verified</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="verified" value="1" class="sr-only peer" <?= $user['verified'] ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 rounded-b-xl">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                        All fields marked with * are required
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" id="cancelEditBtn" class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Cancel
                        </button>
                        <button id="submitEditBtn" type="button" form="editUserForm" class="px-6 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all duration-200 font-medium hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="deleteModalContent">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                    <i data-lucide="trash-2" class="w-8 h-8 text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Delete User Account</h3>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Are you sure you want to delete <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>?
                    This action cannot be undone and will permanently remove all user data.
                </p>
                <div class="flex items-center space-x-3">
                    <button id="cancelDeleteBtn" class="flex-1 px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                        Cancel
                    </button>
                    <button id="confirmDeleteBtn" class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200 font-medium">
                        Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Email Modal -->
    <div id="sendEmailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-xl w-full transform transition-all duration-300 scale-95 opacity-0" id="emailModalContent">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Send Email</h3>
                    <button class="text-gray-400 hover:text-gray-600" onclick="closeEmailModal()">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <form id="sendEmailForm" class="space-y-4">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" name="subject" placeholder="Email subject" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea name="message" rows="5" placeholder="Your message body goes here..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeEmailModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" onclick="sendEmailBtn()" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full transform transition-all duration-300 scale-95 opacity-0" id="resetModalContent">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Reset Password</h3>
                    <button class="text-gray-400 hover:text-gray-600" onclick="closeResetModal()">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mb-4">Are you sure you want to reset this user’s password? A temporary password will be emailed to them.</p>
                <div class="flex justify-end gap-2">
                    <button onclick="closeResetModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button id="confirmResetBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspend/Activate Account Modal -->
    <div id="statusToggleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full transform transition-all duration-300 scale-95 opacity-0" id="statusModalContent">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-900"><?= $user['status'] === 'Active' ? 'Suspend Account' : 'Activate Account' ?></h3>
                    <button class="text-gray-400 hover:text-gray-600" onclick="closeStatusModal()">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    Are you sure you want to <?= strtolower($user['status'] === 'Active' ? 'suspend' : 'activate') ?> <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>'s account?
                </p>
                <div class="flex justify-end gap-2">
                    <button onclick="closeStatusModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button id="toggleStatusBtn" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600"><?= $user['status'] === 'Active' ? 'Suspend' : 'Activate' ?></button>
                </div>
            </div>
        </div>
    </div>


    <!-- Overlay for mobile sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    <script src="js/script.js"></script>
    <script src="../assets/js/confirmation-modal.js"></script>
    <script src="../assets/js/loading-overlay.js"></script>
    <script src="../assets/js/toast.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal elements
            const editUserModal = document.getElementById('editUserModal');
            const editModalContent = document.getElementById('editModalContent');
            const closeEditModalBtn = document.getElementById('closeEditModalBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const editUserForm = document.getElementById('editUserForm');
            const avatarInput = document.getElementById('avatarInput');
            const avatarPreview = document.getElementById('avatarPreview');

            const deleteConfirmModal = document.getElementById('deleteConfirmModal');
            const deleteModalContent = document.getElementById('deleteModalContent');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            // Function to open edit user modal
            window.openEditUserModal = function() {
                editUserModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                setTimeout(() => {
                    editModalContent.classList.remove('scale-95', 'opacity-0');
                    editModalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            };

            // Function to close edit modal
            function closeEditModal() {
                editModalContent.classList.remove('scale-100', 'opacity-100');
                editModalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    editUserModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 300);
            }

            // Function to open delete confirmation modal
            window.openDeleteConfirmModal = function() {
                deleteConfirmModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                setTimeout(() => {
                    deleteModalContent.classList.remove('scale-95', 'opacity-0');
                    deleteModalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            };

            // Function to close delete modal
            function closeDeleteModal() {
                deleteModalContent.classList.remove('scale-100', 'opacity-100');
                deleteModalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    deleteConfirmModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 300);
            }

            // Close modal events
            closeEditModalBtn.addEventListener('click', closeEditModal);
            cancelEditBtn.addEventListener('click', closeEditModal);
            cancelDeleteBtn.addEventListener('click', closeDeleteModal);

            // Close modals when clicking outside
            editUserModal.addEventListener('click', (e) => {
                if (e.target === editUserModal) {
                    closeEditModal();
                }
            });

            deleteConfirmModal.addEventListener('click', (e) => {
                if (e.target === deleteConfirmModal) {
                    closeDeleteModal();
                }
            });

            // Close modals with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (!editUserModal.classList.contains('hidden')) {
                        closeEditModal();
                    }
                    if (!deleteConfirmModal.classList.contains('hidden')) {
                        closeDeleteModal();
                    }
                }
            });

            // Avatar preview functionality
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        showToasted('File size too large. Maximum size is 5MB.', 'error');
                        this.value = '';
                        return;
                    }

                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        showToasted('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.', 'error');
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Edit form submission
            editUserForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(editUserForm);
                const submitBtn = document.getElementById('submitEditBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.addEventListener('click', alert('Submitting edit form'));

                // Show loading state
                submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Saving...';
                submitBtn.disabled = true;

                try {
                    const response = await fetch('api/update-user.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        showToasted('User updated successfully!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToasted(result.message || 'Failed to update user', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToasted('An error occurred while updating the user', 'error');
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });

            // Delete confirmation
            confirmDeleteBtn.addEventListener('click', async () => {
                const originalText = confirmDeleteBtn.textContent;
                confirmDeleteBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Deleting...';
                confirmDeleteBtn.disabled = true;

                try {
                    const response = await fetch('api/delete-user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            user_id: <?= $user['id'] ?>
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        showToasted('User deleted successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = 'users.php';
                        }, 1500);
                    } else {
                        showToasted(result.message || 'Failed to delete user', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToasted('An error occurred while deleting the user', 'error');
                } finally {
                    confirmDeleteBtn.textContent = originalText;
                    confirmDeleteBtn.disabled = false;
                }
            });

            // Form validation
            const requiredFields = editUserForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.classList.add('border-red-500');
                        this.classList.remove('border-gray-300');
                    } else {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-300');
                    }
                });

                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-300');
                    }
                });
            });

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Email Modal
        const sendEmailModal = document.getElementById('sendEmailModal');
        const emailModalContent = document.getElementById('emailModalContent');
        document.getElementById('openEmailBtn').onclick = () => {
            sendEmailModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                emailModalContent.classList.remove('scale-95', 'opacity-0');
                emailModalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        function closeEmailModal() {
            emailModalContent.classList.remove('scale-100', 'opacity-100');
            emailModalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                sendEmailModal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }


        // Send Email Function
        async function sendEmailBtn() {
            e.preventDefault();
            const form = document.getElementById('sendEmailForm');
            const overlay = document.getElementById('overlay');

            // Collect form data
            const formData = new FormData(form);
            const userId = formData.get('user_id');
            const subject = formData.get('subject').trim();
            const message = formData.get('message').trim();

            console.log('Sending email to user ID:', userId);

            // Simple validation
            if (!subject || !message) {
                showToasted("Subject and message are required.", "error");
                return;
            }

            // Show overlay
            overlay.classList.remove('hidden');

            try {
                const response = await fetch('api/send-email.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        subject: subject,
                        message: message
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showToasted(result.message || 'Email sent successfully.', 'success');
                    closeEmailModal();
                    form.reset();
                } else {
                    showToasted(result.message || 'Failed to send email.', 'error');
                }
            } catch (error) {
                console.error('Error sending email:', error);
                showToasted('An error occurred while sending the email.', 'error');
            } finally {
                // Hide overlay
                overlay.classList.add('hidden');
            }
        }


        // Reset Modal
        const resetPasswordModal = document.getElementById('resetPasswordModal');
        const resetModalContent = document.getElementById('resetModalContent');
        document.getElementById("openPasswordBtn").onclick = () => {
            resetPasswordModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                resetModalContent.classList.remove('scale-95', 'opacity-0');
                resetModalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        function closeResetModal() {
            resetModalContent.classList.remove('scale-100', 'opacity-100');
            resetModalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                resetPasswordModal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        document.getElementById("resetForm").addEventListener("submit", async function(e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector("button[type='submit']");
            const original = submitBtn.innerHTML;

            submitBtn.innerHTML = `<i data-lucide="loader-2" class="animate-spin w-4 h-4 mr-2"></i>Resetting...`;
            submitBtn.disabled = true;

            try {
                const formData = new FormData(form);
                const res = await fetch('api/reset-password.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showToasted("Password reset successfully!", "success");
                    closeResetModal();
                } else {
                    showToasted(data.message || "Failed to reset password", "error");
                }
            } catch (error) {
                showToasted("Something went wrong while resetting password", "error");
            } finally {
                submitBtn.innerHTML = original;
                submitBtn.disabled = false;
            }
        });

        // Status Modal
        const statusToggleModal = document.getElementById('statusToggleModal');
        const statusModalContent = document.getElementById('statusModalContent');
        document.getElementById("openStatusBtn").onclick = () => {
            statusToggleModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                statusModalContent.classList.remove('scale-95', 'opacity-0');
                statusModalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        function closeStatusModal() {
            statusModalContent.classList.remove('scale-100', 'opacity-100');
            statusModalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                statusToggleModal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        document.getElementById("statusForm").addEventListener("submit", async function(e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector("button[type='submit']");
            const original = submitBtn.innerHTML;

            submitBtn.innerHTML = `<i data-lucide="loader-2" class="animate-spin w-4 h-4 mr-2"></i>Updating...`;
            submitBtn.disabled = true;

            try {
                const formData = new FormData(form);
                const res = await fetch('api/toggle-status.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showToasted("User status updated!", "success");
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToasted(data.message || "Failed to update status", "error");
                }
            } catch (error) {
                showToasted("Something went wrong while updating status", "error");
            } finally {
                submitBtn.innerHTML = original;
                submitBtn.disabled = false;
            }
        });
    </script>


</body>

</html>