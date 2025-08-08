<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 p-6 rounded-t-3xl z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Edit Profile</h3>
                <button onclick="closeModal('editProfileModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <form id="editProfileForm" class="p-6 space-y-6">
            <!-- Avatar Section -->
            <div class="text-center">
                <div class="relative inline-block">
                    <?php if (!empty($user['avatar']) && $user['avatar'] !== DEFAULT_USER_AVATAR): ?>
                        <img src="<?php echo USER_AVATAR_URL . htmlspecialchars($user['avatar']); ?>"
                            alt="Profile"
                            class="w-20 h-20 rounded-full object-cover border-4 border-orange-200"
                            onerror="this.src='<?php echo USER_AVATAR_URL . DEFAULT_USER_AVATAR; ?>';">
                    <?php else: ?>
                        <div class="w-20 h-20 rounded-full bg-orange-100 flex items-center justify-center border-4 border-orange-200">
                            <i class="fas fa-user text-orange-500 text-2xl"></i>
                        </div>
                    <?php endif; ?>
                    <button type="button" onclick="openAvatarUpload()" class="absolute bottom-0 right-0 w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center shadow-lg hover:bg-orange-600 transition-colors">
                        <i class="fas fa-camera text-white text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                    <input type="text" id="editFirstName" name="first_name"
                        value="<?php echo htmlspecialchars($user['first_name']); ?>"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                    <input type="text" id="editLastName" name="last_name"
                        value="<?php echo htmlspecialchars($user['last_name']); ?>"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                <input type="email" id="editEmail" name="email"
                    value="<?php echo htmlspecialchars($user['email']); ?>"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                    required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                <input type="tel" id="editPhone" name="phone"
                    value="<?php echo htmlspecialchars($user['phone']); ?>"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="button" onclick="closeModal('editProfileModal')"
                    class="w-full px-6 py-3 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="w-full px-6 py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition-colors font-semibold">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div id="avatarUploadModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Update Profile Picture</h3>
                <button onclick="closeModal('avatarUploadModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Avatar Upload Modal - Preview Section -->
            <div class="text-center">
                <div class="w-32 h-32 mx-auto mb-4 relative">
                    <div id="avatarPreview" class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200 overflow-hidden">
                        <?php if (!empty($user['avatar']) && $user['avatar'] !== DEFAULT_USER_AVATAR): ?>
                            <img src="<?php echo USER_AVATAR_URL . htmlspecialchars($user['avatar']); ?>"
                                alt="Profile"
                                class="w-full h-full object-cover"
                                onerror="this.src='<?php echo USER_AVATAR_URL . DEFAULT_USER_AVATAR; ?>';">
                        <?php else: ?>
                            <i class="fas fa-user text-gray-400 text-4xl"></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <input type="file" id="avatarInput" accept="image/*" class="hidden" onchange="previewAvatar(this)">

                <button type="button" onclick="document.getElementById('avatarInput').click()"
                    class="w-full px-6 py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors flex items-center justify-center">
                    <i class="fas fa-upload mr-2"></i>
                    Choose New Photo
                </button>

                <button type="button" onclick="removeAvatar()"
                    class="w-full px-6 py-3 bg-red-50 text-red-600 rounded-xl font-semibold hover:bg-red-100 transition-colors">
                    <i class="fas fa-trash mr-2"></i>
                    Remove Photo
                </button>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="button" onclick="closeModal('avatarUploadModal')"
                    class="w-full px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="uploadAvatar()"
                    class="w-full px-6 py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors">
                    Save Photo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Address Management Modal -->
