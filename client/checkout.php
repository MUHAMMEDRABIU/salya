<?php
require_once 'util/util.php';
require_once 'initialize.php';
require_once 'partials/headers.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartCount = array_sum(array_column($cart_items, 'quantity'));
?>

<body class="font-dm bg-gray min-h-screen blob-bg">
    <!-- Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-accent opacity-20 rounded-full filter blur-3xl"></div>
        <div class="absolute top-1/2 -left-32 w-64 h-64 bg-secondary opacity-15 rounded-full filter blur-3xl"></div>
        <div class="absolute bottom-20 right-1/4 w-48 h-48 bg-accent opacity-10 rounded-full filter blur-2xl"></div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <div class="relative z-10 container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header -->
        <header class="mb-8">
            <?php include 'partials/top-nav.php'; ?>
            <!-- Progress Steps -->
            <div class="flex items-center justify-center space-x-4 mb-8">
                <div class="flex items-center space-x-4">
                    <div id="step-1" class="step-active w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all duration-300">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="w-16 h-1 bg-gray-300 rounded-full">
                        <div id="progress-1" class="h-1 bg-orange-500 rounded-full transition-all duration-500" style="width: 100%;"></div>
                    </div>
                    <div id="step-2" class="step-inactive w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all duration-300">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="w-16 h-1 bg-gray-300 rounded-full">
                        <div id="progress-2" class="h-1 bg-orange-500 rounded-full transition-all duration-500" style="width: 0%;"></div>
                    </div>
                    <div id="step-3" class="step-inactive w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all duration-300">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Step 1: Checkout -->
                <div id="checkout-step" class="animate-fade-in">
                    <h2 class="text-2xl font-bold text-dark mb-6">Checkout</h2>
                    <form id="checkout-form" class="space-y-6">
                        <!-- Personal Information -->
                        <div class="bg-white rounded-2xl p-6 shadow-soft border border-slate-200">
                            <h3 class="text-lg font-semibold text-dark mb-4">Personal information:</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" id="firstName" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" placeholder="e.g Ademu" required>
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter your first name</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" id="lastName" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" placeholder="e.g Rabiu" required>
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter your last name</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <div class="relative">
                                        <input type="tel" id="phone" class="form-input w-full px-4 py-3 pl-16 rounded-xl focus:outline-none" placeholder="70-3949-5494" required inputmode="numeric" maxlength="11">
                                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 flex items-center">
                                            <span class="text-red-500 text-lg mr-1">
                                                <!-- nigerian flag image -->
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Flag_of_Nigeria.svg/32px-Flag_of_Nigeria.svg.png" alt="Nigeria Flag" class="w-6 h-6 rounded-full">
                                            </span>
                                        </div>
                                    </div>
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter a valid phone number</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" id="email" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" placeholder="e.g adamurabiu@gmail.com" required>
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter a valid email address</span>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Details -->
                        <div class="bg-white rounded-2xl p-6 shadow-soft border border-slate-200">
                            <h3 class="text-lg font-semibold text-dark mb-4">Delivery details:</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                    <select id="city" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" required>
                                        <option value="">Select City</option>
                                        <option value="abuja" selected>Abuja</option>
                                        <option value="kaduna">Kaduna</option>
                                        <option value="lagos">Lagos</option>
                                    </select>
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please select a city</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <input type="text" id="address" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" placeholder="e.g 34 Main St" required>
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter your address</span>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                    <input type="text" id="postalCode" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" placeholder="e.g 223466" required>
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter a valid postal code</span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- If cart is not empty show continue else show back to products page -->
                    <div class="flex justify-end mt-6">
                        <?php
                        if (empty($cart_items)) {
                        ?>
                            <div id="cart-empty-message" class="text-center text-gray-500">
                                Your cart is empty. <a href="dashboard.php" class="text-accent">Continue shopping</a>
                            </div>
                        <?php
                        } else {
                        ?>
                            <button id="continue-btn" class="bg-accent hover:bg-orange-600 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-accent hover:shadow-large flex items-center space-x-2">
                                <span id="continue-text">Continue</span>
                                <div id="continue-spinner" class="loading-spinner hidden"></div>
                            </button>
                        <?php
                        }
                        ?>
                    </div>
                </div>

                <!-- Step 2: Payment -->
                <div id="payment-step" class="hidden">
                    <h2 class="text-2xl font-bold text-dark mb-6">Payment</h2>

                    <!-- Payment Methods -->
                    <div class="bg-white rounded-2xl p-6 shadow-soft border border-slate-200 mb-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="payment-method rounded-xl p-4 text-center selected" data-method="mastercard">
                                <div class="w-12 h-8 mx-auto mb-2 bg-gradient-to-r from-red-500 to-yellow-500 rounded flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">MC</span>
                                </div>
                                <span class="text-sm text-gray-600">Mastercard</span>
                            </div>
                            <div class="payment-method rounded-xl p-4 text-center" data-method="visa">
                                <div class="w-12 h-8 mx-auto mb-2 bg-blue-600 rounded flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">VISA</span>
                                </div>
                                <span class="text-sm text-gray-600">Visa</span>
                            </div>
                            <div class="payment-method rounded-xl p-4 text-center" data-method="paypal">
                                <div class="w-12 h-8 mx-auto mb-2 bg-blue-500 rounded flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">PP</span>
                                </div>
                                <span class="text-sm text-gray-600">PayPal</span>
                            </div>
                            <div class="payment-method rounded-xl p-4 text-center" data-method="applepay">
                                <div class="w-12 h-8 mx-auto mb-2 bg-black rounded flex items-center justify-center">
                                    <i class="fab fa-apple text-white"></i>
                                </div>
                                <span class="text-sm text-gray-600">Apple Pay</span>
                            </div>
                        </div>

                        <form id="payment-form" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name on Card</label>
                                    <input type="text" id="cardName" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" placeholder="Anna Montgomery" required>
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter the name on card</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                    <input type="text" id="cardNumber" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" placeholder="1234 - 5678 - 9012 - 3456" maxlength="19" required inputmode="numeric">
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter a valid card number</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiration Date</label>
                                    <input type="text" id="cardExpiry" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" placeholder="06/25" maxlength="5" required>
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter expiration date</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">CVC Code</label>
                                    <input type="text" id="cardCVC" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none" placeholder="123" maxlength="3" required inputmode="numeric">
                                    <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter CVC code</span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>3 digit code on the back of your card 3
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center mt-4">
                                <input type="checkbox" id="billingAddress" class="w-5 h-5 text-accent border-slate-300 rounded focus:ring-accent" checked>
                                <label for="billingAddress" class="ml-3 text-sm text-gray-700">
                                    <i class="fas fa-check text-orange-500 mr-2"></i>
                                    Billing address is the same as shipping address.
                                </label>
                            </div>
                        </form>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button id="back-btn" class="border border-slate-300 text-gray-700 font-semibold px-8 py-3 rounded-xl transition-all duration-300 hover:bg-slate-50 shadow-soft hover:shadow-medium">
                            Back
                        </button>
                        <button id="purchase-btn" class="bg-accent hover:bg-orange-600 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-accent hover:shadow-large flex items-center space-x-2">
                            <span id="purchase-text">Purchase</span>
                            <div id="purchase-spinner" class="loading-spinner hidden"></div>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Success -->
                <div id="success-step" class="hidden text-center">
                    <div class="animate-scale-in">
                        <!-- Success Vector Illustration -->
                        <div class="w-80 h-80 mx-auto mb-8 relative">
                            <svg viewBox="0 0 400 400" class="w-full h-full animate-float">
                                <!-- Background Circle -->
                                <circle cx="200" cy="200" r="180" fill="#f0fdf4" stroke="#bbf7d0" stroke-width="2" />

                                <!-- Delivery Person -->
                                <g transform="translate(150, 120)">
                                    <!-- Head -->
                                    <circle cx="50" cy="40" r="25" fill="#fbbf24" />
                                    <!-- Hair -->
                                    <path d="M25 35 Q50 15 75 35 Q70 25 50 25 Q30 25 25 35" fill="#92400e" />
                                    <!-- Face -->
                                    <circle cx="45" cy="38" r="2" fill="#1f2937" />
                                    <circle cx="55" cy="38" r="2" fill="#1f2937" />
                                    <path d="M45 45 Q50 50 55 45" stroke="#1f2937" stroke-width="2" fill="none" />

                                    <!-- Body -->
                                    <rect x="35" y="65" width="30" height="40" rx="15" fill="#F97316" />
                                    <!-- Arms -->
                                    <rect x="20" y="75" width="15" height="25" rx="7" fill="#fbbf24" />
                                    <rect x="65" y="75" width="15" height="25" rx="7" fill="#fbbf24" />

                                    <!-- Legs -->
                                    <rect x="40" y="105" width="8" height="30" rx="4" fill="#1e40af" />
                                    <rect x="52" y="105" width="8" height="30" rx="4" fill="#1e40af" />

                                    <!-- Delivery Bag -->
                                    <rect x="10" y="85" width="20" height="15" rx="3" fill="#22c55e" />
                                    <rect x="12" y="87" width="16" height="11" rx="2" fill="#16a34a" />
                                    <text x="20" y="95" text-anchor="middle" fill="white" font-size="8" font-weight="bold">W&R</text>
                                </g>

                                <!-- Floating Elements -->
                                <g class="animate-wiggle">
                                    <circle cx="100" cy="100" r="8" fill="#fbbf24" opacity="0.6" />
                                    <circle cx="320" cy="120" r="6" fill="#F97316" opacity="0.5" />
                                    <circle cx="80" cy="300" r="10" fill="#22c55e" opacity="0.4" />
                                    <circle cx="330" cy="280" r="7" fill="#fbbf24" opacity="0.6" />
                                </g>

                                <!-- Success Checkmark -->
                                <g transform="translate(280, 80)">
                                    <circle cx="30" cy="30" r="25" fill="#22c55e" />
                                    <path d="M20 30 L27 37 L40 23" stroke="white" stroke-width="3" fill="none" stroke-linecap="round" />
                                </g>

                                <!-- Motion Lines -->
                                <g opacity="0.3">
                                    <path d="M50 200 Q100 190 150 200" stroke="#F97316" stroke-width="2" fill="none" />
                                    <path d="M60 220 Q110 210 160 220" stroke="#22c55e" stroke-width="2" fill="none" />
                                    <path d="M70 240 Q120 230 170 240" stroke="#fbbf24" stroke-width="2" fill="none" />
                                </g>
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-3xl font-bold text-dark mb-4">Thank! Your order is on the way.</h2>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        We've received your order and will send you updates via email and SMS as it's prepared and shipped.
                    </p>

                    <button id="track-btn" class="bg-accent hover:bg-orange-600 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-accent hover:shadow-large">
                        Track Your Order
                    </button>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="frosted-glass rounded-2xl p-6 sticky top-8 shadow-medium">
                    <h3 class="text-lg font-semibold text-dark mb-6">Order Summary</h3>

                    <div class="space-y-4 mb-6">
                        <?php if (!empty($cart_items)) : ?>
                            <?php foreach ($cart_items as $item) : ?>
                                <div class="flex items-center space-x-4 p-3 bg-white bg-opacity-50 rounded-xl shadow-soft">
                                    <img src="../assets/uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 rounded-lg object-cover">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-dark"><?= htmlspecialchars($item['name']) ?></h4>
                                        <div class="flex items-center justify-between mt-2">
                                            <!-- Quantity Adjusters -->
                                            <div class="flex items-center space-x-2" data-id="<?= $item['product_id']; ?>">
                                                <button class="qty-btn decrease w-6 h-6 rounded-full border border-slate-300 flex items-center justify-center text-gray-500 hover:bg-slate-100 transition-colors">-</button>
                                                <span class="qty-count font-medium"><?= $item['quantity'] ?></span>
                                                <button class="qty-btn increase w-6 h-6 rounded-full border border-slate-300 flex items-center justify-center text-gray-500 hover:bg-slate-100 transition-colors">+</button>
                                            </div>
                                            <span class="font-semibold text-dark">₦<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                        </div>
                                    </div>
                                    <button class="remove-btn text-gray-100 hover:bg-red-600 transition-colors" data-id="<?= $item['product_id'] ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-center text-gray-500">Your cart is empty.</p>
                        <?php endif; ?>
                    </div>


                    <?php
                    $subtotal = 0;
                    foreach ($cart_items as $item) {
                        $subtotal += $item['price'] * $item['quantity'];
                    }
                    $delivery_fee = $subtotal >= 10000 ? 0 : 500;
                    $tax = 0; // Update if you have a tax policy
                    $total = $subtotal + $delivery_fee + $tax;
                    ?>
                    <div class="border-t border-slate-200 pt-4 space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span id="subtotal-value">₦<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Delivery:</span>
                            <span id="delivery-value">₦<?= number_format($delivery_fee, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax:</span>
                            <span id="tax-value">₦<?= number_format($tax, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-dark pt-2 border-t border-slate-200">
                            <span>Total:</span>
                            <span id="total-value">₦<?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bottom navigation include -->
        <?php #include 'partials/bottom-nav.php'; 
        ?>
    </div>

    <script src="../assets/js/toast.js"></script>
    <script src="js/script.js"></script>
    <script>
        window.productId = <?= json_encode(array_column($cart_items, 'product_id')) ?>;
        window.cartItems = <?= json_encode($cart_items) ?>;
        window.cartTotals = {
            subtotal: <?= $subtotal ?>,
            delivery_fee: <?= $delivery_fee ?>,
            total: <?= $total ?>
        };
    </script>

    <script>
        // Mock API endpoints for demonstration
        const API_BASE = 'https://jsonplaceholder.typicode.com'; // Using JSONPlaceholder for demo

        // Store checkout data
        let checkoutData = {};
        let paymentData = {};

        // AJAX helper function
        async function makeRequest(url, method = 'GET', data = null) {
            try {
                const options = {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                };

                if (data) {
                    options.body = JSON.stringify(data);
                }

                const response = await fetch(url, options);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();
            } catch (error) {
                console.error('API request failed:', error);
                throw error;
            }
        }

        // Save checkout data to database
        async function saveCheckoutData(data) {
            const response = await fetch('api/save-checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (!result.success) throw new Error(result.message || 'Failed to save checkout data');
            return result;
        }

        async function fetchCheckoutData() {
            try {
                const response = await fetch('api/get-checkout-data.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                const result = await response.json();
                if (result.success && result.data) {
                    const data = result.data;
                    if (data.first_name) document.getElementById('firstName').value = data.first_name;
                    if (data.last_name) document.getElementById('lastName').value = data.last_name;
                    if (data.phone) document.getElementById('phone').value = data.phone;
                    if (data.email) document.getElementById('email').value = data.email;
                    if (data.city) document.getElementById('city').value = data.city;
                    if (data.address) document.getElementById('address').value = data.address;
                    if (data.postal_code) document.getElementById('postalCode').value = data.postal_code;
                }

            } catch (error) {
                console.warn('Could not fetch checkout data', error);
            }
        }

        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const parent = btn.closest('[data-id]');
                const productId = parent.getAttribute('data-id');
                const action = btn.classList.contains('increase') ? 'increase' : 'decrease';
                const qtySpan = parent.querySelector('.qty-count');
                const itemTotalSpan = parent.closest('.space-x-4').querySelector('.item-total-price');

                try {
                    const res = await fetch('api/update-quantity.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            action: action
                        })
                    });

                    const data = await res.json();

                    if (data.success) {
                        // Update quantity text
                        qtySpan.textContent = data.quantity;

                        // Update item total
                        if (itemTotalSpan) {
                            itemTotalSpan.textContent = `₦${data.item_total.toFixed(2)}`;
                        }

                        // Update summary totals
                        document.getElementById('subtotal-value').textContent = `₦${data.subtotal.toFixed(2)}`;
                        document.getElementById('delivery-value').textContent = `₦${data.delivery_fee.toFixed(2)}`;
                        document.getElementById('tax-value').textContent = `₦${data.tax.toFixed(2)}`;
                        document.getElementById('total-value').textContent = `₦${data.total.toFixed(2)}`;

                        // Optional: Update cart count badge
                        if (document.getElementById('cartCount')) {
                            document.getElementById('cartCount').textContent = `${data.cartCount}`;
                        }

                    } else {
                        showToasted(data.message || 'Error updating cart', 'error');
                    }

                } catch (err) {
                    console.error('AJAX Error:', err);
                    showToasted('Failed to update cart. Please try again.', 'error');
                }
            });
        });


        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const itemId = btn.getAttribute('data-id');

                // if (!confirm('Are you sure you want to remove this item?')) return;

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

                    const data = await res.json();

                    if (data.success) {
                        showToasted('Item removed.', 'success');

                        // Remove item from DOM
                        const itemContainer = btn.closest('.flex.items-center.space-x-4');
                        itemContainer?.remove();

                        // Update totals
                        document.getElementById('subtotal-value').textContent = `₦${data.subtotal.toFixed(2)}`;
                        document.getElementById('delivery-value').textContent = `₦${data.delivery_fee.toFixed(2)}`;
                        document.getElementById('total-value').textContent = `₦${data.total.toFixed(2)}`;

                        // Update cart count
                        const cartCountEl = document.getElementById('cartCount');
                        if (cartCountEl) {
                            cartCountEl.textContent = `${data.cartCount}`;
                        }

                        // If cart is empty, optionally hide or show empty state
                        if (data.cartCountEl == 0) {
                            document.querySelector('.frosted-glass').innerHTML = '<p class="text-center text-gray-600 py-6">Your cart is empty.</p>';
                        }

                    } else {
                        showToasted(data.message || 'Failed to remove item.', 'error');
                    }

                } catch (err) {
                    console.error('Remove error:', err);
                    showToasted('Error removing item.', 'error');
                }
            });
        });





        // Step management
        let currentStep = Number(localStorage.getItem('checkoutStep')) || 1;
        const totalSteps = 3;

        function updateStepIndicators(step) {
            for (let i = 1; i <= totalSteps; i++) {
                const stepEl = document.getElementById(`step-${i}`);
                const progressEl = document.getElementById(`progress-${i}`);

                if (i < step) {
                    stepEl.className = 'step-completed w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all duration-300';
                    if (progressEl) progressEl.style.width = '100%';
                } else if (i === step) {
                    stepEl.className = 'step-active w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all duration-300';
                    if (progressEl) progressEl.style.width = '0%';
                } else {
                    stepEl.className = 'step-inactive w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all duration-300';
                    if (progressEl) progressEl.style.width = '0%';
                }
            }
            localStorage.setItem('checkoutStep', step);
        }

        function showStep(step) {
            // Hide all steps
            document.getElementById('checkout-step').classList.add('hidden');
            document.getElementById('payment-step').classList.add('hidden');
            document.getElementById('success-step').classList.add('hidden');

            // Hide order summary on last step
            const orderSummary = document.querySelector('.frosted-glass.rounded-2xl.p-6.sticky.top-8.shadow-medium');
            if (orderSummary) {
                if (step === 3) {
                    orderSummary.classList.add('hidden');
                    setTimeout(() => {
                        // clear session storage
                        sessionStorage.removeItem('cart');
                        sessionStorage.removeItem('checkoutStep');
                        localStorage.removeItem('checkoutStep');

                        fetch('api/clear-cart.php', {
                            method: 'POST'
                        });

                    }, 500);
                } else {
                    orderSummary.classList.remove('hidden');
                }
            }

            // Show current step with animation
            setTimeout(() => {
                if (step === 1) {
                    document.getElementById('checkout-step').classList.remove('hidden');
                    document.getElementById('checkout-step').classList.add('animate-slide-right');
                } else if (step === 2) {
                    document.getElementById('payment-step').classList.remove('hidden');
                    document.getElementById('payment-step').classList.add('animate-slide-left');
                } else if (step === 3) {
                    document.getElementById('success-step').classList.remove('hidden');
                    document.getElementById('success-step').classList.add('animate-scale-in');
                }
            }, 100);

            updateStepIndicators(step);
            currentStep = step;
            localStorage.setItem('checkoutStep', step);
        }

        // Form validation logic for step 1 (personal + delivery info)
        function validateCheckoutForm() {
            const fields = ['firstName', 'lastName', 'phone', 'email', 'city', 'address', 'postalCode'];
            let isValid = true;

            fields.forEach(field => {
                const input = document.getElementById(field);

                if (!input) {
                    console.warn(`Missing input field: ${field}`);
                    isValid = false;
                    return;
                }

                const errorMsg = input.parentElement.querySelector('.error-message');
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    if (errorMsg) errorMsg.classList.remove('hidden');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-orange-500');
                    if (errorMsg) errorMsg.classList.add('hidden');
                }
            });

            // Email validation
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.value && !emailRegex.test(email.value)) {
                email.classList.add('border-red-500');
                email.parentElement.querySelector('.error-message').classList.remove('hidden');
                isValid = false;
            }

            return isValid;
        }

        function validatePaymentForm() {
            const fields = ['cardName', 'cardNumber', 'cardExpiry', 'cardCVC'];
            let isValid = true;

            fields.forEach(field => {
                const input = document.getElementById(field);
                const errorMsg = input.parentElement.querySelector('.error-message') || null;

                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    if (errorMsg) errorMsg.classList.remove('hidden');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-orange-500');
                    if (errorMsg) errorMsg.classList.add('hidden');
                }

            });

            return isValid;
        }

        // Card formatting
        function formatCardNumber(input) {
            let value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            input.value = formattedValue;
        }

        function formatExpiry(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            input.value = value;
        }

        // Loading state management
        function setButtonLoading(buttonId, textId, spinnerId, isLoading) {
            const button = document.getElementById(buttonId);
            const text = document.getElementById(textId);
            const spinner = document.getElementById(spinnerId);

            if (isLoading) {
                button.disabled = true;
                button.classList.add('opacity-75', 'cursor-not-allowed');
                text.textContent = 'Processing...';
                spinner.classList.remove('hidden');
            } else {
                button.disabled = false;
                button.classList.remove('opacity-75', 'cursor-not-allowed');
                spinner.classList.add('hidden');
                text.textContent = 'Continue';
            }
        }

        // Event listeners
        document?.getElementById('continue-btn').addEventListener('click', async () => {
            if (validateCheckoutForm()) {
                setButtonLoading('continue-btn', 'continue-text', 'continue-spinner', true);

                try {
                    // Collect form data
                    const formData = {
                        firstName: document.getElementById('firstName').value,
                        lastName: document.getElementById('lastName').value,
                        phone: document.getElementById('phone').value,
                        email: document.getElementById('email').value,
                        city: document.getElementById('city').value,
                        address: document.getElementById('address').value,
                        postalCode: document.getElementById('postalCode').value,
                        timestamp: new Date().toISOString()
                    };

                    // Save to database
                    await saveCheckoutData(formData);

                    // Success - move to next step
                    showStep(2);
                    showToasted('Personal information saved successfully!', 'success');

                } catch (error) {
                    showToasted('Failed to save information. Please try again.', 'error');
                } finally {
                    setButtonLoading('continue-btn', 'continue-text', 'continue-spinner', false);
                    document.getElementById('continue-text').textContent = 'Continue';
                }
            } else {
                showToasted('Please fill in all required fields correctly.', 'error');
            }
        });

        document.getElementById('back-btn').addEventListener('click', () => {
            showStep(1);
        });
        document.getElementById('purchase-btn').addEventListener('click', async () => {
            if (!validatePaymentForm()) {
                showToasted('Please fill in all payment details correctly.', 'error');
                return;
            }

            setButtonLoading('purchase-btn', 'purchase-text', 'purchase-spinner', true);

            try {
                const paymentFormData = {
                    cardName: document.getElementById('cardName').value,
                    cardNumber: document.getElementById('cardNumber').value.replace(/\s/g, ''),
                    cardExpiry: document.getElementById('cardExpiry').value,
                    billingAddressSame: document.getElementById('billingAddress').checked,
                    paymentMethod: document.querySelector('.payment-method.selected').dataset.method,
                    amount: window.cartTotals.total,
                    currency: 'NGN', // Use your currency
                    timestamp: new Date().toISOString()
                };

                const orderDetails = {
                    product_id: window.productId,
                    items: window.cartItems,
                    subtotal: window.cartTotals.subtotal,
                    delivery_fee: window.cartTotals.delivery_fee,
                    total_amount: window.cartTotals.total,
                    timestamp: new Date().toISOString()
                };

                // Save card details securely
                const cardRes = await fetch('api/save-card-details.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(paymentFormData)
                });

                if (!cardRes.ok) throw new Error('Card save failed');
                const cardResult = await cardRes.json();
                if (!cardResult.success) throw new Error(cardResult.message || 'Failed to save card details');

                // Save order
                const orderRes = await fetch('api/place-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderDetails)
                });

                if (!orderRes.ok) throw new Error('Order save failed');
                const orderResult = await orderRes.json();
                if (!orderResult.success) throw new Error(orderResult.message || 'Failed to place order');

                // Success
                showStep(3);
                showToasted('Payment processed and order placed successfully!', 'success');

                await makeRequest('api/send-confirmation-email.php', 'POST', {
                    email: document.getElementById('email')?.value,
                    orderDetails: orderDetails
                });

                await fetch('api/clear-cart.php', {
                    method: 'POST'
                });
                sessionStorage.clear();
                localStorage.removeItem('checkoutStep');

                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 20000);

            } catch (error) {
                console.error(error);
                showToasted('Payment or order processing failed. Please try again.', 'error');
            } finally {
                setButtonLoading('purchase-btn', 'purchase-text', 'purchase-spinner', false);
                document.getElementById('purchase-text').textContent = 'Purchase';
            }
        });


        document.getElementById('track-btn').addEventListener('click', () => {
            showToasted('Redirecting to order tracking...', 'info');
            setTimeout(() => {
                window.location.href = 'orders.php';
            }, 1500);
        });

        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', () => {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                method.classList.add('selected');
                showToasted(`${method.dataset.method} selected as payment method.`, 'info');
            });
        });

        // Card number formatting
        document.getElementById('cardNumber').addEventListener('input', (e) => formatCardNumber(e.target));
        document.getElementById('cardExpiry').addEventListener('input', (e) => formatExpiry(e.target));

        // CVC validation
        document.getElementById('cardCVC').addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Real-time validation
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('blur', () => {
                if (input.value.trim()) {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-orange-500');
                    input.parentElement.querySelector('.error-message')?.classList.add('hidden');
                }
            });
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            fetchCheckoutData();
            updateStepIndicators(1);
            showStep(currentStep);
        });
    </script>
</body>

</html>