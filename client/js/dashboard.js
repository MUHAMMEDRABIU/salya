/**
 * Dashboard functionality for Frozen Foods
 */

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeSidebar();
    initializeTabs();
    initializeSearch();
    initializeFavorites();
});

/**
 * Initialize sidebar functionality
 */
function initializeSidebar() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!menuToggle || !sidebar || !overlay) return;

    // Toggle sidebar
    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    // Close sidebar on window resize if screen is large
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        }
    });
}

/**
 * Initialize tab functionality
 */
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const productCards = document.querySelectorAll('.product-card');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active tab
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('bg-dark', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            });
            
            this.classList.add('active');
            this.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            this.classList.add('bg-dark', 'text-white');
            
            // Filter products
            filterProducts(category);
        });
    });

    // Set initial active tab styles
    const activeTab = document.querySelector('.tab-button.active');
    if (activeTab) {
        activeTab.classList.add('bg-dark', 'text-white');
    }
    
    // Set inactive tab styles
    tabButtons.forEach(button => {
        if (!button.classList.contains('active')) {
            button.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
        }
    });
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchInput = document.getElementById('search-input');
    
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        searchProducts(query);
    });
}

/**
 * Initialize favorite functionality
 */
function initializeFavorites() {
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const icon = this.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                icon.classList.add('text-red-500');
                this.classList.add('bg-red-50');
            } else {
                icon.classList.remove('fas', 'text-red-500');
                icon.classList.add('far');
                this.classList.remove('bg-red-50');
            }
        });
    });
}

/**
 * Filter products by category
 * @param {string} category 
 */
function filterProducts(category) {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        
        if (category === 'all' || cardCategory === category) {
            card.style.display = 'block';
            card.classList.remove('hidden');
        } else {
            card.style.display = 'none';
            card.classList.add('hidden');
        }
    });
}

/**
 * Search products by name
 * @param {string} query 
 */
function searchProducts(query) {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const productName = card.getAttribute('data-name');
        
        if (query === '' || productName.includes(query)) {
            card.style.display = 'block';
            card.classList.remove('hidden');
        } else {
            card.style.display = 'none';
            card.classList.add('hidden');
        }
    });
}

/**
 * Show loading state
 */
function showLoading() {
    const grid = document.getElementById('products-grid');
    if (grid) {
        grid.innerHTML = '<div class="col-span-full text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
    }
}

/**
 * Show error message
 * @param {string} message 
 */
function showError(message) {
    const grid = document.getElementById('products-grid');
    if (grid) {
        grid.innerHTML = `<div class="col-span-full text-center py-8 text-red-500">${message}</div>`;
    }
}

/**
 * Utility function to format currency
 * @param {number} amount 
 * @returns {string}
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
}

/**
 * Utility function to debounce function calls
 * @param {Function} func 
 * @param {number} wait 
 * @returns {Function}
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}