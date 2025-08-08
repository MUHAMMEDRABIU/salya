<?php
require_once 'initialize.php';
require_once '../config/constants.php';
require_once 'util/util.php';

$user_id = $_SESSION['user_id'];

// Get cart items from database
$cart_items = getUserCartItems($pdo, $user_id);
$cartTotals = getUserCartTotals($pdo, $user_id);

// Extract values for backward compatibility
$subtotal = $cartTotals['subtotal'];
$delivery_fee = $cartTotals['delivery_fee'];
$total = $cartTotals['total'];
$cartCount = $cartTotals['item_count'];

require_once 'partials/headers.php';
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
        <!-- ✅ FIX 1: Added bottom padding for navigation overlap -->
        <div class="container mx-auto p-4 sm:p-6 pb-24 sm:pb-6">
            <?php include 'partials/top-nav.php'; ?>
            <!-- Cart Content -->
            <div class="max-w-7xl mx-auto animate-fade-in">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 order-2 lg:order-1">
                        <div class="frosted-glass rounded-2xl p-4 sm:p-6 shadow-medium border border-white/20">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                                <!-- ✅ FIX 2: Removed cart items count badge -->
                                <h2 class="text-lg sm:text-xl font-semibold text-custom-dark flex items-center mb-3 sm:mb-0">
                                    <i class="fas fa-shopping-bag mr-3 text-accent"></i>
                                    Cart Items
                                </h2>
                                <!-- ✅ FIX 3: Enhanced clear cart button adaptiveness -->
                                <?php if (!empty($cart_items)): ?>
                                    <button id="clear-cart-btn" class="clear-cart-adaptive text-orange-500 hover:text-orange-700 text-sm font-medium px-3 sm:px-4 py-2 rounded-lg hover:bg-orange-50 transition-all duration-300 transform hover:scale-105 flex items-center justify-center sm:justify-start min-w-0">
                                        <i class="fas fa-trash mr-1 sm:mr-2 flex-shrink-0"></i>
                                        <span class="hidden xs:inline text-xs sm:text-sm whitespace-nowrap">Clear Cart</span>
                                        <span class="xs:hidden text-xs">Clear</span>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Cart Items List -->
                            <div id="cart-items" class="space-y-3 sm:space-y-4">
                                <?php if (!empty($cart_items)): ?>
                                    <?php foreach ($cart_items as $item): ?>
                                        <!-- ✅ Enhanced responsive cart item layout (Subtotal removed) -->
                                        <div class="cart-item bg-white/60 backdrop-blur-sm border border-white/30 rounded-xl shadow-soft hover:shadow-medium p-3 sm:p-4 transition-all duration-300" data-item-id="<?php echo $item['product_id']; ?>" data-price="<?php echo $item['price']; ?>">

                                            <!-- Mobile Layout (< sm) -->
                                            <div class="block sm:hidden space-y-3">
                                                <!-- Image and Name Row -->
                                                <div class="flex items-center space-x-3">
                                                    <div class="relative overflow-hidden rounded-lg flex-shrink-0">
                                                        <?php
                                                        // Generate product image URL with fallback
                                                        $productImage = !empty($item['image']) && $item['image'] !== DEFAULT_PRODUCT_IMAGE
                                                            ? PRODUCT_IMAGE_URL . htmlspecialchars($item['image'])
                                                            : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE;
                                                        ?>
                                                        <img src="<?php echo $productImage; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-14 h-14 xs:w-16 xs:h-16 object-cover transition-transform duration-300 hover:scale-110">
                                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                                                    </div>
                                                    <div class="flex-1 min-w-0 pr-2">
                                                        <h3 class="font-semibold text-custom-dark text-sm leading-tight truncate"><?php echo htmlspecialchars($item['name']); ?></h3>
                                                        <p class="text-accent font-bold text-base mt-1"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($item['price']); ?></p>
                                                    </div>
                                                </div>

                                                <!-- ✅ FIXED: Quantity Controls Row with proper updating -->
                                                <div class="flex items-center justify-between pt-2">
                                                    <div class="flex items-center space-x-2 xs:space-x-3">
                                                        <button class="quantity-btn decrease-btn w-7 h-7 xs:w-8 xs:h-8 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center hover:from-accent hover:to-orange-600 hover:text-white transition-all duration-300 shadow-soft" data-action="decrease">
                                                            <i class="fas fa-minus text-xs"></i>
                                                        </button>
                                                        <span class="quantity-display font-bold text-custom-dark min-w-[2rem] xs:min-w-[2.5rem] text-center text-sm xs:text-base bg-white/80 px-2 py-1 rounded-lg shadow-soft"><?php echo $item['quantity']; ?></span>
                                                        <button class="quantity-btn increase-btn w-7 h-7 xs:w-8 xs:h-8 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center hover:from-accent hover:to-orange-600 hover:text-white transition-all duration-300 shadow-soft" data-action="increase">
                                                            <i class="fas fa-plus text-xs"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Desktop Layout (>= sm) -->
                                            <div class="hidden sm:flex items-center space-x-4">
                                                <div class="relative overflow-hidden rounded-xl flex-shrink-0">
                                                    <?php
                                                    // Generate product image URL with fallback for desktop
                                                    $productImageDesktop = !empty($item['image']) && $item['image'] !== DEFAULT_PRODUCT_IMAGE
                                                        ? PRODUCT_IMAGE_URL . htmlspecialchars($item['image'])
                                                        : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE;
                                                    ?>
                                                    <img src="<?php echo $productImageDesktop; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-20 h-20 object-cover transition-transform duration-300 hover:scale-110">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="font-semibold text-custom-dark text-lg"><?php echo htmlspecialchars($item['name']); ?></h3>
                                                    <p class="text-accent font-bold text-lg"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($item['price']); ?></p>
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
                                                <!-- ✅ Desktop section cleaned up (Subtotal removed) -->
                                                <div class="text-right">
                                                    <button class="remove-item-btn text-red-500 hover:text-red-700 text-sm px-3 py-1 rounded-lg hover:bg-red-50 transition-all duration-300 transform hover:scale-105" data-item-id="<?php echo $item['product_id']; ?>">
                                                        <i class="fas fa-times mr-1"></i>
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Empty Cart State -->
                            <div id="empty-cart" class="<?php echo empty($cart_items) ? '' : 'hidden'; ?> text-center py-8 sm:py-12 lg:py-16">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32 mx-auto mb-4 sm:mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-gray-400 text-2xl sm:text-3xl lg:text-4xl"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-600 mb-2 sm:mb-3">Your cart is empty</h3>
                                <p class="text-gray-500 mb-4 sm:mb-6 lg:mb-8 max-w-md mx-auto text-sm sm:text-base px-4">Add some delicious frozen foods to get started on your culinary journey!</p>
                                <!-- Continue Shopping button -->
                                <a href="products.php" class="btn-primary text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold inline-flex items-center space-x-2 shadow-accent transform hover:scale-105 transition-all duration-300">
                                    <i class="fas fa-utensils"></i>
                                    <span>Continue Shopping</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <!-- ✅ FIX 1: Added bottom margin for mobile navigation spacing -->
                    <div class="lg:col-span-1 order-1 lg:order-2 mb-4 sm:mb-0">
                        <div class="frosted-glass-accent rounded-2xl p-4 sm:p-6 sticky top-4 sm:top-6 shadow-large border border-accent/20">
                            <h2 class="text-lg sm:text-xl font-semibold text-custom-dark mb-4 sm:mb-6 flex items-center">
                                <i class="fas fa-receipt mr-3 text-accent"></i>
                                Order Summary
                            </h2>

                            <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
                                <div class="flex justify-between items-center p-3 bg-white/50 rounded-lg">
                                    <span class="text-gray-700 font-medium text-sm sm:text-base">Subtotal</span>
                                    <span id="subtotal" class="font-bold text-custom-dark text-base sm:text-lg"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($subtotal); ?></span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-white/50 rounded-lg">
                                    <span class="text-gray-700 font-medium text-sm sm:text-base">Delivery Fee</span>
                                    <span id="delivery-fee" class="font-bold text-custom-dark text-base sm:text-lg"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($delivery_fee); ?></span>
                                </div>
                                <?php if ($subtotal >= 10000): ?>
                                    <div class="text-green-600 text-xs sm:text-sm font-medium p-3 bg-green-50 rounded-lg flex items-center">
                                        <i class="fas fa-check-circle mr-2 flex-shrink-0"></i>
                                        <span>Free delivery on orders <?php echo CURRENCY_SYMBOL; ?>10,000+</span>
                                    </div>
                                <?php else: ?>
                                    <div class="text-amber-600 text-xs sm:text-sm font-medium p-3 bg-amber-50 rounded-lg flex items-center">
                                        <i class="fas fa-info-circle mr-2 flex-shrink-0"></i>
                                        <span>Add <?php echo CURRENCY_SYMBOL; ?><?php echo number_format(10000 - $subtotal); ?> more for free delivery</span>
                                    </div>
                                <?php endif; ?>
                                <hr class="border-white/30">
                                <div class="flex justify-between text-lg sm:text-xl font-bold p-3 sm:p-4 bg-white/70 rounded-xl shadow-soft">
                                    <span class="text-custom-dark">Total</span>
                                    <span id="total" class="text-accent"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($total); ?></span>
                                </div>
                            </div>

                            <!-- Promo Code -->
                            <div class="mb-4 sm:mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Promo Code</label>
                                <div class="flex space-x-2">
                                    <input type="text" id="promo-code" placeholder="Enter promo code" class="input-focus flex-1 px-3 sm:px-4 py-2 sm:py-3 border border-white/30 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent bg-white/80 backdrop-blur-sm text-sm sm:text-base">
                                    <button id="apply-promo-btn" class="bg-white/80 text-custom-dark px-3 sm:px-4 lg:px-6 py-2 sm:py-3 rounded-xl font-semibold hover:bg-accent hover:text-white transition-all duration-300 transform hover:scale-105 shadow-soft text-xs sm:text-sm lg:text-base whitespace-nowrap">
                                        Apply
                                    </button>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <?php if (!empty($cart_items)): ?>
                                <button id="checkout-btn" class="w-full btn-primary text-white py-3 sm:py-4 rounded-xl font-semibold mb-3 sm:mb-4 flex items-center justify-center space-x-2 shadow-accent text-sm sm:text-base">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Proceed to Checkout</span>
                                </button>
                            <?php else: ?>
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-3 sm:py-4 rounded-xl font-semibold mb-3 sm:mb-4 flex items-center justify-center space-x-2 cursor-not-allowed text-sm sm:text-base">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Cart is Empty</span>
                                </button>
                            <?php endif; ?>

                            <!-- Continue Shopping button -->
                            <a href="products.php" class="block w-full text-center bg-white/80 text-custom-dark py-3 sm:py-4 rounded-xl font-semibold hover:bg-white transition-all duration-300 transform hover:scale-105 shadow-soft backdrop-blur-sm text-sm sm:text-base">
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
    <script src="js/script.js"></script>
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
            const clearBtn = document.getElementById('clear-cart-btn');
            console.log('🔍 DEBUG - Clear cart button found:', !!clearBtn); // DEBUG
            console.log('🔍 DEBUG - Button element:', clearBtn); // DEBUG

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

                    console.log('🔍 DEBUG - Cart operation:', {
                        itemId,
                        quantity,
                        action
                    }); // DEBUG

                    try {
                        // ✅ FIXED: Use unified endpoint and correct parameter
                        const res = await fetch('api/update-cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                product_id: itemId, // ✅ FIXED: Changed from 'id' to 'product_id'
                                quantity: quantity,
                                action: action
                            })
                        });

                        console.log('🔍 DEBUG - Response status:', res.status); // DEBUG

                        if (!res.ok) {
                            const errorText = await res.text();
                            console.error('🔍 DEBUG - Error response:', errorText);
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }

                        const result = await res.json();
                        console.log('🔍 DEBUG - API Response:', result); // DEBUG

                        if (result.success) {
                            // Animate quantity change
                            quantityDisplay.style.transform = 'scale(1.2)';
                            quantityDisplay.textContent = result.quantity;
                            setTimeout(() => {
                                quantityDisplay.style.transform = 'scale(1)';
                            }, 200);

                            updateTotalsFromAPI(result);
                            updateCartCount();
                            // showToasted(result.message, 'success');
                        } else {
                            showToasted(result.message || 'Failed to update cart', 'error');
                        }
                    } catch (error) {
                        console.error('🔍 DEBUG - Error details:', error);
                        showToasted('Network error occurred', 'error');
                    } finally {
                        // Restore button
                        this.innerHTML = action === 'increase' ? '<i class="fas fa-plus text-xs sm:text-sm"></i>' : '<i class="fas fa-minus text-xs sm:text-sm"></i>';
                        this.disabled = false;
                    }
                });
            });

            // ✅ UPDATED: Remove item using unified endpoint
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
                        // ✅ FIXED: Use unified endpoint
                        const res = await fetch('api/update-cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                product_id: itemId, // ✅ FIXED: Changed from 'id' to 'product_id'
                                action: 'remove'
                            })
                        });

                        const result = await res.json();
                        if (result.success) {
                            setTimeout(() => {
                                cartItem.remove();
                                updateTotalsFromAPI(result);
                                updateCartCount();
                                checkEmptyCart();
                            }, 300);
                            showToasted(result.message, 'info');
                        } else {
                            // Restore item if failed
                            cartItem.style.transform = 'translateX(0)';
                            cartItem.style.opacity = '1';
                            showToasted(result.message || 'Failed to remove item', 'error');
                        }
                    } catch (error) {
                        cartItem.style.transform = 'translateX(0)';
                        cartItem.style.opacity = '1';
                        showToasted('Network error occurred', 'error');
                    }
                });
            });

            // ✅ UPDATED: Enhanced clear cart with better debugging
            const clearCartBtn = document.getElementById('clear-cart-btn');
            if (clearCartBtn) {
                clearCartBtn.addEventListener('click', async function() {
                    console.log('🔍 DEBUG - Clear cart button clicked!'); // DEBUG

                    const itemCount = document.querySelectorAll('.cart-item').length;
                    console.log('🔍 DEBUG - Items to clear:', itemCount); // DEBUG

                    if (itemCount === 0) {
                        console.log('🔍 DEBUG - No items to clear'); // DEBUG
                        showToasted('Cart is already empty', 'info');
                        return;
                    }

                    const confirmed = await showCustomConfirm(
                        'Clear Cart',
                        `Are you sure you want to remove all ${itemCount} items from your cart? This action cannot be undone.`,
                        'fa-trash-alt',
                        'text-red-500'
                    );

                    console.log('🔍 DEBUG - User confirmed:', confirmed); // DEBUG

                    if (!confirmed) {
                        console.log('🔍 DEBUG - User cancelled clear cart'); // DEBUG
                        return;
                    }

                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1 sm:mr-2"></i><span class="hidden xs:inline text-xs sm:text-sm">Clearing...</span><span class="xs:hidden text-xs">...</span>';
                    this.disabled = true;

                    console.log('🔍 DEBUG - Making API request to clear cart'); // DEBUG

                    try {
                        // ✅ FIXED: Use unified endpoint
                        const res = await fetch('api/update-cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                action: 'clear'
                            })
                        });

                        console.log('🔍 DEBUG - Clear cart response status:', res.status); // DEBUG
                        console.log('🔍 DEBUG - Clear cart response ok:', res.ok); // DEBUG

                        if (!res.ok) {
                            const errorText = await res.text();
                            console.error('🔍 DEBUG - Clear cart error response:', errorText); // DEBUG
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }

                        const result = await res.json();
                        console.log('🔍 DEBUG - Clear cart API response:', result); // DEBUG

                        if (result.success) {
                            console.log('🔍 DEBUG - Clear cart successful, animating items out'); // DEBUG

                            // Animate all items out
                            const items = document.querySelectorAll('.cart-item');
                            console.log('🔍 DEBUG - Found', items.length, 'items to animate'); // DEBUG

                            items.forEach((item, index) => {
                                setTimeout(() => {
                                    console.log('🔍 DEBUG - Animating item', index); // DEBUG
                                    item.style.transform = 'translateX(-100%)';
                                    item.style.opacity = '0';
                                    setTimeout(() => {
                                        console.log('🔍 DEBUG - Removing item', index); // DEBUG
                                        item.remove();
                                    }, 300);
                                }, index * 100);
                            });

                            setTimeout(() => {
                                console.log('🔍 DEBUG - Updating totals and cart count'); // DEBUG
                                updateTotalsFromAPI(result);
                                updateCartCount();
                                checkEmptyCart();
                            }, items.length * 100 + 300);

                            showToasted(result.message, 'success');
                        } else {
                            console.error('🔍 DEBUG - Clear cart failed:', result.message); // DEBUG
                            showToasted(result.message || 'Failed to clear cart', 'error');
                        }
                    } catch (error) {
                        console.error('🔍 DEBUG - Clear cart network error:', error); // DEBUG
                        showToasted('Network error occurred', 'error');
                    } finally {
                        console.log('🔍 DEBUG - Restoring clear cart button'); // DEBUG
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                });
            } else {
                console.error('🔍 DEBUG - Clear cart button not found!'); // DEBUG
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
            const backBtn = document.getElementById('backBtn');
            if (backBtn) {
                backBtn.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                        window.history.back();
                    }, 150);
                });
            }
        }

        // Enhanced update totals with responsive calculation
        function updateTotalsFromAPI(apiResponse) {
            console.log('🔍 DEBUG - Updating UI from API:', apiResponse);

            // Update subtotal
            const subtotalEl = document.getElementById('subtotal');
            if (subtotalEl && apiResponse.subtotal !== undefined) {
                subtotalEl.textContent = `<?php echo CURRENCY_SYMBOL; ?>${apiResponse.subtotal.toLocaleString()}`;
                subtotalEl.style.transform = 'scale(1.1)';
                setTimeout(() => subtotalEl.style.transform = 'scale(1)', 200);
            }

            // Update delivery fee
            const deliveryEl = document.getElementById('delivery-fee');
            if (deliveryEl && apiResponse.delivery_fee !== undefined) {
                deliveryEl.textContent = `<?php echo CURRENCY_SYMBOL; ?>${apiResponse.delivery_fee.toLocaleString()}`;
                deliveryEl.style.transform = 'scale(1.1)';
                setTimeout(() => deliveryEl.style.transform = 'scale(1)', 200);
            }

            // Update total
            const totalEl = document.getElementById('total');
            if (totalEl && apiResponse.total !== undefined) {
                totalEl.textContent = `<?php echo CURRENCY_SYMBOL; ?>${apiResponse.total.toLocaleString()}`;
                totalEl.style.transform = 'scale(1.1)';
                setTimeout(() => totalEl.style.transform = 'scale(1)', 200);
            }

            // Update cart count in badge
            const cartBadge = document.getElementById('cartCount');
            if (cartBadge && apiResponse.cart_count !== undefined) {
                cartBadge.textContent = apiResponse.cart_count;
                cartBadge.parentElement.style.display = apiResponse.cart_count > 0 ? 'flex' : 'none';
            }

            // ✅ ENHANCED: Update individual cart item quantity displays
            if (apiResponse.product_id && apiResponse.quantity !== undefined) {
                const cartItem = document.querySelector(`.cart-item[data-item-id="${apiResponse.product_id}"]`);
                if (cartItem) {
                    console.log('🔍 DEBUG - Updating item quantity display for product:', apiResponse.product_id, 'to qty:', apiResponse.quantity);

                    // Method 2: Fallback - find by text content
                    const qtyDisplays = cartItem.querySelectorAll('.text-right span');
                    qtyDisplays.forEach(span => {
                        if (span.textContent.includes('Qty:')) {
                            span.textContent = `Qty: ${apiResponse.quantity}`;
                            span.style.transform = 'scale(1.2)';
                            setTimeout(() => span.style.transform = 'scale(1)', 200);
                        }
                    });

                    // Method 3: Also update data attribute for consistency
                    cartItem.setAttribute('data-current-qty', apiResponse.quantity);
                }
            }
        }

        // ✅ Enhanced empty cart check with Clear Cart button visibility
        function checkEmptyCart() {
            const cartItems = document.getElementById('cart-items');
            const emptyCart = document.getElementById('empty-cart');
            const items = document.querySelectorAll('.cart-item');
            const clearCartBtn = document.getElementById('clear-cart-btn');

            if (items.length === 0) {
                cartItems.classList.add('hidden');
                emptyCart.classList.remove('hidden');
                emptyCart.classList.add('animate-scale-in');

                // Hide clear cart button when cart is empty
                if (clearCartBtn) {
                    clearCartBtn.style.display = 'none';
                }
            } else {
                cartItems.classList.remove('hidden');
                emptyCart.classList.add('hidden');

                // Show clear cart button when cart has items
                if (clearCartBtn) {
                    clearCartBtn.style.display = 'flex';
                }
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