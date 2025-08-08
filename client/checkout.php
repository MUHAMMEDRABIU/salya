<?php
require_once 'initialize.php';
require_once '../config/constants.php';
require_once 'util/util.php';
require_once '../helpers/monnify.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items from database
$cart_items = getUserCartItems($pdo, $user_id);
$cartTotals = getUserCartTotals($pdo, $user_id);

// Use database totals
$subtotal = $cartTotals['subtotal'];
$delivery_fee = $cartTotals['delivery_fee'];
$tax = $cartTotals['tax'];
$total = $cartTotals['total'];
$cartCount = $cartTotals['item_count'];

// Redirect if cart is empty
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

// Get user virtual account
$account = getUserVirtualAccount($pdo, $_SESSION['user_id']);
$accountNumber = $account['account_number'] ?? 'Unavailable';
$accountName = $account['full_customer_name'] ?? $account['account_name'] ?? 'Unavailable';
$bankName = $account['bank_name'] ?? 'Unavailable';
$customerName = $user['first_name'] . ' ' . $user['last_name'];
$customerEmail = $user['email'];

require_once 'partials/headers.php';
?>

<body class="font-dm bg-gray-50 min-h-screen blob-bg">
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
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" id="checkoutContainer">
            <!-- Payment Methods Section -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Payment Method Selection -->
                <div class="bg-white rounded-2xl p-6 shadow-soft border border-slate-200">
                    <h2 class="text-xl font-bold text-dark mb-6 flex items-center">
                        <i class="fas fa-credit-card text-accent mr-3"></i>
                        Select Payment Method
                    </h2>

                    <div class="space-y-4">
                        <!-- Bank Transfer -->
                        <div>
                            <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" class="payment-option hidden" checked>
                            <label for="bank_transfer" class="payment-label flex items-center p-4 border-2 border-accent bg-orange-50 rounded-xl cursor-pointer transition-all duration-300 shadow-lg shadow-orange-200/50">
                                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mr-4">
                                    <i class="fas fa-university text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-dark">Bank Transfer</h3>
                                    <p class="text-sm text-gray-600">Transfer to our virtual account number</p>
                                </div>
                                <div class="w-5 h-5 border-2 border-accent rounded-full flex items-center justify-center transition-all duration-300">
                                    <div class="w-2 h-2 bg-accent rounded-full radio-dot transition-opacity duration-300" style="opacity: 1;"></div>
                                </div>
                            </label>
                        </div>

                        <!-- Card Payment -->
                        <div>
                            <input type="radio" id="card_payment" name="payment_method" value="card_payment" class="payment-option hidden">
                            <label for="card_payment" class="payment-label flex items-center p-4 border-2 border-transparent rounded-xl hover:border-accent cursor-pointer transition-all duration-300 hover:shadow-lg">
                                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mr-4">
                                    <i class="fas fa-credit-card text-green-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-dark">Card Payment</h3>
                                    <p class="text-sm text-gray-600">Pay with Visa, Mastercard, or Verve</p>
                                </div>
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center transition-all duration-300">
                                    <div class="w-2 h-2 bg-accent rounded-full opacity-0 radio-dot transition-opacity duration-300"></div>
                                </div>
                            </label>
                        </div>

                        <!-- Mobile Money -->
                        <div>
                            <input type="radio" id="mobile_money" name="payment_method" value="mobile_money" class="payment-option hidden">
                            <label for="mobile_money" class="payment-label flex items-center p-4 border-2 border-transparent rounded-xl hover:border-accent cursor-pointer transition-all duration-300 hover:shadow-lg">
                                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg mr-4">
                                    <i class="fas fa-mobile-alt text-purple-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-dark">Mobile Money</h3>
                                    <p class="text-sm text-gray-600">Pay with Opay, PalmPay, Kuda, etc.</p>
                                </div>
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center transition-all duration-300">
                                    <div class="w-2 h-2 bg-accent rounded-full opacity-0 radio-dot transition-opacity duration-300"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Payment Details Section -->
                <div id="paymentDetails" class="bg-white rounded-2xl p-6 shadow-soft border border-slate-200">
                    <!-- Bank Transfer Details (Default) -->
                    <div id="bank_transferDetails" class="payment-details">
                        <h3 class="text-lg font-bold text-dark mb-4 flex items-center">
                            <i class="fas fa-university text-accent mr-2"></i>
                            Bank Transfer Details
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Bank Name:</span>
                                    <p class="font-semibold text-dark"><?= $bankName ?></p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Account Name:</span>
                                    <p class="font-semibold text-dark"><?= $accountName ?></p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <span class="text-gray-600 text-sm">Virtual Account Number:</span>
                                <div class="flex items-center justify-between bg-white rounded-lg p-3 mt-2 border-2 border-accent">
                                    <span class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-dark tracking-wider" id="accountNumber">
                                        <?php echo $accountNumber; ?>
                                    </span>
                                    <button onclick="copyAccountNumber()" class="bg-accent hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 flex items-center transform hover:scale-105">
                                        <i id="copyIcon" class="fas fa-copy mr-2"></i>
                                        <span id="copyText">Copy</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-3 flex-shrink-0"></i>
                                <div>
                                    <h4 class="font-semibold text-yellow-800 mb-2">Important Instructions:</h4>
                                    <ul class="text-sm text-yellow-700 space-y-1">
                                        <li>• Transfer the exact amount: <strong><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($total, 2); ?></strong></li>
                                        <li>• This account number is permanently yours for all orders</li>
                                        <li>• Payment will be verified by our admin team</li>
                                        <li>• Click "I Have Made Payment" after transferring</li>
                                        <li>• Keep your transaction reference for verification</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Payment Details -->
                    <div id="card_paymentDetails" class="payment-details hidden">
                        <h3 class="text-lg font-bold text-dark mb-4 flex items-center">
                            <i class="fas fa-credit-card text-accent mr-2"></i>
                            Card Payment
                        </h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                            <i class="fas fa-shield-alt text-blue-600 text-4xl mb-4"></i>
                            <p class="text-blue-800 font-semibold text-lg">Secure Card Payment</p>
                            <p class="text-blue-600 text-sm mt-2">You will be redirected to a secure payment gateway</p>
                            <div class="flex justify-center space-x-4 mt-4">
                                <div class="w-12 h-8 bg-gradient-to-r from-red-500 to-yellow-500 rounded flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">MC</span>
                                </div>
                                <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">VISA</span>
                                </div>
                                <div class="w-12 h-8 bg-green-600 rounded flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">VERVE</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Money Details -->
                    <div id="mobile_moneyDetails" class="payment-details hidden">
                        <h3 class="text-lg font-bold text-dark mb-4 flex items-center">
                            <i class="fas fa-mobile-alt text-accent mr-2"></i>
                            Mobile Money Payment
                        </h3>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 text-center">
                            <i class="fas fa-mobile-alt text-purple-600 text-4xl mb-4"></i>
                            <p class="text-purple-800 font-semibold text-lg">Mobile Money Transfer</p>
                            <p class="text-purple-600 text-sm mt-2">Choose your preferred mobile money provider</p>
                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <div class="bg-green-100 p-3 rounded-lg">
                                    <span class="text-green-800 font-semibold">Opay</span>
                                </div>
                                <div class="bg-blue-100 p-3 rounded-lg">
                                    <span class="text-blue-800 font-semibold">PalmPay</span>
                                </div>
                                <div class="bg-purple-100 p-3 rounded-lg">
                                    <span class="text-purple-800 font-semibold">Kuda</span>
                                </div>
                                <div class="bg-orange-100 p-3 rounded-lg">
                                    <span class="text-orange-800 font-semibold">Others</span>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <?php
                                    // Generate product image URL with fallback
                                    $productImage = !empty($item['image']) && $item['image'] !== DEFAULT_PRODUCT_IMAGE
                                        ? PRODUCT_IMAGE_URL . htmlspecialchars($item['image'])
                                        : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE;
                                    ?>
                                    <img src="<?php echo $productImage; ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 rounded-lg object-cover">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-dark"><?= htmlspecialchars($item['name']) ?></h4>
                                        <p class="text-sm text-gray-600">Qty: <?= $item['quantity'] ?></p>
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-dark"><?php echo CURRENCY_SYMBOL; ?><?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="border-t border-slate-200 pt-4 space-y-2">
                        <div class="flex justify-between mb-3">
                            <span class="text-gray-600">Subtotal:</span>
                            <span id="subtotal-value"><?php echo CURRENCY_SYMBOL; ?><?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="flex justify-between mb-3">
                            <span class="text-gray-600">Delivery:</span>
                            <span id="delivery-value"><?php echo CURRENCY_SYMBOL; ?><?= number_format($delivery_fee, 2) ?></span>
                        </div>
                        <div class="flex justify-between mb-3">
                            <span class="text-gray-600">Tax:</span>
                            <span id="tax-value"><?php echo CURRENCY_SYMBOL; ?><?= number_format($tax, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-dark pt-2 border-t border-slate-200">
                            <span>Total:</span>
                            <span id="total-value"><?php echo CURRENCY_SYMBOL; ?><?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                    <!-- Payment Status -->
                    <div id="paymentStatus" class="mb-6 p-4 rounded-lg border-2 border-yellow-200 bg-yellow-50" style="display: none;">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-yellow-800 font-semibold">Waiting for Payment</span>
                        </div>
                        <p class="text-yellow-700 text-sm mt-1">Complete your payment using the selected method</p>
                    </div>

                    <!-- Payment Verification Status -->
                    <div id="verificationStatus" class="mb-6 p-4 rounded-lg border-2 border-blue-200 bg-blue-50" style="display: none;">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="text-blue-800 font-semibold">Verifying Payment...</span>
                        </div>
                        <p class="text-blue-700 text-sm mt-1">Admin is verifying your payment. This may take a few minutes.</p>
                    </div>

                    <div class="mt-4">
                        <button onclick="confirmPayment()"
                            id="paymentButton"
                            class="w-full bg-custom-accent text-white py-4 rounded-2xl font-semibold text-lg hover:opacity-90 transition-opacity shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            I Have Made Payment
                        </button>

                        <div class="text-center mt-3">
                            <button onclick="checkPaymentStatus()" class="text-accent hover:underline text-sm font-medium">
                                Check Payment Status
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Form Modal -->
    <div id="deliveryFormModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-custom-dark">Delivery Information</h2>
                <button onclick="closeDeliveryForm()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="deliveryForm" class="space-y-6 mb-8">
                <!-- Personal Information -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="text-lg font-semibold text-custom-dark mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" id="fullName" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-custom-accent focus:outline-none" placeholder="e.g John Doe" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" id="phoneNumber" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-custom-accent focus:outline-none" placeholder="08012345678" required>
                        </div>
                    </div>
                </div>

                <!-- Delivery Address -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="text-lg font-semibold text-custom-dark mb-4">Delivery Address</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                            <select id="deliveryState" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-custom-accent" required onchange="updateCities()">
                                <option value="">Select State</option>
                                <option value="fct">Federal Capital Territory (Abuja)</option>
                                <option value="lagos">Lagos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                            <select id="deliveryCity" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-custom-accent" required onchange="updateAreas()">
                                <option value="">Select City</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Area/District *</label>
                            <select id="deliveryArea" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-custom-accent" required>
                                <option value="">Select Area</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Street Address *</label>
                            <textarea id="streetAddress" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-custom-accent" placeholder="e.g., 123 Main Street, Victoria Island" required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code (Optional)</label>
                            <input type="text" id="postalCode" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-custom-accent" placeholder="e.g., 100001">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Landmark (Optional)</label>
                            <input type="text" id="landmark" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-custom-accent" placeholder="e.g., Near First Bank">
                        </div>
                    </div>
                </div>

                <!-- Delivery Options -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="text-lg font-semibold text-custom-dark mb-4">Delivery Options</h3>
                    <div class="space-y-3">
                        <div>
                            <input type="radio" id="standard" name="delivery_option" value="standard" class="hidden" checked>
                            <label for="standard" class="delivery-option-label flex items-center p-3 border-2 border-custom-accent bg-blue-50 rounded-xl cursor-pointer transition-all">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-custom-dark">Standard Delivery</h4>
                                    <p class="text-sm text-gray-600">3-5 business days • Free for orders above <?php echo CURRENCY_SYMBOL; ?>10,000</p>
                                </div>
                                <div class="text-custom-accent font-semibold"><?php echo CURRENCY_SYMBOL; ?><?= number_format($delivery_fee) ?></div>
                            </label>
                        </div>
                        <div>
                            <input type="radio" id="express" name="delivery_option" value="express" class="hidden">
                            <label for="express" class="delivery-option-label flex items-center p-3 border-2 border-gray-50 rounded-xl cursor-pointer hover:border-custom-accent transition-all">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-custom-dark">Express Delivery</h4>
                                    <p class="text-sm text-gray-600">1-2 business days • Fast delivery</p>
                                </div>
                                <div class="text-custom-accent font-semibold"><?php echo CURRENCY_SYMBOL; ?>4,500</div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Special Instructions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Delivery Instructions (Optional)</label>
                    <textarea id="specialInstructions" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-custom-accent focus:outline-none" placeholder="Any special instructions for delivery..."></textarea>
                </div>

                <div class="flex space-x-4 pt-4 mb-6">
                    <button type="button" onclick="closeDeliveryForm()" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-custom-accent text-white rounded-xl hover:opacity-90 transition-opacity">
                        Complete Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bottom navigation include -->
    <?php include 'partials/bottom-nav.php'; ?>

    <script src="../assets/js/toast.js"></script>
    <script src="js/script.js"></script>
    <script>
        let currentOrderId = null;
        let currentVerificationId = null;
        let verificationInterval = null;

        // Nigerian states and their cities/areas (delivery locations only)
        const nigerianLocations = {
            'fct': {
                name: 'Federal Capital Territory (Abuja)',
                cities: {
                    'abuja': {
                        name: 'Abuja',
                        areas: ['Wuse 2', 'Wuse 1', 'Garki 1', 'Garki 2', 'Maitama', 'Asokoro', 'Central Business District', 'Utako', 'Jabi', 'Life Camp']
                    },
                    'gwagwalada': {
                        name: 'Gwagwalada',
                        areas: ['Gwagwalada Town', 'Zuba', 'Dobi', 'Paiko']
                    },
                    'kuje': {
                        name: 'Kuje',
                        areas: ['Kuje Town', 'Rubochi', 'Gudaba']
                    },
                    'kwali': {
                        name: 'Kwali',
                        areas: ['Kwali Town', 'Kilankwa', 'Yangoji']
                    },
                    'abaji': {
                        name: 'Abaji',
                        areas: ['Abaji Town', 'Toto', 'Pandogari']
                    },
                    'bwari': {
                        name: 'Bwari',
                        areas: ['Bwari Town', 'Kubwa', 'Dutse', 'Sabon Wuse', 'Gwarinpa']
                    }
                }
            },
            'lagos': {
                name: 'Lagos',
                cities: {
                    'ikeja': {
                        name: 'Ikeja',
                        areas: ['GRA', 'Allen Avenue', 'Computer Village', 'Alausa', 'Ogba', 'Agege']
                    },
                    'victoria_island': {
                        name: 'Victoria Island',
                        areas: ['VI', 'Ikoyi', 'Lekki', 'Ajah', 'Epe']
                    },
                    'mainland': {
                        name: 'Lagos Mainland',
                        areas: ['Yaba', 'Surulere', 'Mushin', 'Isolo', 'Oshodi']
                    }
                }
            }
        };

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            initializePaymentMethods();
            initializeFormHandlers();
        });

        // Initialize payment methods
        function initializePaymentMethods() {
            document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    // Hide all payment details
                    document.querySelectorAll('.payment-details').forEach(detail => {
                        detail.classList.add('hidden');
                    });

                    // Show selected payment details
                    const selectedMethod = this.value;
                    document.getElementById(selectedMethod + 'Details').classList.remove('hidden');

                    // Reset all labels to inactive state
                    document.querySelectorAll('.payment-label').forEach(label => {
                        label.classList.remove('border-accent', 'bg-orange-50', 'shadow-lg', 'shadow-orange-200/50');
                        label.classList.add('border-transparent');
                    });

                    // Reset all radio dots
                    document.querySelectorAll('.radio-dot').forEach(dot => {
                        dot.style.opacity = '0';
                    });

                    // Reset all radio button borders
                    document.querySelectorAll('.payment-label .rounded-full').forEach(circle => {
                        circle.classList.remove('border-accent');
                        circle.classList.add('border-gray-300');
                    });

                    // Apply active styles to selected option
                    const selectedLabel = this.parentElement.querySelector('.payment-label');
                    const selectedDot = this.parentElement.querySelector('.radio-dot');
                    const selectedCircle = this.parentElement.querySelector('.rounded-full');

                    selectedLabel.classList.remove('border-transparent');
                    selectedLabel.classList.add('border-accent', 'bg-orange-50', 'shadow-lg', 'shadow-orange-200/50');
                    selectedDot.style.opacity = '1';
                    selectedCircle.classList.remove('border-gray-300');
                    selectedCircle.classList.add('border-accent');
                });
            });
        }

        // Initialize form handlers
        function initializeFormHandlers() {
            // Delivery form submission
            document.getElementById('deliveryForm').addEventListener('submit', handleDeliveryFormSubmit);

            // Delivery option selection
            document.querySelectorAll('input[name="delivery_option"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.delivery-option-label').forEach(label => {
                        label.classList.remove('border-custom-accent', 'bg-blue-50');
                        label.classList.add('border-gray-50');
                    });
                    this.parentElement.classList.add('border-custom-accent', 'bg-blue-50');
                    this.parentElement.classList.remove('border-gray-50');
                });
            });
        }

        // Update cities based on selected state
        function updateCities() {
            const stateSelect = document.getElementById('deliveryState');
            const citySelect = document.getElementById('deliveryCity');
            const areaSelect = document.getElementById('deliveryArea');

            const selectedState = stateSelect.value;

            // Clear city and area options
            citySelect.innerHTML = '<option value="">Select City</option>';
            areaSelect.innerHTML = '<option value="">Select Area</option>';

            if (selectedState && nigerianLocations[selectedState]) {
                const cities = nigerianLocations[selectedState].cities;

                Object.keys(cities).forEach(cityKey => {
                    const option = document.createElement('option');
                    option.value = cityKey;
                    option.textContent = cities[cityKey].name;
                    citySelect.appendChild(option);
                });
            }
        }

        // Update areas based on selected city
        function updateAreas() {
            const stateSelect = document.getElementById('deliveryState');
            const citySelect = document.getElementById('deliveryCity');
            const areaSelect = document.getElementById('deliveryArea');

            const selectedState = stateSelect.value;
            const selectedCity = citySelect.value;

            // Clear area options
            areaSelect.innerHTML = '<option value="">Select Area</option>';

            if (selectedState && selectedCity && nigerianLocations[selectedState] && nigerianLocations[selectedState].cities[selectedCity]) {
                const areas = nigerianLocations[selectedState].cities[selectedCity].areas;

                areas.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.toLowerCase().replace(/\s+/g, '_');
                    option.textContent = area;
                    areaSelect.appendChild(option);
                });
            }
        }

        // Copy account number function
        function copyAccountNumber() {
            const accountNumber = document.getElementById('accountNumber').textContent.trim();

            navigator.clipboard.writeText(accountNumber).then(() => {
                const copyIcon = document.getElementById('copyIcon');
                const copyText = document.getElementById('copyText');

                copyIcon.className = 'fas fa-check mr-2';
                copyText.textContent = 'Copied!';

                showToasted('Account number copied to clipboard!', 'success');

                setTimeout(() => {
                    copyIcon.className = 'fas fa-copy mr-2';
                    copyText.textContent = 'Copy';
                }, 2000);
            }).catch(() => {
                showToasted('Failed to copy account number', 'error');
            });
        }

        // Main payment confirmation function
        async function confirmPayment() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!selectedMethod) {
                showToasted('Please select a payment method', 'error');
                return;
            }

            // Show delivery form first
            showDeliveryForm();
        }

        // Show delivery form
        function showDeliveryForm() {
            document.getElementById('deliveryFormModal').classList.remove('hidden');
        }

        // Close delivery form
        function closeDeliveryForm() {
            document.getElementById('deliveryFormModal').classList.add('hidden');
        }

        // Handle delivery form submission
        async function handleDeliveryFormSubmit(e) {
            e.preventDefault();

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';
            submitBtn.disabled = true;

            try {
                // Get shipping data
                const shippingData = {
                    name: document.getElementById('fullName').value,
                    phone: document.getElementById('phoneNumber').value,
                    state: document.getElementById('deliveryState').value,
                    city: document.getElementById('deliveryCity').value,
                    area: document.getElementById('deliveryArea').value,
                    address: document.getElementById('streetAddress').value,
                    postal_code: document.getElementById('postalCode').value,
                    landmark: document.getElementById('landmark').value,
                    delivery_option: document.querySelector('input[name="delivery_option"]:checked').value,
                    special_instructions: document.getElementById('specialInstructions').value
                };

                // Validate required fields
                const requiredFields = ['name', 'phone', 'state', 'city', 'area', 'address'];
                for (let field of requiredFields) {
                    if (!shippingData[field]) {
                        throw new Error(`Please fill in the ${field.replace('_', ' ')} field`);
                    }
                }

                // Get selected payment method
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

                // Create order
                const response = await fetch('api/create-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_method: selectedMethod,
                        amount: <?php echo $total; ?>,
                        virtual_account: '<?php echo $accountNumber; ?>',
                        cart_items: <?php echo json_encode($cart_items); ?>,
                        shipping_address: shippingData
                    })
                });

                const result = await response.json();

                if (result.success) {
                    currentOrderId = result.order_id;
                    currentVerificationId = result.verification_id;

                    // Close delivery form
                    closeDeliveryForm();

                    // Show payment verification
                    showPaymentVerification();

                    // Simulate payment verification after delay
                    setTimeout(() => {
                        verifyPayment();
                    }, 3000);

                } else {
                    throw new Error(result.message || 'Order creation failed');
                }

            } catch (error) {
                console.error('Order error:', error);
                showToasted(error.message || 'Failed to process order. Please try again.', 'error');
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        }

        // Show payment verification status
        function showPaymentVerification() {
            const paymentButton = document.getElementById('paymentButton');
            const verificationStatus = document.getElementById('verificationStatus');

            paymentButton.innerHTML = 'Payment Verification in Progress...';
            paymentButton.disabled = true;
            verificationStatus.style.display = 'block';

            showToasted('Payment submitted for verification', 'success');
        }

        // Verify payment
        async function verifyPayment() {
            try {
                const response = await fetch('api/verify-payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        verification_id: currentVerificationId
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showOrderSuccess(result.order_number);
                } else {
                    showPaymentFailed();
                }

            } catch (error) {
                console.error('Payment verification error:', error);
                showPaymentFailed();
            }
        }

        // Show order success
        function showOrderSuccess(orderNumber) {
            const checkoutContainer = document.getElementById('checkoutContainer');

            checkoutContainer.innerHTML = `
                <div class="lg:col-span-3 flex items-center justify-center min-h-[60vh]">
                    <div class="text-center max-w-md mx-auto">
                        <!-- Success Vector SVG -->
                        <div class="w-32 h-32 mx-auto mb-6">
                            <svg viewBox="0 0 200 200" class="w-full h-full">
                                <circle cx="100" cy="100" r="90" fill="#f0fdf4" stroke="#22c55e" stroke-width="2"/>
                                <path d="M60 100 L85 125 L140 70" stroke="#22c55e" stroke-width="6" 
                                      fill="none" stroke-linecap="round" stroke-linejoin="round"
                                      class="animate-pulse"/>
                                <circle cx="50" cy="50" r="3" fill="#fbbf24" class="animate-bounce"/>
                                <circle cx="150" cy="60" r="2" fill="#f59e0b" class="animate-bounce" style="animation-delay: 0.2s"/>
                                <circle cx="40" cy="140" r="2" fill="#fbbf24" class="animate-bounce" style="animation-delay: 0.4s"/>
                                <circle cx="160" cy="130" r="3" fill="#f59e0b" class="animate-bounce" style="animation-delay: 0.6s"/>
                            </svg>
                        </div>

                        <h2 class="text-3xl font-bold text-green-600 mb-4">Order Placed Successfully!</h2>
                        <p class="text-gray-600 mb-6">
                            Your order <strong>${orderNumber}</strong> has been confirmed and will be delivered to your specified address.
                        </p>

                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Order Number:</span>
                                <span class="font-bold text-green-800">${orderNumber}</span>
                            </div>
                           <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total Amount:</span>
                                <span class="font-bold text-green-800"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($total); ?></span>
                            </div>
                        </div>

                        <button onclick="redirectToOrders()" 
                                class="w-full bg-custom-accent text-white py-4 rounded-xl font-semibold text-lg 
                                       hover:opacity-90 transition-all duration-300 transform hover:scale-105 
                                       shadow-lg hover:shadow-xl mb-4">
                            <i class="fas fa-truck mr-2"></i>
                            Track Your Order
                        </button>

                        <button onclick="continueShopping()" 
                                class="w-full border border-gray-300 text-gray-700 py-3 rounded-xl 
                                       hover:bg-gray-50 transition-colors">
                            Continue Shopping
                        </button>
                    </div>
                </div>
            `;

            // Update cart count to 0
            const cartBadge = document.getElementById('cartCount');
            if (cartBadge) {
                cartBadge.textContent = '0';
                cartBadge.parentElement.style.display = 'none';
            }

            showToasted('Order completed successfully! 🎉', 'success');
        }

        // Show payment failed
        function showPaymentFailed() {
            const verificationStatus = document.getElementById('verificationStatus');
            const paymentButton = document.getElementById('paymentButton');

            verificationStatus.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-red-800 font-semibold">Payment Verification Failed</span>
                </div>
                <p class="text-red-700 text-sm mt-1">Your payment could not be verified. Please try again or contact support.</p>
            `;
            verificationStatus.className = 'mb-6 p-4 rounded-lg border-2 border-red-200 bg-red-50';

            paymentButton.disabled = false;
            paymentButton.innerHTML = 'I Have Made Payment';

            showToasted('Payment verification failed. Please try again.', 'error');
        }

        // Check payment status
        function checkPaymentStatus() {
            if (currentVerificationId) {
                showToasted('Checking payment status...', 'info');

                fetch(`api/check-payment-status.php?verification_id=${currentVerificationId}`)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            if (result.status === 'verified') {
                                showOrderSuccess(result.order_number);
                            } else if (result.status === 'rejected') {
                                showPaymentFailed();
                            } else {
                                showToasted('Payment verification still in progress', 'info');
                            }
                        } else {
                            showToasted('Could not check payment status', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Status check error:', error);
                        showToasted('Error checking payment status', 'error');
                    });
            } else {
                showToasted('No payment verification in progress', 'error');
            }
        }

        // Navigation functions
        function redirectToOrders() {
            window.location.href = 'orders.php';
        }

        function continueShopping() {
            window.location.href = 'dashboard.php';
        }
    </script>
</body>

</html>