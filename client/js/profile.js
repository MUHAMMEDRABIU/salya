/**
 * Profile page functionality for Frozen Foods
 */

// Initialize profile page when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeProfileEdit();
    initializePasswordModal();
    initializeNotificationToggles();
    initializeQuickActions();
});

/**
 * Initialize profile editing functionality
 */
function initializeProfileEdit() {
    const editBtn = document.getElementById('edit-profile-btn');
    const saveBtn = document.getElementById('save-profile-btn');
    const cancelBtn = document.getElementById('cancel-edit-btn');
    const editActions = document.getElementById('edit-actions');
    
    const nameInput = document.getElementById('user-name');
    const emailInput = document.getElementById('user-email');
    const phoneInput = document.getElementById('user-phone');
    const addressInput = document.getElementById('user-address');
    
    let originalValues = {};
    
    if (!editBtn || !saveBtn || !cancelBtn) return;
    
    // Store original values
    function storeOriginalValues() {
        originalValues = {
            name: nameInput.value,
            email: emailInput.value,
            phone: phoneInput.value,
            address: addressInput.value
        };
    }
    
    // Enable editing mode
    editBtn.addEventListener('click', function() {
        storeOriginalValues();
        
        // Enable inputs
        nameInput.removeAttribute('readonly');
        emailInput.removeAttribute('readonly');
        phoneInput.removeAttribute('readonly');
        addressInput.removeAttribute('readonly');
        
        // Add focus styles
        [nameInput, emailInput, phoneInput, addressInput].forEach(input => {
            input.classList.add('bg-white');
            input.classList.remove('bg-gray-50');
        });
        
        // Show/hide buttons
        editBtn.classList.add('hidden');
        editActions.classList.remove('hidden');
        
        // Focus on first input
        nameInput.focus();
    });
    
    // Save changes
    saveBtn.addEventListener('click', function() {
        if (validateProfileForm()) {
            saveProfileChanges();
        }
    });
    
    // Cancel editing
    cancelBtn.addEventListener('click', function() {
        cancelProfileEdit();
    });
    
    // Cancel editing function
    function cancelProfileEdit() {
        // Restore original values
        nameInput.value = originalValues.name;
        emailInput.value = originalValues.email;
        phoneInput.value = originalValues.phone;
        addressInput.value = originalValues.address;
        
        // Disable inputs
        nameInput.setAttribute('readonly', true);
        emailInput.setAttribute('readonly', true);
        phoneInput.setAttribute('readonly', true);
        addressInput.setAttribute('readonly', true);
        
        // Remove focus styles
        [nameInput, emailInput, phoneInput, addressInput].forEach(input => {
            input.classList.remove('bg-white');
            input.classList.add('bg-gray-50');
        });
        
        // Show/hide buttons
        editBtn.classList.remove('hidden');
        editActions.classList.add('hidden');
    }
    
    // Validate profile form
    function validateProfileForm() {
        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();
        const address = addressInput.value.trim();
        
        if (!name) {
            showNotification('Please enter your full name', 'error');
            nameInput.focus();
            return false;
        }
        
        if (!email || !isValidEmail(email)) {
            showNotification('Please enter a valid email address', 'error');
            emailInput.focus();
            return false;
        }
        
        if (!phone || !isValidPhoneNumber(phone)) {
            showNotification('Please enter a valid Nigerian phone number', 'error');
            phoneInput.focus();
            return false;
        }
        
        if (!address) {
            showNotification('Please enter your delivery address', 'error');
            addressInput.focus();
            return false;
        }
        
        return true;
    }
    
    // Save profile changes
    function saveProfileChanges() {
        // Show loading state
        saveBtn.textContent = 'Saving...';
        saveBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // In real app, make API call to save profile
            const profileData = {
                name: nameInput.value.trim(),
                email: emailInput.value.trim(),
                phone: phoneInput.value.trim(),
                address: addressInput.value.trim()
            };
            
            // Save to localStorage for demo
            localStorage.setItem('userProfile', JSON.stringify(profileData));
            
            // Reset button
            saveBtn.textContent = 'Save Changes';
            saveBtn.disabled = false;
            
            // Exit edit mode
            cancelProfileEdit();
            
            // Show success message
            showNotification('Profile updated successfully!', 'success');
            
        }, 1500);
    }
}

/**
 * Initialize password change modal
 */