<div id="addressModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 p-6 rounded-t-3xl z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Delivery Addresses</h3>
                <button onclick="closeModal('addressModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="flex justify-between items-center mb-6" id="addressHeader">
                <p class="text-gray-600">Manage your saved delivery addresses</p>
                <button onclick="openAddAddressForm()" id="addAddressBtn"
                    class="px-4 py-2 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add New
                </button>
            </div>

            <div id="addressesList" class="space-y-4">
                <?php if (empty($addresses)): ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-map-marker-alt text-gray-400 text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">No Addresses Added</h4>
                        <p class="text-gray-500 text-sm mb-4">Add your first delivery address to get started</p>
                        <button onclick="openAddAddressForm()"
                            class="px-6 py-2 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors">
                            Add Address
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($addresses as $address): ?>
                        <div class="p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h5 class="font-semibold text-gray-900"><?php echo htmlspecialchars($address['address_name']); ?></h5>
                                        <?php if ($address['is_default']): ?>
                                            <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs rounded-full">Default</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($address['full_address']); ?></p>
                                    <p class="text-gray-500 text-xs mt-1"><?php echo htmlspecialchars($address['city'] . ', ' . $address['state']); ?></p>
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <button onclick="editAddress(<?php echo $address['id']; ?>)"
                                        class="p-2 text-gray-500 hover:text-orange-500 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if (!$address['is_default']): ?>
                                        <button onclick="deleteAddress(<?php echo $address['id']; ?>)"
                                            class="p-2 text-gray-500 hover:text-red-500 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if (!$address['is_default']): ?>
                                        <button onclick="setDefaultAddress(<?php echo $address['id']; ?>)"
                                            class="px-3 py-1 text-xs border border-orange-300 text-orange-600 rounded-lg hover:bg-orange-50 transition-colors">
                                            Set Default
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Add Address Form (Hidden by default) -->
            <div id="addAddressForm" class="hidden mt-6 p-4 bg-gray-50 rounded-xl">
                <h4 class="font-semibold text-gray-900 mb-4">Add New Address</h4>
                <form class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Name *</label>
                            <input type="text" id="newAddressName" placeholder="e.g., Home, Office"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-orange-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                            <select id="newState" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-orange-400" required>
                                <option value="">Select State</option>
                                <option value="Federal Capital Territory">Federal Capital Territory</option>
                                <option value="Lagos">Lagos</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <input type="text" id="newCity" placeholder="e.g., Abuja, Lagos"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-orange-400" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Address *</label>
                        <textarea id="newFullAddress" rows="3" placeholder="House number, street, area, landmark..."
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 resize-none" required></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="setAsDefault" class="mr-2">
                        <label for="setAsDefault" class="text-sm text-gray-700">Set as default address</label>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="cancelAddAddress()"
                            class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="button" onclick="saveNewAddress()"
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                            Save Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Security Modal -->
<div id="securityModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Security Settings</h3>
                <button onclick="closeModal('securityModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-red-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Change Password</h4>
                <p class="text-gray-500 text-sm">Keep your account secure with a strong password</p>
            </div>

            <form id="passwordChangeForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password *</label>
                    <input type="password" id="currentPassword"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">New Password *</label>
                    <input type="password" id="newPassword"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        required minlength="6">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password *</label>
                    <input type="password" id="confirmPassword"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        required minlength="6">
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="button" onclick="closeModal('securityModal')"
                        class="w-full px-6 py-3 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="w-full px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors font-semibold">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Language Modal -->
<div id="languageModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Choose Language</h3>
                <button onclick="closeModal('languageModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-3">
                <div class="language-option p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors cursor-pointer" data-lang="en">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🇺🇸</span>
                            <div>
                                <p class="font-semibold text-gray-900">English (US)</p>
                                <p class="text-gray-500 text-sm">Default language</p>
                            </div>
                        </div>
                        <div class="radio-check <?php echo $preferences['language'] === 'en' ? 'active' : ''; ?>">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>

                <div class="language-option p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors cursor-pointer" data-lang="yo">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🇳🇬</span>
                            <div>
                                <p class="font-semibold text-gray-900">Yoruba</p>
                                <p class="text-gray-500 text-sm">Ede Yoruba</p>
                            </div>
                        </div>
                        <div class="radio-check <?php echo $preferences['language'] === 'yo' ? 'active' : ''; ?>">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>

                <div class="language-option p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors cursor-pointer" data-lang="ha">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🇳🇬</span>
                            <div>
                                <p class="font-semibold text-gray-900">Hausa</p>
                                <p class="text-gray-500 text-sm">Harshen Hausa</p>
                            </div>
                        </div>
                        <div class="radio-check <?php echo $preferences['language'] === 'ha' ? 'active' : ''; ?>">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>

                <div class="language-option p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors cursor-pointer" data-lang="ig">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🇳🇬</span>
                            <div>
                                <p class="font-semibold text-gray-900">Igbo</p>
                                <p class="text-gray-500 text-sm">Asụsụ Igbo</p>
                            </div>
                        </div>
                        <div class="radio-check <?php echo $preferences['language'] === 'ig' ? 'active' : ''; ?>">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-6">
                <button type="button" onclick="closeModal('languageModal')"
                    class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="saveLanguagePreference()"
                    class="flex-1 px-6 py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Methods Modal -->
