/**
 * Product page functionality for Frozen Foods
 */

let currentProduct = null;
let currentQuantity = 1;

/**
 * Initialize product page
 * @param {Object} product 
 */
function initializeProductPage(product) {
    currentProduct = product;
    
    // Initialize quantity controls
    initializeQuantityControls();
    
    // Initialize action buttons
    initializeActionButtons();
    
    // Initialize favorite button
    initializeFavoriteButton();
}

/**
 * Initialize quantity controls
 */
function initializeQuantityControls() {
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const quantityDisplay = document.getElementById('quantity');
    
    if (!decreaseBtn || !increaseBtn || !quantityDisplay) return;
    
    decreaseBtn.addEventListener('click', function() {
        if (currentQuantity > 1) {
            currentQuantity--;
            updateQuantityDisplay();
        }
    });
    
    increaseBtn.addEventListener('click', function() {
        if (currentQuantity < 99) {
            currentQuantity++;
            updateQuantityDisplay();
        }
    });
}

/**
 * Update quantity display
 */
function updateQuantityDisplay() {
    const quantityDisplay = document.getElementById('quantity');
    if (quantityDisplay) {
        quantityDisplay.textContent = currentQuantity;
    }
}

/**
 * Initialize action buttons
 */
function initializeActionButtons() {
    const orderNowBtn = document.getElementById('order-now-btn');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    
    if (orderNowBtn) {
        orderNowBtn.addEventListener('click', function() {
            handleOrderNow();
        });
    }
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            handleAddToCart();
        });
    }
}

/**
 * Initialize favorite button
 */
function initializeFavoriteButton() {
    const favoriteBtn = document.querySelector('.fa-heart').closest('button');
    
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            toggleFavorite();
        });
    }
}

/**
 * Handle order now action
 */
function handleOrderNow() {
    if (!currentProduct) return;
    
    // Show loading state
    const orderBtn = document.getElementById('order-now-btn');
    const originalText = orderBtn.textContent;
    orderBtn.textContent = 'Processing...';
    orderBtn.disabled = true;
    
    // Simulate order processing
    setTimeout(() => {
        // In a real application, this would make an API call
        const orderData = {
            product: currentProduct,
            quantity: currentQuantity,
            total: currentProduct.price * currentQuantity
        };
        
        // Store order data (in real app, send to server)
        localStorage.setItem('currentOrder', JSON.stringify(orderData));
        
        // Show success message
        showNotification('Order placed successfully!', 'success');
        
        // Reset button
        orderBtn.textContent = originalText;
        orderBtn.disabled = false;
        
        // Redirect to order confirmation (in real app)
        // window.location.href = 'order-confirmation.php';
        
    }, 1500);
}

/**
 * Handle add to cart action
 */
function handleAddToCart() {
    if (!currentProduct) return;
    
    // Show loading state
    const cartBtn = document.getElementById('add-to-cart-btn');
    const originalText = cartBtn.textContent;
    cartBtn.textContent = 'Adding...';
    cartBtn.disabled = true;
    
    // Simulate adding to cart
    setTimeout(() => {
        // Get existing cart or create new one
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        
        // Check if product already exists in cart
        const existingItemIndex = cart.findIndex(item => item.product.id === currentProduct.id);
        
        if (existingItemIndex > -1) {
            // Update quantity if product exists
            cart[existingItemIndex].quantity += currentQuantity;
        } else {
            // Add new item to cart
            cart.push({
                product: currentProduct,
                quantity: currentQuantity
            });
        }
        
        // Save cart
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Show success message
        showNotification('Added to cart successfully!', 'success');
        
        // Reset button
        cartBtn.textContent = originalText;
        cartBtn.disabled = false;
        
        // Update cart count in header (if exists)
        updateCartCount();
        
    }, 1000);
}

/**
 * Toggle favorite status
 */
function toggleFavorite() {
    if (!currentProduct) return;
    
    const icon = document.querySelector('.fa-heart');
    const button = icon.closest('button');
    
    if (icon.classList.contains('far')) {
        // Add to favorites
        icon.classList.remove('far');
        icon.classList.add('fas', 'text-red-500');
        button.classList.add('bg-red-50');
        
        // Save to favorites
        addToFavorites(currentProduct);
        showNotification('Added to favorites!', 'success');
    } else {
        // Remove from favorites
        icon.classList.remove('fas', 'text-red-500');
        icon.classList.add('far');
        button.classList.remove('bg-red-50');
        
        // Remove from favorites
        removeFromFavorites(currentProduct.id);
        showNotification('Removed from favorites!', 'info');
    }
}

/**
 * Add product to favorites
 * @param {Object} product 
 */
function addToFavorites(product) {
    let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    
    // Check if already in favorites
    if (!favorites.find(fav => fav.id === product.id)) {
        favorites.push(product);
        localStorage.setItem('favorites', JSON.stringify(favorites));
    }
}

/**
 * Remove product from favorites
 * @param {number} productId 
 */
function removeFromFavorites(productId) {
    let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    favorites = favorites.filter(fav => fav.id !== productId);
    localStorage.setItem('favorites', JSON.stringify(favorites));
}

/**
 * Update cart count in header
 */
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    // Update cart badge if exists
    const cartBadge = document.querySelector('.cart-count');
    if (cartBadge) {
        cartBadge.textContent = totalItems;
        cartBadge.style.display = totalItems > 0 ? 'flex' : 'none';
    }
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
            document.body.removeChild(notification);
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
 * Format currency for display
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
 * Validate quantity input
 * @param {number} quantity 
 * @returns {boolean}
 */
function validateQuantity(quantity) {
    return quantity >= 1 && quantity <= 99 && Number.isInteger(quantity);
}

/**
 * Get cart total
 * @returns {number}
 */
function getCartTotal() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    return cart.reduce((total, item) => total + (item.product.price * item.quantity), 0);
}

/**
 * Clear cart
 */
function clearCart() {
    localStorage.removeItem('cart');
    updateCartCount();
}