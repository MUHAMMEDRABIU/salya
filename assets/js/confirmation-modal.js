// Confirmation Modal System
function createConfirmationModal(options = {}) {
    const {
        title = 'Confirm Action',
        message = 'Are you sure you want to proceed?',
        confirmText = 'Confirm',
        cancelText = 'Cancel',
        type = 'warning', // success, error, warning, info
        onConfirm = () => {},
        onCancel = () => {}
    } = options;
    
    // Remove existing confirmation modal
    const existingModal = document.querySelector('.confirmation-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create modal HTML
    const modal = document.createElement('div');
    modal.className = 'confirmation-modal fixed inset-0 bg-black bg-opacity-50 z-[9998] flex items-center justify-center p-4';
    modal.style.opacity = '0';
    
    const iconColors = {
        success: 'text-green-500 bg-green-100',
        error: 'text-red-500 bg-red-100',
        warning: 'text-yellow-500 bg-yellow-100',
        info: 'text-blue-500 bg-blue-100'
    };
    
    const buttonColors = {
        success: 'bg-green-500 hover:bg-green-600',
        error: 'bg-red-500 hover:bg-red-600',
        warning: 'bg-yellow-500 hover:bg-yellow-600',
        info: 'bg-blue-500 hover:bg-blue-600'
    };
    
    const icons = {
        success: 'check-circle',
        error: 'x-circle',
        warning: 'alert-triangle',
        info: 'info'
    };
    
    modal.innerHTML = `
        <div class="confirmation-modal-content bg-white rounded-xl shadow-2xl max-w-md w-full transform scale-95 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full ${iconColors[type]} flex items-center justify-center mr-4">
                        <i data-lucide="${icons[type]}" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                </div>
                <p class="text-gray-600 mb-6">${message}</p>
                <div class="flex items-center justify-end space-x-3">
                    <button class="confirmation-cancel px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        ${cancelText}
                    </button>
                    <button class="confirmation-confirm px-4 py-2 text-white rounded-lg transition-colors font-medium ${buttonColors[type]}">
                        ${confirmText}
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add to document
    document.body.appendChild(modal);
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Animate in
    setTimeout(() => {
        modal.style.opacity = '1';
        const content = modal.querySelector('.confirmation-modal-content');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
    
    // Get buttons
    const confirmBtn = modal.querySelector('.confirmation-confirm');
    const cancelBtn = modal.querySelector('.confirmation-cancel');
    
    // Handle confirm
    confirmBtn.addEventListener('click', () => {
        closeModal();
        onConfirm();
    });
    
    // Handle cancel
    cancelBtn.addEventListener('click', () => {
        closeModal();
        onCancel();
    });
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
            onCancel();
        }
    });
    
    // Close on Escape key
    const handleEscape = (e) => {
        if (e.key === 'Escape') {
            closeModal();
            onCancel();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
    
    function closeModal() {
        modal.style.opacity = '0';
        const content = modal.querySelector('.confirmation-modal-content');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        
        setTimeout(() => {
            if (modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
        }, 300);
        
        document.removeEventListener('keydown', handleEscape);
    }
    
    return modal;
}

// Convenience function for product addition confirmation
function confirmAddProduct(productName, price, onConfirm) {
    return createConfirmationModal({
        title: 'Add Product',
        message: `Are you sure you want to add "${productName}" for ₦${parseFloat(price).toLocaleString()}?`,
        confirmText: 'Add Product',
        cancelText: 'Cancel',
        type: 'info',
        onConfirm: onConfirm
    });
}