<div id="paymentModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Payment Methods</h3>
                <button onclick="closeModal('paymentModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-purple-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">No Payment Methods</h4>
                <p class="text-gray-500 text-sm mb-6">You can add payment methods during checkout for faster payments in the future.</p>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-mobile-alt text-green-600"></i>
                            </div>
                            <span class="font-medium text-gray-900">Mobile Money</span>
                        </div>
                        <span class="text-green-600 text-sm font-semibold">Available</span>
                    </div>

                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-university text-blue-600"></i>
                            </div>
                            <span class="font-medium text-gray-900">Bank Transfer</span>
                        </div>
                        <span class="text-blue-600 text-sm font-semibold">Available</span>
                    </div>

                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-credit-card text-purple-600"></i>
                            </div>
                            <span class="font-medium text-gray-900">Card Payment</span>
                        </div>
                        <span class="text-purple-600 text-sm font-semibold">Available</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div id="helpModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 p-6 rounded-t-3xl z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Help Center</h3>
                <button onclick="closeModal('helpModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <div class="help-item p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors cursor-pointer" onclick="toggleHelpItem(this)">
                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-gray-900">How do I place an order?</h4>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </div>
                    <div class="help-content hidden mt-3 text-gray-600 text-sm">
                        <p>1. Browse our products and add items to your cart</p>
                        <p>2. Go to checkout and enter your delivery information</p>
                        <p>3. Choose your payment method and complete payment</p>
                        <p>4. You'll receive a confirmation and tracking information</p>
                    </div>
                </div>

                <div class="help-item p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors cursor-pointer" onclick="toggleHelpItem(this)">
                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-gray-900">What are your delivery areas?</h4>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </div>
                    <div class="help-content hidden mt-3 text-gray-600 text-sm">
                        <p>We currently deliver to major cities in Nigeria including:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Lagos State</li>
                            <li>Abuja (FCT)</li>
                            <li>Kano State</li>
                            <li>Rivers State</li>
                            <li>Oyo State</li>
                        </ul>
                    </div>
                </div>

                <div class="help-item p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors cursor-pointer" onclick="toggleHelpItem(this)">
                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-gray-900">How do I track my order?</h4>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </div>
                    <div class="help-content hidden mt-3 text-gray-600 text-sm">
                        <p>You can track your order in several ways:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Visit the "My Orders" section in your profile</li>
                            <li>Use the tracking link sent to your email</li>
                            <li>Check your SMS notifications for updates</li>
                        </ul>
                    </div>
                </div>

                <div class="help-item p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors cursor-pointer" onclick="toggleHelpItem(this)">
                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-gray-900">What payment methods do you accept?</h4>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </div>
                    <div class="help-content hidden mt-3 text-gray-600 text-sm">
                        <p>We accept various payment methods:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Debit/Credit Cards (Visa, Mastercard)</li>
                            <li>Mobile Money (MTN, Airtel, Glo, 9mobile)</li>
                            <li>Bank Transfer</li>
                            <li>USSD Payments</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-orange-50 rounded-xl">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-headset text-orange-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Still need help?</h4>
                        <p class="text-gray-600 text-sm">Contact our support team</p>
                    </div>
                </div>
                <button onclick="closeModal('helpModal'); openContactModal();" class="w-full mt-3 px-4 py-2 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors">
                    Contact Support
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div id="contactModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Contact Support</h3>
                <button onclick="closeModal('contactModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-pink-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Get in Touch</h4>
                <p class="text-gray-500 text-sm">Choose how you'd like to contact us</p>
            </div>

            <div class="space-y-3">
                <a href="tel:+2349012345678" class="contact-option flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-phone text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Call Us</p>
                        <p class="text-gray-500 text-sm">+234 901 234 5678</p>
                    </div>
                </a>

                <a href="mailto:support@salya.com" class="contact-option flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-envelope text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Email Us</p>
                        <p class="text-gray-500 text-sm">support@salya.com</p>
                    </div>
                </a>

                <a href="https://wa.me/2349012345678" target="_blank" class="contact-option flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-colors">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fab fa-whatsapp text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">WhatsApp</p>
                        <p class="text-gray-500 text-sm">Chat with us</p>
                    </div>
                </a>
            </div>

            <div class="text-center text-gray-500 text-xs">
                <p>Support hours: Monday - Friday, 9:00 AM - 6:00 PM</p>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Send Feedback</h3>
                <button onclick="closeModal('feedbackModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <form id="feedbackForm" class="p-6 space-y-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comment-alt text-blue-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">We Value Your Feedback</h4>
                <p class="text-gray-500 text-sm">Help us improve your experience</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Feedback Type</label>
                <select id="feedbackType" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    <option value="">Select type</option>
                    <option value="bug">Bug Report</option>
                    <option value="suggestion">Suggestion</option>
                    <option value="complaint">Complaint</option>
                    <option value="praise">Praise</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Your Message</label>
                <textarea id="feedbackMessage" rows="4" placeholder="Tell us what you think..."
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"></textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="closeModal('feedbackModal')"
                    class="w-full px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="w-full px-6 py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors">
                    Send Feedback
                </button>
            </div>
        </form>
    </div>