function initializePasswordModal() {
    const changePasswordBtn = document.getElementById('change-password-btn');
    const passwordModal = document.getElementById('password-modal');
    const closeModal = document.getElementById('close-modal');
    const cancelPassword = document.getElementById('cancel-password');
    const passwordForm = document.getElementById('password-form');
    
    if (!changePasswordBtn || !passwordModal) return;
    
    // Open modal
    changePasswordBtn.addEventListener('click', function() {
        passwordModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
    
    // Close modal
    function closePasswordModal() {
        passwordModal.classList.add('hidden');
        document.body.style.overflow = '';
        passwordForm.reset();
    }
    
    closeModal.addEventListener('click', closePasswordModal);
    cancelPassword.addEventListener('click', closePasswordModal);
    
    // Close on backdrop click
    passwordModal.addEventListener('click', function(e) {
        if (e.target === passwordModal) {
            closePasswordModal();
        }
    });
    
    // Handle form submission
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        handlePasswordChange();
    });
}

/**
 * Handle password change
 */
function handlePasswordChange() {
    const currentPassword = document.getElementById('current-password').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    // Validate passwords
    if (!currentPassword) {
        showNotification('Please enter your current password', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        showNotification('New password must be at least 6 characters', 'error');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showNotification('New passwords do not match', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = passwordForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Updating...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // In real app, make API call to change password
        
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        // Close modal
        document.getElementById('password-modal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('password-form').reset();
        
        // Show success message
        showNotification('Password updated successfully!', 'success');
        
    }, 2000);
}

/**
 * Initialize notification toggles
 */
function initializeNotificationToggles() {
    const toggles = document.querySelectorAll('input[type="checkbox"]');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const setting = this.closest('.flex').querySelector('h4').textContent;
            const status = this.checked ? 'enabled' : 'disabled';
            
            // Save setting to localStorage for demo
            const settings = JSON.parse(localStorage.getItem('notificationSettings') || '{}');
            settings[setting] = this.checked;
            localStorage.setItem('notificationSettings', JSON.stringify(settings));
            
            // Show feedback
            showNotification(`${setting} ${status}`, 'info');
        });
    });
    
    // Load saved settings
    const savedSettings = JSON.parse(localStorage.getItem('notificationSettings') || '{}');
    toggles.forEach(toggle => {
        const setting = toggle.closest('.flex').querySelector('h4').textContent;
        if (savedSettings.hasOwnProperty(setting)) {
            toggle.checked = savedSettings[setting];
        }
    });
}

/**
 * Initialize quick actions
 */
function initializeQuickActions() {
    const quickActionBtns = document.querySelectorAll('.grid button');
    
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.querySelector('h4').textContent;
            
            switch (action) {
                case 'My Favorites':
                    // Navigate to favorites page
                    showNotification('Redirecting to favorites...', 'info');
                    // window.location.href = 'favorites.php';
                    break;
                case 'Addresses':
                    // Navigate to addresses page
                    showNotification('Redirecting to addresses...', 'info');
                    // window.location.href = 'addresses.php';
                    break;
                case 'Support':
                    // Navigate to support page
                    showNotification('Redirecting to support...', 'info');
                    // window.location.href = 'support.php';
                    break;
            }
        });
    });
}

/**
 * Validate email format
 * @param {string} email 
 * @returns {boolean}
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate Nigerian phone number
 * @param {string} phone 
 * @returns {boolean}
 */
function isValidPhoneNumber(phone) {
    // Remove all non-numeric characters
    const cleanPhone = phone.replace(/[^0-9]/g, '');
    
    // Check if it's a valid Nigerian phone number
    if (cleanPhone.length === 11 && cleanPhone.startsWith('0')) {
        return true;
    }
    
    if (cleanPhone.length === 13 && cleanPhone.startsWith('234')) {
        return true;
    }
    
    return false;
}

/**
 * Show notification message
 * @param {string} message 
 * @param {string} type 
 */
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform translate-x-full transition-transform duration-300 ${getNotificationColor(type)}`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

/**
 * Get notification color based on type
 * @param {string} type 
 * @returns {string}
 */
function getNotificationColor(type) {
    switch (type) {
        case 'success':
            return 'bg-green-500';
        case 'error':
            return 'bg-red-500';
        case 'warning':
            return 'bg-yellow-500';
        default:
            return 'bg-blue-500';
    }
}

/**
 * Format phone number for display
 * @param {string} phone 
 * @returns {string}
 */
function formatPhoneDisplay(phone) {
    const cleanPhone = phone.replace(/[^0-9]/g, '');
    
    if (cleanPhone.length === 11) {
        return cleanPhone.replace(/(\d{4})(\d{3})(\d{4})/, '$1 $2 $3');
    }
    
    return phone;
}

/**
 * Load user profile data
 */
function loadUserProfile() {
    const savedProfile = localStorage.getItem('userProfile');
    
    if (savedProfile) {
        const profile = JSON.parse(savedProfile);
        
        document.getElementById('user-name').value = profile.name || '';
        document.getElementById('user-email').value = profile.email || '';
        document.getElementById('user-phone').value = profile.phone || '';
        document.getElementById('user-address').value = profile.address || '';
    }
}

/**
 * Initialize profile data on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    loadUserProfile();
});