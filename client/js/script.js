document?.addEventListener("DOMContentLoaded", function () {
  lucide.createIcons();
  
  // Initialize cart count on page load
  updateCartCount();
});

// Back button functionality
document.getElementById("backBtn")?.addEventListener("click", function () {
  this.style.transform = "scale(0.95)";
  setTimeout(() => {
    this.style.transform = "scale(1)";
    window.history.back();
  }, 150);
});

/**
 * Fetch and update cart count from server
 */
async function updateCartCount() {
  try {
    const response = await fetch('api/get-cart-count.php');
    const data = await response.json();
    
    if (data.success) {
      const cartCountElement = document.getElementById('cartCount');
      if (cartCountElement) {
        const newCount = data.cart_count;
        cartCountElement.textContent = newCount;
        
        // Show/hide cart badge based on count
        const cartBadge = cartCountElement.parentElement;
        if (newCount > 0) {
          cartBadge.style.display = 'flex';
          // Add bounce animation for visual feedback
          cartBadge.classList.remove('animate-bounce-gentle');
          setTimeout(() => cartBadge.classList.add('animate-bounce-gentle'), 10);
        } else {
          cartBadge.style.display = 'none';
        }
      }
    }
  } catch (error) {
    console.log('Error updating cart count:', error);
    // Silently fail - cart count will show server-rendered value
  }
}

/**
 * Add item to cart and update count
 */
async function addToCart(productId, quantity = 1) {
  try {
    const response = await fetch('api/add-to-cart.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: quantity
      })
    });

    const data = await response.json();
    
    if (data.success) {
      // Update cart count in navigation
      const cartCountElement = document.getElementById('cartCount');
      if (cartCountElement && data.cart_count !== undefined) {
        cartCountElement.textContent = data.cart_count;
        
        // Show cart badge and add animation
        const cartBadge = cartCountElement.parentElement;
        cartBadge.style.display = 'flex';
        cartBadge.classList.remove('animate-bounce-gentle');
        setTimeout(() => cartBadge.classList.add('animate-bounce-gentle'), 10);
      }
      
      // Show success message
      if (typeof showToasted === 'function') {
        showToasted(data.message || 'Item added to cart!', 'success');
      }
      
      return true;
    } else {
      if (typeof showToasted === 'function') {
        showToasted(data.message || 'Failed to add item to cart', 'error');
      }
      return false;
    }
  } catch (error) {
    console.error('Error adding to cart:', error);
    if (typeof showToasted === 'function') {
      showToasted('An error occurred. Please try again.', 'error');
    }
    return false;
  }
}

/**
 * Update cart item quantity and refresh count
 */
async function updateCartQuantity(productId, quantity) {
  try {
    const response = await fetch('api/update-cart.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        id: productId,
        quantity: quantity
      })
    });

    const data = await response.json();
    
    if (data.success) {
      // Update cart count in navigation
      updateCartCount();
      return data;
    } else {
      if (typeof showToasted === 'function') {
        showToasted(data.message || 'Failed to update cart', 'error');
      }
      return false;
    }
  } catch (error) {
    console.error('Error updating cart:', error);
    return false;
  }
}

// Make functions globally available
window.updateCartCount = updateCartCount;
window.addToCart = addToCart;
window.updateCartQuantity = updateCartQuantity;