</div>

<!-- About Modal -->
<div id="aboutModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 p-6 rounded-t-3xl z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">About Salya</h3>
                <button onclick="closeModal('aboutModal')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="text-center">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-snowflake text-orange-600 text-3xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-2">Salya</h4>
                <p class="text-gray-500 text-sm">Fresh Frozen Foods Delivery</p>
                <p class="text-gray-400 text-xs mt-2">Version 1.0.0</p>
            </div>

            <div class="space-y-4 text-center">
                <p class="text-gray-600 text-sm">
                    Salya is your trusted partner for premium frozen foods delivery across Nigeria.
                    We bring you the freshest frozen seafood, meat, and specialty items right to your doorstep.
                </p>

                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <h5 class="font-semibold text-gray-900 text-sm">Founded</h5>
                        <p class="text-orange-500 font-bold">2024</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <h5 class="font-semibold text-gray-900 text-sm">Cities</h5>
                        <p class="text-orange-500 font-bold">5+</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h5 class="font-semibold text-gray-900 mb-3">Our Mission</h5>
                    <p class="text-gray-600 text-sm">
                        To make premium frozen foods accessible to everyone across Nigeria,
                        ensuring quality, freshness, and convenience in every delivery.
                    </p>
                </div>

                <div class="space-y-2 text-xs text-gray-400">
                    <p>© 2024 Salya. All rights reserved.</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="hover:text-orange-500">Privacy Policy</a>
                        <a href="#" class="hover:text-orange-500">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sign Out Confirmation Modal -->
