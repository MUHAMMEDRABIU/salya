<?php
require_once 'util/util.php';
require_once 'initialize.php';
require_once 'partials/headers.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items from database
$cart_items = getUserCartItems($pdo, $user_id);
$cartTotals = getUserCartTotals($pdo, $user_id);

// Extract values for backward compatibility
$subtotal = $cartTotals['subtotal'];
$delivery_fee = $cartTotals['delivery_fee'];
$total = $cartTotals['total'];
$cartCount = $cartTotals['item_count'];
?>

<body class="font-dm bg-custom-gray min-h-screen blob-bg">
    <!-- Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-accent opacity-10 rounded-full filter blur-3xl animate-float"></div>
        <div class="absolute top-1/2 -left-32 w-64 h-64 bg-secondary opacity-8 rounded-full filter blur-3xl animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 right-1/4 w-48 h-48 bg-accent opacity-9 rounded-full filter blur-3xl animate-float" style="animation-delay: 2s;"></div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="toast-container space-y-2"></div>

    <!-- Custom Confirm Modal -->
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300"></div>

        <!-- Modal -->
        <div class="relative flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-large max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="confirm-modal-content">
                <div class="p-6">
                    <!-- Icon -->
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center">
                        <i id="confirm-icon" class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>

                    <!-- Title -->
                    <h3 id="confirm-title" class="text-xl font-bold text-custom-dark text-center mb-2">
                        Confirm Action
                    </h3>

                    <!-- Message -->
                    <p id="confirm-message" class="text-gray-600 text-center mb-6">
                        Are you sure you want to proceed?
                    </p>

                    <!-- Buttons -->
                    <div class="flex space-x-3">
                        <button id="confirm-cancel" class="flex-1 px-4 py-3 border border-blue-200 text-gray-700 rounded-xl font-semibold hover:bg-slate-50 transition-all duration-300 transform hover:scale-105">
                            Cancel
                        </button>
                        <button id="confirm-ok" class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 shadow-accent">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10">
        <div class="container mx-auto p-6">
            <?php include 'partials/top-nav.php'; ?>
            <!-- Cart Content -->
            <div class="max-w-7xl mx-auto animate-fade-in">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="frosted-glass rounded-2xl p-6 shadow-medium border border-white/20">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-custom-dark flex items-center">
                                    <i class="fas fa-shopping-bag mr-3 text-accent"></i>
                                    Cart Items
                                </h2>
                                <?php if (!empty($cart_items)): ?>
                                    <button id="clear-cart-btn" class="text-orange-500 hover:text-orange-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-orange-50 transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-trash mr-2"></i>
                                        Clear Cart
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Cart Items List -->
                            <div id="cart-items" class="space-y-4">
                                <?php if (!empty($cart_items)): ?>
                                    <?php foreach ($cart_items as $item): ?>
                                        <div class="cart-item flex items-center space-x-4 p-4 bg-white/60 backdrop-blur-sm border border-white/30 rounded-xl shadow-soft hover:shadow-medium" data-item-id="<?php echo $item['product_id']; ?>" data-price="<?php echo $item['price']; ?>">
                                            <div class="relative overflow-hidden rounded-xl">
                                                <img src="../assets/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-20 h-20 object-cover transition-transform duration-300 hover:scale-110">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-custom-dark text-lg"><?php echo htmlspecialchars($item['name']); ?></h3>
                                                <p class="text-accent font-bold text-lg">₦<?php echo number_format($item['price']); ?></p>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <button class="quantity-btn decrease-btn w-10 h-10 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center hover:from-accent hover:to-orange-600 hover:text-white transition-all duration-300 shadow-soft" data-action="decrease">
                                                    <i class="fas fa-minus text-sm"></i>
                                                </button>
                                                <span class="quantity-display font-bold text-custom-dark min-w-[3rem] text-center text-lg bg-white/80 px-3 py-1 rounded-lg shadow-soft"><?php echo $item['quantity']; ?></span>
                                                <button class="quantity-btn increase-btn w-10 h-10 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center hover:from-accent hover:to-orange-600 hover:text-white transition-all duration-300 shadow-soft" data-action="increase">
                                                    <i class="fas fa-plus text-sm"></i>
                                                </button>
                                            </div>
                                            <div class="text-right">
                                                <button class="remove-item-btn text-red-500 hover:text-red-700 text-sm mt-2 px-3 py-1 rounded-lg hover:bg-red-50 transition-all duration-300 transform hover:scale-105" data-item-id="<?php echo $item['product_id']; ?>">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Empty Cart State -->
                            <div id="empty-cart" class="<?php echo empty($cart_items) ? '' : 'hidden'; ?> text-center py-16">
                                <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-gray-400 text-4xl"></i>
                                </div>
                                <h3 class="text-2xl font-semibold text-gray-600 mb-3">Your cart is empty</h3>
                                <p class="text-gray-500 mb-8 max-w-md mx-auto">Add some delicious frozen foods to get started on your culinary journey!</p>
                                <a href="dashboard.php" class="btn-primary text-white px-8 py-4 rounded-xl font-semibold inline-flex items-center space-x-2 shadow-accent">
                                    <i class="fas fa-utensils"></i>
                                    <span>Continue Shopping</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="frosted-glass-accent rounded-2xl p-6 sticky top-6 shadow-large border border-accent/20">
                            <h2 class="text-xl font-semibold text-custom-dark mb-6 flex items-center">
                                <i class="fas fa-receipt mr-3 text-accent"></i>
                                Order Summary
                            </h2>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center p-3 bg-white/50 rounded-lg">
                                    <span class="text-gray-700 font-medium">Subtotal</span>
                                    <span id="subtotal" class="font-bold text-custom-dark text-lg">₦<?php echo number_format($subtotal); ?></span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-white/50 rounded-lg">
                                    <span class="text-gray-700 font-medium">Delivery Fee</span>
                                    <span id="delivery-fee" class="font-bold text-custom-dark text-lg">₦<?php echo number_format($delivery_fee); ?></span>
                                </div>
                                <?php if ($subtotal >= 10000): ?>
                                    <div class="text-green-600 text-sm font-medium p-3 bg-green-50 rounded-lg flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Free delivery on orders ₦10,000+
                                    </div>
                                <?php else: ?>
                                    <div class="text-amber-600 text-sm font-medium p-3 bg-amber-50 rounded-lg flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Add ₦<?php echo number_format(10000 - $subtotal); ?> more for free delivery
                                    </div>
                                <?php endif; ?>
                                <hr class="border-white/30">
                                <div class="flex justify-between text-xl font-bold p-4 bg-white/70 rounded-xl shadow-soft">
                                    <span class="text-custom-dark">Total</span>
                                    <span id="total" class="text-accent">₦<?php echo number_format($total); ?></span>
                                </div>
                            </div>

                            <!-- Promo Code -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Promo Code</label>
                                <div class="flex space-x-2">
                                    <input type="text" id="promo-code" placeholder="Enter promo code" class="input-focus flex-1 px-4 py-3 border border-white/30 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent bg-white/80 backdrop-blur-sm">
                                    <button id="apply-promo-btn" class="bg-white/80 text-custom-dark px-6 py-3 rounded-xl font-semibold hover:bg-accent hover:text-white transition-all duration-300 transform hover:scale-105 shadow-soft">
                                        Apply
                                    </button>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <?php if (!empty($cart_items)): ?>
                                <button id="checkout-btn" class="w-full btn-primary text-white py-4 rounded-xl font-semibold mb-4 flex items-center justify-center space-x-2 shadow-accent">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Proceed to Checkout</span>
                                </button>
                            <?php else: ?>
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-4 rounded-xl font-semibold mb-4 flex items-center justify-center space-x-2 cursor-not-allowed">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Cart is Empty</span>
                                </button>
                            <?php endif; ?>

                            <a href="dashboard.php" class="block w-full text-center bg-white/80 text-custom-dark py-4 rounded-xl font-semibold hover:bg-white transition-all duration-300 transform hover:scale-105 shadow-soft backdrop-blur-sm">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom navigation include -->
    <?php include 'partials/bottom-nav.php'; ?>

    <script src="../assets/js/toast.js"></script>
    <script>
        // Custom confirm modal functionality
        function showCustomConfirm(title, message, iconClass = 'fa-exclamation-triangle', iconColor = 'text-red-500') {
            return new Promise((resolve) => {
                const modal = document.getElementById('confirm-modal');
                const modalContent = document.getElementById('confirm-modal-content');
                const titleEl = document.getElementById('confirm-title');
                const messageEl = document.getElementById('confirm-message');
                const iconEl = document.getElementById('confirm-icon');
                const cancelBtn = document.getElementById('confirm-cancel');
                const okBtn = document.getElementById('confirm-ok');

                // Set content
                titleEl.textContent = title;
                messageEl.textContent = message;
                iconEl.className = `fas ${iconClass} ${iconColor} text-2xl`;

                // Show modal with animation
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);

                // Handle buttons
                const handleCancel = () => {
                    hideModal();
                    resolve(false);
                };

                const handleConfirm = () => {
                    hideModal();
                    resolve(true);
                };

                const hideModal = () => {
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    modalContent.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        cancelBtn.removeEventListener('click', handleCancel);
                        okBtn.removeEventListener('click', handleConfirm);
                    }, 300);
                };

                // Add event listeners
                cancelBtn.addEventListener('click', handleCancel);
                okBtn.addEventListener('click', handleConfirm);

                // Close on backdrop click
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        handleCancel();
                    }
                });

                // Close on escape key
                const handleEscape = (e) => {
                    if (e.key === 'Escape') {
                        handleCancel();
                        document.removeEventListener('keydown', handleEscape);
                    }
                };
                document.addEventListener('keydown', handleEscape);
            });
        }

        // Initialize cart page
        document.addEventListener('DOMContentLoaded', function() {
            initializeCartActions();
            checkEmptyCart();

            // Add loading animation to page elements
            const elements = document.querySelectorAll('.cart-item');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
                el.classList.add('animate-slide-up');
            });
        });

        // Cart actions functionality
        function initializeCartActions() {
            // Quantity buttons with enhanced animations
            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const action = this.getAttribute('data-action');
                    const cartItem = this.closest('.cart-item');
                    const itemId = cartItem.getAttribute('data-item-id');
                    const price = parseInt(cartItem.getAttribute('data-price'));
                    const quantityDisplay = cartItem.querySelector('.quantity-display');

                    // Add loading state
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    this.disabled = true;

                    let quantity = parseInt(quantityDisplay.textContent);
                    quantity = action === 'increase' ? quantity + 1 : Math.max(1, quantity - 1);

                    try {
                        const res = await fetch('api/update-cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: itemId,
                                quantity
                            })
                        });
                        const result = await res.json();

                        if (result.success) {
                            // Animate quantity change
                            quantityDisplay.style.transform = 'scale(1.2)';
                            quantityDisplay.textContent = quantity;
                            setTimeout(() => {
                                quantityDisplay.style.transform = 'scale(1)';
                            }, 200);

                            updateTotals();
                            updateCartCount();
                        } else {
                            showToasted(result.message || 'Failed to update cart', 'error');
                        }
                    } catch (error) {
                        console.log(error)
                        showToasted('Network error occurred', 'error');
                    } finally {
                        // Restore button
                        this.innerHTML = action === 'increase' ? '<i class="fas fa-plus text-sm"></i>' : '<i class="fas fa-minus text-sm"></i>';
                        this.disabled = false;
                    }
                });
            });

            // Remove item with animation
            document.querySelectorAll('.remove-item-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const itemId = this.getAttribute('data-item-id');
                    const cartItem = this.closest('.cart-item');
                    const itemName = cartItem.querySelector('h3').textContent;

                    const confirmed = await showCustomConfirm(
                        'Remove Item',
                        `Are you sure you want to remove "${itemName}" from your cart?`,
                        'fa-trash-alt',
                        'text-red-500'
                    );

                    if (!confirmed) return;

                    // Add removing animation
                    cartItem.style.transform = 'translateX(-100%)';
                    cartItem.style.opacity = '0';

                    try {
                        const res = await fetch('api/remove-cart-item.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: itemId
                            })
                        });

                        const result = await res.json();
                        if (result.success) {
                            setTimeout(() => {
                                cartItem.remove();
                                updateTotals();
                                updateCartCount();
                                checkEmptyCart();
                            }, 300);
                            showToasted('Item removed from cart', 'info');
                        } else {
                            // Restore item if failed
                            cartItem.style.transform = 'translateX(0)';
                            cartItem.style.opacity = '1';
                            showToasted('Failed to remove item', 'error');
                        }
                    } catch (error) {
                        cartItem.style.transform = 'translateX(0)';
                        cartItem.style.opacity = '1';
                        showToasted('Network error occurred', 'error');
                    }
                });
            });

            // Clear cart button with confirmation
            const clearCartBtn = document.getElementById('clear-cart-btn');
            if (clearCartBtn) {
                clearCartBtn.addEventListener('click', async function() {
                    const itemCount = document.querySelectorAll('.cart-item').length;
                    const confirmed = await showCustomConfirm(
                        'Clear Cart',
                        `Are you sure you want to remove all ${itemCount} items from your cart? This action cannot be undone.`,
                        'fa-trash-alt',
                        'text-red-500'
                    );

                    if (!confirmed) return;

                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Clearing...';
                    this.disabled = true;

                    try {
                        const res = await fetch('api/clear-cart.php', {
                            method: 'POST'
                        });
                        const result = await res.json();

                        if (result.success) {
                            // Animate all items out
                            const items = document.querySelectorAll('.cart-item');
                            items.forEach((item, index) => {
                                setTimeout(() => {
                                    item.style.transform = 'translateX(-100%)';
                                    item.style.opacity = '0';
                                    setTimeout(() => item.remove(), 300);
                                }, index * 100);
                            });

                            setTimeout(() => {
                                updateTotals();
                                updateCartCount();
                                checkEmptyCart();
                            }, items.length * 100 + 300);

                            showToasted('Cart cleared successfully', 'info');
                        } else {
                            showToasted('Failed to clear cart', 'error');
                        }
                    } catch (error) {
                        showToasted('Network error occurred', 'error');
                    } finally {
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                });
            }

            // Promo code with enhanced feedback
            document.getElementById('apply-promo-btn').addEventListener('click', function() {
                const promoCode = document.getElementById('promo-code').value.trim();
                const button = this;
                const originalText = button.textContent;

                if (!promoCode) {
                    showToasted('Please enter a promo code', 'error');
                    return;
                }

                // Add loading state
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

                // Simulate API call
                setTimeout(() => {
                    const validCodes = ['SAVE10', 'WELCOME20', 'FROZEN15'];

                    if (validCodes.includes(promoCode.toUpperCase())) {
                        showToasted('Promo code applied successfully! 🎉', 'success');
                        document.getElementById('promo-code').value = '';
                        // Add visual feedback
                        document.getElementById('promo-code').style.borderColor = '#22c55e';
                        setTimeout(() => {
                            document.getElementById('promo-code').style.borderColor = '';
                        }, 2000);
                    } else {
                        showToasted('Invalid promo code', 'error');
                        document.getElementById('promo-code').style.borderColor = '#ef4444';
                        setTimeout(() => {
                            document.getElementById('promo-code').style.borderColor = '';
                        }, 2000);
                    }

                    button.textContent = originalText;
                    button.disabled = false;
                }, 1500);
            });

            // Enhanced checkout button
            const checkoutBtn = document.getElementById('checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', function() {
                    const cartItems = document.querySelectorAll('.cart-item');

                    if (cartItems.length === 0) {
                        showToasted('Your cart is empty', 'error');
                        return;
                    }

                    // Enhanced loading state
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    this.disabled = true;
                    this.style.transform = 'scale(0.98)';

                    // Simulate checkout process
                    setTimeout(() => {
                        setTimeout(() => {
                            window.location.href = 'checkout.php';
                        }, 1000);

                        // Reset button after delay
                        setTimeout(() => {
                            this.innerHTML = originalContent;
                            this.disabled = false;
                            this.style.transform = 'scale(1)';
                        }, 2000);
                    }, 2000);
                });
            }

            // Back button functionality
            document.getElementById('backBtn').addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                    window.history.back();
                }, 150);
            });
        }

        // Enhanced update totals with animations
        function updateTotals() {
            const cartItems = document.querySelectorAll('.cart-item');
            let subtotal = 0;

            cartItems.forEach(item => {
                const price = parseInt(item.getAttribute('data-price'));
                const quantity = parseInt(item.querySelector('.quantity-display').textContent);
                subtotal += price * quantity;
            });

            const deliveryFee = subtotal >= 10000 ? 0 : 500;
            const total = subtotal + deliveryFee;

            // Animate total changes
            const subtotalEl = document.getElementById('subtotal');
            const deliveryEl = document.getElementById('delivery-fee');
            const totalEl = document.getElementById('total');

            [subtotalEl, deliveryEl, totalEl].forEach(el => {
                el.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    el.style.transform = 'scale(1)';
                }, 200);
            });

            subtotalEl.textContent = '₦' + subtotal.toLocaleString();
            deliveryEl.textContent = '₦' + deliveryFee.toLocaleString();
            totalEl.textContent = '₦' + total.toLocaleString();
        }

        // Enhanced empty cart check
        function checkEmptyCart() {
            const cartItems = document.getElementById('cart-items');
            const emptyCart = document.getElementById('empty-cart');
            const items = document.querySelectorAll('.cart-item');

            if (items.length === 0) {
                cartItems.classList.add('hidden');
                emptyCart.classList.remove('hidden');
                emptyCart.classList.add('animate-scale-in');
            } else {
                cartItems.classList.remove('hidden');
                emptyCart.classList.add('hidden');
            }
        }

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';

        // Add intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-slide-up');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.addEventListener('DOMContentLoaded', () => {
            const animateElements = document.querySelectorAll('.cart-item, .frosted-glass');
            animateElements.forEach(el => observer.observe(el));
        });
    </script>
</body>

</html>