<div id="signOutModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-3xl max-w-sm w-full">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Sign Out</h3>
            <p class="text-gray-500 text-sm mb-6">Are you sure you want to sign out of your account?</p>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="closeModal('signOutModal')"
                    class="w-full px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="signOut()"
                    class="w-full px-6 py-3 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Sign Out
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Edit Profile Form Handler
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {
            first_name: document.getElementById('editFirstName').value.trim(),
            last_name: document.getElementById('editLastName').value.trim(),
            email: document.getElementById('editEmail').value.trim(),
            phone: document.getElementById('editPhone').value.trim()
        };

        // Validation
        if (!formData.first_name || !formData.last_name || !formData.email) {
            showToasted('Please fill in all required fields', 'error');
            return;
        }

        if (!isValidEmail(formData.email)) {
            showToasted('Please enter a valid email address', 'error');
            return;
        }

        updateProfile(formData);
    });

    // Password Change Form Handler
    document.getElementById('passwordChangeForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Validation
        if (!currentPassword || !newPassword || !confirmPassword) {
            showToasted('Please fill in all password fields', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            showToasted('New passwords do not match', 'error');
            return;
        }

        if (newPassword.length < 6) {
            showToasted('New password must be at least 6 characters long', 'error');
            return;
        }

        changePassword(currentPassword, newPassword);
    });

    // Feedback Form Handler
    document.getElementById('feedbackForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const feedbackType = document.getElementById('feedbackType').value;
        const message = document.getElementById('feedbackMessage').value.trim();

        if (!feedbackType || !message) {
            showToasted('Please fill in all fields', 'error');
            return;
        }

        submitFeedback(feedbackType, message);
    });


    // Constants from PHP
    const USER_AVATAR_URL = '<?php echo USER_AVATAR_URL; ?>';
    const DEFAULT_USER_AVATAR = '<?php echo DEFAULT_USER_AVATAR; ?>';

    // Preview image when file is selected
    function previewAvatar(input) {
        const preview = document.getElementById('avatarPreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Upload avatar
    async function uploadAvatar() {
        const fileInput = document.getElementById('avatarInput');
        const file = fileInput.files[0];

        if (!file) {
            showToasted('Please select a file first', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('avatar', file);

        try {
            showToasted('Uploading avatar...', 'info');

            const response = await fetch('api/upload-avatar.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Update avatar throughout the page
                updateAvatarDisplay(USER_AVATAR_URL + result.avatar_filename);

                closeModal('avatarUploadModal');
                resetAvatarForm();
                showToasted('Avatar updated successfully!', 'success');
            } else {
                throw new Error(result.message || 'Failed to upload avatar');
            }
        } catch (error) {
            console.error('Avatar upload error:', error);
            showToasted('Failed to upload avatar', 'error');
        }
    }

    // Remove avatar
    async function removeAvatar() {
        if (!confirm('Are you sure you want to remove your avatar?')) return;

        try {
            showToasted('Removing avatar...', 'info');

            const response = await fetch('api/remove-avatar.php', {
                method: 'POST'
            });

            const result = await response.json();

            if (result.success) {
                // Reset to default avatar
                updateAvatarDisplay(null);

                closeModal('avatarUploadModal');
                showToasted('Avatar removed successfully!', 'success');
            } else {
                throw new Error(result.message || 'Failed to remove avatar');
            }
        } catch (error) {
            console.error('Remove avatar error:', error);
            showToasted('Failed to remove avatar', 'error');
        }
    }

    // Update avatar display throughout the page
    function updateAvatarDisplay(avatarUrl) {
        const avatarImages = document.querySelectorAll('.profile-avatar img, .user-avatar');
        const avatarContainers = document.querySelectorAll('.profile-avatar');

        if (avatarUrl) {
            // Update existing image elements
            avatarImages.forEach(img => {
                img.src = avatarUrl + '?t=' + Date.now(); // Add timestamp to prevent caching
                img.style.display = 'block';
                img.onerror = function() {
                    this.src = USER_AVATAR_URL + DEFAULT_USER_AVATAR;
                };
            });

            // Update preview in modals
            const editModalAvatar = document.querySelector('#editProfileModal .relative img');
            if (editModalAvatar) {
                editModalAvatar.src = avatarUrl + '?t=' + Date.now();
            }

            const uploadModalPreview = document.querySelector('#avatarPreview');
            if (uploadModalPreview) {
                uploadModalPreview.innerHTML = `<img src="${avatarUrl}?t=${Date.now()}" alt="Profile" class="w-full h-full object-cover">`;
            }
        } else {
            // Revert to default avatar
            avatarContainers.forEach(container => {
                const avatarSection = container.querySelector('.relative');
                if (avatarSection) {
                    avatarSection.innerHTML = `
                        <div class="w-20 h-20 rounded-full bg-orange-100 flex items-center justify-center border-4 border-orange-200">
                            <i class="fas fa-user text-orange-500 text-2xl"></i>
                        </div>
                        <button type="button" onclick="openAvatarUpload()" class="absolute bottom-0 right-0 w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center shadow-lg hover:bg-orange-600 transition-colors">
                            <i class="fas fa-camera text-white text-xs"></i>
                        </button>
                    `;
                }
            });

            // Update modals
            const editModalAvatar = document.querySelector('#editProfileModal .relative');
            if (editModalAvatar) {
                editModalAvatar.innerHTML = `
                    <div class="w-20 h-20 rounded-full bg-orange-100 flex items-center justify-center border-4 border-orange-200">
                        <i class="fas fa-user text-orange-500 text-2xl"></i>
                    </div>
                    <button type="button" onclick="openAvatarUpload()" class="absolute bottom-0 right-0 w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center shadow-lg hover:bg-orange-600 transition-colors">
                        <i class="fas fa-camera text-white text-xs"></i>
                    </button>
                `;
            }

            const uploadModalPreview = document.querySelector('#avatarPreview');
            if (uploadModalPreview) {
                uploadModalPreview.innerHTML = `<i class="fas fa-user text-gray-400 text-4xl"></i>`;
            }
        }
    }

    // Reset avatar upload form
    function resetAvatarForm() {
        const fileInput = document.getElementById('avatarInput');
        const preview = document.getElementById('avatarPreview');

        if (fileInput) {
            fileInput.value = '';
        }

        if (preview) {
            // Reset to current user avatar or default
            const currentAvatar = '<?php echo !empty($user["avatar"]) && $user["avatar"] !== DEFAULT_USER_AVATAR ? USER_AVATAR_URL . htmlspecialchars($user["avatar"]) : ""; ?>';

            if (currentAvatar) {
                preview.innerHTML = `<img src="${currentAvatar}" alt="Profile" class="w-full h-full object-cover">`;
            } else {
                preview.innerHTML = `<i class="fas fa-user text-gray-400 text-4xl"></i>`;
            }
        }
    }

    // File drag and drop functionality
    function setupAvatarDragDrop() {
        const dropZone = document.getElementById('avatarDropZone');
        const fileInput = document.getElementById('avatarFile');

        if (!dropZone || !fileInput) return;

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-orange-500', 'bg-orange-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-orange-500', 'bg-orange-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-orange-500', 'bg-orange-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                previewAvatar(fileInput);
            }
        });
    }

    // Initialize avatar functionality when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        setupAvatarDragDrop();

        // Set up proper error handling for existing avatar images
        const avatarImages = document.querySelectorAll('img[src*="avatar"], img[src*="profile"]');
        avatarImages.forEach(img => {
            img.onerror = function() {
                this.src = USER_AVATAR_URL + DEFAULT_USER_AVATAR;
            };
        });
    });


    // Address functions
    function openAddAddressForm() {
        document.getElementById('addAddressForm').classList.remove('hidden');
        document.getElementById('addAddressBtn').style.display = 'none';

        // Reset form
        document.getElementById('addAddressForm').querySelectorAll('input, textarea, select').forEach(el => {
            if (el.type === 'checkbox') {
                el.checked = false;
            } else {
                el.value = '';
            }
        });

        // Update button for adding mode
        const saveBtn = document.querySelector('#addAddressForm button[onclick*="save"]');
        saveBtn.textContent = 'Save Address';
        saveBtn.setAttribute('onclick', 'saveNewAddress()');
    }

    function cancelAddAddress() {
        document.getElementById('addAddressForm').classList.add('hidden');
        document.getElementById('addAddressBtn').style.display = 'inline-flex';
    }

    async function saveNewAddress() {
        const addressData = {
            address_name: document.getElementById('newAddressName').value.trim(),
            full_address: document.getElementById('newFullAddress').value.trim(),
            city: document.getElementById('newCity').value.trim(),
            state: document.getElementById('newState').value,
            is_default: document.getElementById('setAsDefault').checked
        };

        // Validation
        if (!addressData.address_name || !addressData.full_address || !addressData.city || !addressData.state) {

            showToasted('Please fill in all required fields', 'error');
            return;
        }

        try {
            showToasted('Saving address...', 'info');

            const response = await fetch('api/save-address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(addressData)
            });

            const result = await response.json();

            if (result.success) {
                showToasted('Address saved successfully!', 'success');
                cancelAddAddress();
                // Refresh addresses list
                location.reload();
            } else {
                throw new Error(result.message || 'Failed to save address');
            }
        } catch (error) {
            console.error('Save address error:', error);
            showToasted('Failed to save address', 'error');
        }
    }

    // Address management functions
    async function editAddress(addressId) {
        try {
            const response = await fetch(`api/get-address.php?address_id=${addressId}`);
            const result = await response.json();

            if (result.success) {
                const address = result.address;

                // Populate edit form (you can create a separate edit modal or modify the add form)
                document.getElementById('newAddressName').value = address.address_name;
                document.getElementById('newFullAddress').value = address.full_address;
                document.getElementById('newCity').value = address.city;
                document.getElementById('newState').value = address.state;
                document.getElementById('setAsDefault').checked = address.is_default == 1;

                // Show form and update button text
                openAddAddressForm();
                const saveBtn = document.querySelector('#addAddressForm button[onclick="saveNewAddress()"]');
                saveBtn.textContent = 'Update Address';
                saveBtn.setAttribute('onclick', `updateAddress(${addressId})`);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Edit address error:', error);
            showToasted('Failed to load address for editing', 'error');
        }
    }

    async function updateAddress(addressId) {
        const addressData = {
            address_id: addressId,
            address_name: document.getElementById('newAddressName').value.trim(),
            full_address: document.getElementById('newFullAddress').value.trim(),
            city: document.getElementById('newCity').value.trim(),
            state: document.getElementById('newState').value,
            is_default: document.getElementById('setAsDefault').checked
        };

        if (!addressData.address_name || !addressData.full_address || !addressData.city || !addressData.state) {
            showToasted('Please fill in all required fields', 'error');
            return;
        }

        try {
            showToasted('Updating address...', 'info');

            const response = await fetch('api/edit-address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(addressData)
            });

            const result = await response.json();

            if (result.success) {
                showToasted('Address updated successfully!', 'success');
                cancelAddAddress();
                location.reload();
            } else {
                throw new Error(result.message || 'Failed to update address');
            }
        } catch (error) {
            console.error('Update address error:', error);
            showToasted('Failed to update address', 'error');
        }
    }

    async function deleteAddress(addressId) {
        if (!confirm('Are you sure you want to delete this address?')) {
            return;
        }

        try {
            showToasted('Deleting address...', 'info');

            const response = await fetch('api/delete-address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    address_id: addressId
                })
            });

            const result = await response.json();

            if (result.success) {
                showToasted('Address deleted successfully!', 'success');
                location.reload();
            } else {
                throw new Error(result.message || 'Failed to delete address');
            }
        } catch (error) {
            console.error('Delete address error:', error);
            showToasted('Failed to delete address', 'error');
        }
    }

    async function setDefaultAddress(addressId) {
        try {
            showToasted('Setting default address...', 'info');

            const response = await fetch('api/set-default-address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    address_id: addressId
                })
            });

            const result = await response.json();

            if (result.success) {
                showToasted('Default address updated!', 'success');
                location.reload();
            } else {
                throw new Error(result.message || 'Failed to set default address');
            }
        } catch (error) {
            console.error('Set default address error:', error);
            showToasted('Failed to set default address', 'error');
        }
    }

    // Language functions
    let selectedLanguage = '<?php echo $preferences['language'] ?? 'en'; ?>';

    document.querySelectorAll('.language-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            document.querySelectorAll('.radio-check').forEach(check => check.classList.remove('active'));

            // Add active class to selected option
            this.querySelector('.radio-check').classList.add('active');

            // Update selected language
            selectedLanguage = this.dataset.lang;
        });
    });

    async function saveLanguagePreference() {
        try {
            showToasted('Updating language...', 'info');

            const response = await fetch('api/update-preferences.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    key: 'language',
                    value: selectedLanguage
                })
            });

            const result = await response.json();

            if (result.success) {
                showToasted('Language updated successfully!', 'success');
                closeModal('languageModal');
                // Update UI language text if needed
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(result.message || 'Failed to update language');
            }
        } catch (error) {
            console.error('Language update error:', error);
            showToasted('Failed to update language', 'error');
        }
    }

    // Help functions
    function toggleHelpItem(element) {
        const content = element.querySelector('.help-content');
        const icon = element.querySelector('i');

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Password visibility toggle
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling.querySelector('i');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Utility functions
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    async function changePassword(currentPassword, newPassword) {
        try {
            showToasted('Updating password...', 'info');

            const response = await fetch('api/change-password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword
                })
            });

            const result = await response.json();

            if (result.success) {
                showToasted('Password updated successfully!', 'success');
                closeModal('securityModal');
                // Clear form
                document.getElementById('passwordChangeForm').reset();
            } else {
                throw new Error(result.message || 'Failed to update password');
            }
        } catch (error) {
            showToasted(error.message || 'Failed to update password', 'error');
        }
    }

    async function submitFeedback(type, message) {
        try {
            showToasted('Sending feedback...', 'info');

            const response = await fetch('api/submit-feedback.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: type,
                    message: message
                })
            });

            const result = await response.json();

            if (result.success) {
                showToasted('Thank you for your feedback!', 'success');
                closeModal('feedbackModal');
                document.getElementById('feedbackForm').reset();
            } else {
                throw new Error(result.message || 'Failed to submit feedback');
            }
        } catch (error) {
            console.error('Feedback submission error:', error);
            showToasted('Failed to submit feedback', 'error');
        }
    }

    // Add modal-specific styles
    const modalStyles = document.createElement('style');
    modalStyles.textContent = `
    .radio-check {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        display: flex;
        items-center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .radio-check.active {
        background-color: #f97316;
        border-color: #f97316;
        color: white;
    }
    
    .radio-check i {
        font-size: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .radio-check.active i {
        opacity: 1;
    }
    
    .contact-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .help-item .help-content {
        transition: all 0.3s ease;
    }
    
    .language-option:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
`;
    document.head.appendChild(modalStyles);
</script>