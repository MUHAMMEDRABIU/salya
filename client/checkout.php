<?php
require_once 'util/util.php';
require_once 'initialize.php';
require_once '../helpers/monnify.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartCount = array_sum(array_column($cart_items, 'quantity'));

if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$delivery_fee = $subtotal >= 10000 ? 0 : 500;
$tax = 0;
$total = $subtotal + $delivery_fee + $tax;

$account = getUserVirtualAccount($pdo, $_SESSION['user_id']);

$accountNumber = $account['account_number'] ?? 'Unavailable';
$accountName = $account['full_customer_name'] ?? $account['account_name'] ?? 'Unavailable';
$bankName = $account['bank_name'] ?? 'Unavailable';
$customerName = $user['first_name'] . ' ' . $user['last_name'];
$customerEmail = $user['email'];

// format account number
$accountNumber = formatAccountNumber($accountNumber);

require_once 'partials/headers.php';
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
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
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
                            <label for="bank_transfer" class="payment-label flex items-center p-4 border-2 border-transparent rounded-xl hover:border-accent cursor-pointer transition-all duration-300 hover:shadow-lg">
                                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mr-4">
                                    <i class="fas fa-university text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-dark">Bank Transfer</h3>
                                    <p class="text-sm text-gray-600">Transfer to our virtual account number</p>
                                </div>
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center transition-all duration-300">
                                    <div class="w-2 h-2 bg-accent rounded-full opacity-0 radio-dot transition-opacity duration-300"></div>
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
                                    <span class="text-2xl font-bold text-dark tracking-wider" id="accountNumber">
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
                                        <li>• Transfer the exact amount: <strong>₦<?php echo number_format($total, 2); ?></strong></li>
                                        <li>• This account number is unique to your order</li>
                                        <li>• Payment will be confirmed automatically</li>
                                        <li>• Account expires in 24 hours</li>
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
                                    <img src="../assets/uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 rounded-lg object-cover">
                                    <div class="flex-1">
                                        <h4 class="text-gray-400"><?= htmlspecialchars($item['name']) ?></h4>
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-dark">₦<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                        </div>
                                    </div>
                                    <button class="remove-btn text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors" data-id="<?= $item['product_id'] ?>">
                                        <i data-lucide="trash"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="border-t border-slate-200 pt-4 space-y-2">
                        <div class="flex justify-between mb-3">
                            <span class="text-gray-300">Subtotal:</span>
                            <span id="subtotal-value">₦<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="flex justify-between mb-3">
                            <span class="text-gray-300">Delivery:</span>
                            <span id="delivery-value">₦<?= number_format($delivery_fee, 2) ?></span>
                        </div>
                        <div class="flex justify-between mb-3">
                            <span class="text-gray-300">Tax:</span>
                            <span id="tax-value">₦<?= number_format($tax, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-dark pt-2 border-t border-slate-200">
                            <span class="text-gray-300">Total:</span>
                            <span id="total-value">₦<?= number_format($total, 2) ?></span>
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

    <script src="../assets/js/toast.js"></script>
    <script src="js/script.js"></script>
    <script>
        let currentVerificationId = null;
        let verificationInterval = null;

        // Nigerian states and their cities/areas
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
                        areas: ['GRA', 'Allen Avenue', 'Computer Village', 'Alausa']
                    },
                    'victoria_island': {
                        name: 'Victoria Island',
                        areas: ['VI', 'Ikoyi', 'Lekki']
                    }
                }
            }
        };

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


        // Initialize first option on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('deliveryCity').addEventListener('change', updateAreas);
            lucide.createIcons();

            // Remove any existing default styling
            document.querySelectorAll('.payment-label').forEach(label => {
                label.classList.remove('border-accent', 'bg-orange-50', 'shadow-lg', 'shadow-orange-200/50');
                label.classList.add('border-transparent');
            });

            document.querySelectorAll('.radio-dot').forEach(dot => {
                dot.style.opacity = '0';
            });

            document.querySelectorAll('.payment-label .rounded-full').forEach(circle => {
                circle.classList.remove('border-accent');
                circle.classList.add('border-gray-300');
            });

            // Apply active styles only to the checked option
            const firstOption = document.querySelector('input[name="payment_method"]:checked');
            if (firstOption) {
                const firstLabel = firstOption.parentElement.querySelector('.payment-label');
                const firstDot = firstOption.parentElement.querySelector('.radio-dot');
                const firstCircle = firstOption.parentElement.querySelector('.rounded-full');

                // Apply initial active styles
                firstLabel.classList.remove('border-transparent');
                firstLabel.classList.add('border-accent', 'bg-orange-50', 'shadow-lg', 'shadow-orange-200/50');
                firstDot.style.opacity = '1';
                firstCircle.classList.remove('border-gray-300');
                firstCircle.classList.add('border-accent');
            }
        });



        function showToast(message, type = 'success') {
            showToasted(message, type);
        }

        window.productId = <?= json_encode(array_column($cart_items, 'product_id')) ?>;
        window.cartItems = <?= json_encode($cart_items) ?>;
        window.cartTotals = {
            subtotal: <?= $subtotal ?>,
            delivery_fee: <?= $delivery_fee ?>,
            total: <?= $total ?>
        };
        window.checkoutData = {
            customerName: '<?= htmlspecialchars($customerName) ?>',
            customerEmail: '<?= htmlspecialchars($customerEmail) ?>',
            accountNumber: '<?= htmlspecialchars($accountNumber) ?>',
            bankName: '<?= htmlspecialchars($bankName) ?>',
            accountName: '<?= htmlspecialchars($accountName) ?>',
            total: <?= $total ?>,
            cartItems: <?= json_encode($cart_items) ?>
        };

        // Payment method selection logic (Fixed)
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
                    // Remove active styles
                    label.classList.remove('border-accent', 'bg-orange-50', 'shadow-lg', 'shadow-orange-200/50');
                    // Set to transparent border (inactive state)
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

                // Active state styles
                selectedLabel.classList.remove('border-transparent');
                selectedLabel.classList.add('border-accent', 'bg-orange-50', 'shadow-lg', 'shadow-orange-200/50');

                // Show radio dot
                selectedDot.style.opacity = '1';

                // Update radio button border
                selectedCircle.classList.remove('border-gray-300');
                selectedCircle.classList.add('border-accent');
            });
        });

        // Initialize first option
        document.querySelector('input[name="payment_method"]:checked').parentElement.querySelector('.radio-dot').style.opacity = '1';
        document.querySelector('input[name="payment_method"]:checked').parentElement.classList.add('border-accent', 'bg-orange-50');

        function copyAccountNumber() {
            const accountNumber = document.getElementById('accountNumber').textContent.trim();

            navigator.clipboard.writeText(accountNumber).then(() => {
                const copyIcon = document.getElementById('copyIcon');
                const copyText = document.getElementById('copyText');

                // Change to checkmark
                copyIcon.className = 'fas fa-check mr-2';
                copyText.textContent = 'Copied!';

                showToasted('Account number copied to clipboard!', 'success');

                // Reset after 2 seconds
                setTimeout(() => {
                    copyIcon.className = 'fas fa-copy mr-2';
                    copyText.textContent = 'Copy';
                }, 2000);
            }).catch(() => {
                showToasted('Failed to copy account number', 'error');
            });
        }

        async function confirmPayment() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const paymentButton = document.getElementById('paymentButton');
            const paymentStatus = document.getElementById('paymentStatus');
            const verificationStatus = document.getElementById('verificationStatus');

            // Disable button and show loading
            paymentButton.disabled = true;
            paymentButton.innerHTML = 'Processing...';

            try {
                // Submit payment for verification
                const response = await fetch('api/verify-payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_method: selectedMethod,
                        amount: <?php echo $total; ?>,
                        virtual_account: '<?php echo $accountNumber; ?>'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    currentVerificationId = result.verification_id;

                    // Hide payment status, show verification status
                    paymentStatus.style.display = 'none';
                    verificationStatus.style.display = 'block';

                    // Update button
                    paymentButton.innerHTML = 'Payment Submitted for Verification';
                    paymentButton.disabled = true;

                    showToast('Payment submitted for admin verification', 'success');

                    // Start checking verification status
                    startVerificationCheck();

                } else {
                    throw new Error(result.message || 'Payment submission failed');
                }

            } catch (error) {
                console.error('Payment error:', error);
                showToast('Payment submission failed. Please try again.', 'error');

                // Re-enable button
                paymentButton.disabled = false;
                paymentButton.innerHTML = 'I Have Made Payment';
            }
        }

        function startVerificationCheck() {
            if (verificationInterval) {
                clearInterval(verificationInterval);
            }

            verificationInterval = setInterval(async () => {
                try {
                    const response = await fetch(`api/check-payment-status.php?verification_id=${currentVerificationId}`);
                    const result = await response.json();

                    if (result.success) {
                        if (result.status === 'verified') {
                            clearInterval(verificationInterval);
                            onPaymentVerified();
                        } else if (result.status === 'rejected') {
                            clearInterval(verificationInterval);
                            onPaymentRejected();
                        }
                        // If still pending, continue checking
                    }
                } catch (error) {
                    console.error('Verification check error:', error);
                }
            }, 5000); // Check every 5 seconds
        }

        function onPaymentVerified() {
            const verificationStatus = document.getElementById('verificationStatus');

            // Update verification status to success
            verificationStatus.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 font-semibold">Payment Verified!</span>
                </div>
                <p class="text-green-700 text-sm mt-1">Your payment has been confirmed by admin. Please provide delivery details.</p>
            `;
            verificationStatus.className = 'mb-6 p-4 rounded-lg border-2 border-green-200 bg-green-50';

            showToast('Payment verified! Please provide delivery details.', 'success');

            // Show delivery form
            setTimeout(() => {
                document.getElementById('deliveryLocationForm').style.display = 'block';
                document.getElementById('deliveryLocationForm').scrollIntoView({
                    behavior: 'smooth'
                });
            }, 2000);
        }

        function onPaymentRejected() {
            const verificationStatus = document.getElementById('verificationStatus');
            const paymentButton = document.getElementById('paymentButton');

            // Update verification status to rejected
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

            // Re-enable payment button
            paymentButton.disabled = false;
            paymentButton.innerHTML = 'I Have Made Payment';

            showToast('Payment verification failed. Please try again.', 'error');
        }

        function checkPaymentStatus() {
            if (currentVerificationId) {
                showToast('Checking payment status...', 'success');

                fetch(`api/check-payment-status.php?verification_id=${currentVerificationId}`)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            if (result.status === 'verified') {
                                onPaymentVerified();
                            } else if (result.status === 'rejected') {
                                onPaymentRejected();
                            } else {
                                showToast('Payment verification still in progress', 'info');
                            }
                        } else {
                            showToast('Could not check payment status', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Status check error:', error);
                        showToast('Error checking payment status', 'error');
                    });
            } else {
                showToast('No payment verification in progress', 'error');
            }
        }

        // Enhanced order tracking with real-time updates
        function trackOrder(orderId) {
            fetch(`api/get-delivery-status.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        updateTrackingDisplay(result.data);
                    } else {
                        showToast('Could not fetch tracking information', 'error');
                    }
                })
                .catch(error => {
                    console.error('Tracking error:', error);
                    showToast('Error fetching tracking information', 'error');
                });
        }

        function updateTrackingDisplay(trackingData) {
            const trackingInfo = document.getElementById('trackingInfo');

            trackingInfo.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-custom-dark">Order Status</h4>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            ${trackingData.status.replace('_', ' ').toUpperCase()}
                        </span>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-custom-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">Current Location</span>
                        </div>
                        <p class="text-gray-700">${trackingData.location}</p>
                        <p class="text-sm text-gray-500 mt-1">Updated: ${new Date(trackingData.updated_at).toLocaleString()}</p>
                    </div>
                    
                    ${trackingData.delivery_person ? `
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h5 class="font-medium text-custom-dark mb-2">Delivery Person</h5>
                        <div class="text-sm space-y-1">
                            <p><strong>Name:</strong> ${trackingData.delivery_person.name}</p>
                            <p><strong>Phone:</strong> ${trackingData.delivery_person.phone}</p>
                            <p><strong>Vehicle:</strong> ${trackingData.delivery_person.vehicle}</p>
                        </div>
                    </div>
                    ` : ''}
                    
                    <div class="text-sm text-gray-600">
                        <p><strong>Estimated Delivery:</strong> ${new Date(trackingData.estimated_delivery).toLocaleString()}</p>
                    </div>
                </div>
            `;
        }

        // Existing cart management code
        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const parent = btn.closest('[data-id]');
                const productId = parent.getAttribute('data-id');
                const qtySpan = parent.querySelector('.qty-count');

                try {
                    const res = await fetch('api/update-quantity.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                        })
                    });

                    const data = await res.json();

                    if (data.success) {
                        qtySpan.textContent = data.quantity;
                        document.getElementById('subtotal-value').textContent = `₦${data.subtotal.toFixed(2)}`;
                        document.getElementById('delivery-value').textContent = `₦${data.delivery_fee.toFixed(2)}`;
                        document.getElementById('tax-value').textContent = `₦${data.tax.toFixed(2)}`;
                        document.getElementById('total-value').textContent = `₦${data.total.toFixed(2)}`;

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
                        const itemContainer = btn.closest('.flex.items-center.space-x-4');
                        itemContainer?.remove();

                        document.getElementById('subtotal-value').textContent = `₦${data.subtotal.toFixed(2)}`;
                        document.getElementById('delivery-value').textContent = `₦${data.delivery_fee.toFixed(2)}`;
                        document.getElementById('total-value').textContent = `₦${data.total.toFixed(2)}`;

                        const cartCountEl = document.getElementById('cartCount');
                        if (cartCountEl) {
                            cartCountEl.textContent = `${data.cartCount}`;
                        }

                        if (data.cartCount == 0) {
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
    </script>

    <!-- Delivery Location Form (Hidden by default) -->
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

            <form id="deliveryForm" class="space-y-6">
                <!-- Personal Information -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="text-lg font-semibold text-custom-dark mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="fullName" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-custom-accent focus:outline-none" placeholder="e.g John Doe" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <div class="relative">
                                <input type="tel" id="phoneNumber" class="w-full px-4 py-3 pl-16 rounded-xl border border-gray-300 focus:border-custom-accent focus:outline-none" placeholder="08012345678" required>
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Flag_of_Nigeria.svg/32px-Flag_of_Nigeria.svg.png" alt="Nigeria Flag" class="w-6 h-4 rounded">
                                </div>
                            </div>
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
                                <option value="kano">Kano</option>
                                <option value="rivers">Rivers</option>
                                <option value="oyo">Oyo</option>
                                <option value="kaduna">Kaduna</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                            <select id="deliveryCity" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-custom-accent" required>
                                <option value="">Select City</option>
                            </select>
                            <span class="error-message text-red-500 text-sm mt-1 hidden">Please select a city</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Area/District *</label>
                            <select id="deliveryArea" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-custom-accent" required>
                                <option value="">Select Area</option>
                            </select>
                            <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter your area</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Street Address *</label>
                            <textarea id="streetAddress" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-custom-accent" placeholder="e.g., 123 Main Street, Victoria Island" required></textarea>
                            <span class="error-message text-red-500 text-sm mt-1 hidden">Please enter your street address</span>
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
                            <label for="standard" class="delivery-option-label flex items-center p-3 border-2 border-gray-50 rounded-xl cursor-pointer hover:border-custom-accent transition-all">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-custom-dark">Standard Delivery</h4>
                                    <p class="text-sm text-gray-600">3-5 business days • Free for orders above ₦10,000</p>
                                </div>
                                <div class="text-custom-accent font-semibold">₦2000</div>
                            </label>
                        </div>
                        <div>
                            <input type="radio" id="express" name="delivery_option" value="express" class="hidden">
                            <label for="express" class="delivery-option-label flex items-center p-3 border-2 border-gray-50 rounded-xl cursor-pointer hover:border-custom-accent transition-all">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-custom-dark">Express Delivery</h4>
                                    <p class="text-sm text-gray-600">1-2 business days • Fast delivery</p>
                                </div>
                                <div class="text-custom-accent font-semibold">₦4,500</div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Special Instructions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Delivery Instructions (Optional)</label>
                    <textarea id="specialInstructions" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-custom-accent focus:outline-none" placeholder="Any special instructions for delivery..."></textarea>
                </div>

                <div class="flex space-x-4 pt-4">
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

    <!-- Order Success Modal -->
    <div id="orderSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-8 w-full max-w-md text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-custom-dark mb-2">Order Placed Successfully!</h2>
            <p class="text-gray-600 mb-4">Your order has been confirmed and will be delivered to your specified address.</p>

            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-600 mb-2">Order ID</p>
                <p class="text-lg font-bold text-custom-dark" id="orderIdDisplay">#ORD-2024-001</p>
            </div>

            <div class="space-y-3">
                <button onclick="trackOrder()" class="w-full bg-custom-accent text-white py-3 rounded-xl hover:opacity-90 transition-opacity">
                    Track Your Order
                </button>
                <button onclick="continueShopping()" class="w-full border border-gray-300 text-gray-700 py-3 rounded-xl hover:bg-gray-50 transition-colors">
                    Continue Shopping
                </button>
            </div>
        </div>
    </div>

    <!-- Order Tracking Modal -->
    <div id="orderTrackingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-custom-dark">Track Your Order</h2>
                <button onclick="closeTrackingModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Order ID</p>
                        <p class="font-bold text-custom-dark" id="trackingOrderId">#ORD-2024-001</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Estimated Delivery</p>
                        <p class="font-bold text-custom-dark" id="estimatedDelivery">Dec 28, 2024</p>
                    </div>
                </div>
            </div>

            <!-- Tracking Progress -->
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-custom-dark">Order Confirmed</h4>
                        <p class="text-sm text-gray-600">Dec 25, 2024 at 2:30 PM</p>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="w-8 h-8 bg-custom-accent rounded-full flex items-center justify-center mr-4">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-custom-dark">Processing</h4>
                        <p class="text-sm text-gray-600">Your order is being prepared</p>
                    </div>
                </div>

                <div class="flex items-center opacity-50">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-500">Shipped</h4>
                        <p class="text-sm text-gray-400">Pending</p>
                    </div>
                </div>

                <div class="flex items-center opacity-50">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-500">Delivered</h4>
                        <p class="text-sm text-gray-400">Pending</p>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="mt-6 bg-blue-50 rounded-xl p-4">
                <h4 class="font-semibold text-custom-dark mb-2">Delivery Information</h4>
                <p class="text-sm text-gray-600" id="deliveryAddress">123 Main Street, Victoria Island, Lagos</p>
                <p class="text-sm text-gray-600 mt-1">Contact: <span id="deliveryPhone">+234 801 234 5678</span></p>
            </div>

            <div class="mt-6 flex space-x-4">
                <button onclick="contactSupport()" class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-xl hover:bg-gray-50 transition-colors">
                    Contact Support
                </button>
                <button onclick="closeTrackingModal()" class="flex-1 bg-custom-accent text-white py-3 rounded-xl hover:opacity-90 transition-opacity">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Generate random order ID
        function generateOrderId() {
            const timestamp = Date.now().toString().slice(-8);
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return `ORD-2024-${timestamp}${random}`;
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
        document.getElementById('deliveryForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';
            submitBtn.disabled = true;

            // Collect form data
            const deliveryData = {
                fullName: document.getElementById('fullName').value,
                phoneNumber: document.getElementById('phoneNumber').value,
                state: document.getElementById('state').value,
                city: document.getElementById('city').value,
                streetAddress: document.getElementById('streetAddress').value,
                postalCode: document.getElementById('postalCode').value,
                landmark: document.getElementById('landmark').value,
                deliveryOption: document.querySelector('input[name="delivery_option"]:checked').value,
                specialInstructions: document.getElementById('specialInstructions').value
            };

            // Validate required fields
            const requiredFields = ['fullName', 'phoneNumber', 'state', 'city', 'streetAddress'];
            let isValid = true;

            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-custom-accent');
                }
            });

            if (!isValid) {
                showToast('Please fill in all required fields', 'error');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                return;
            }

            try {
                // Simulate API call - replace with your actual endpoint
                const response = await fetch('api/complete-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        deliveryData: deliveryData,
                        cartItems: <?= json_encode($cart_items) ?>,
                        totalAmount: <?= $total ?>
                    })
                });

                // Simulate successful response for demo
                const result = {
                    success: true,
                    orderId: generateOrderId()
                };

                if (result.success) {
                    // Generate and display order ID
                    const orderId = result.orderId || generateOrderId();
                    document.getElementById('orderIdDisplay').textContent = `#${orderId}`;
                    document.getElementById('trackingOrderId').textContent = `#${orderId}`;

                    // Update delivery info in tracking
                    document.getElementById('deliveryAddress').textContent =
                        `${deliveryData.streetAddress}, ${deliveryData.city}, ${deliveryData.state}`;
                    document.getElementById('deliveryPhone').textContent = deliveryData.phoneNumber;

                    // Close delivery form and show success
                    closeDeliveryForm();
                    document.getElementById('orderSuccessModal').classList.remove('hidden');

                    showToast('Order completed successfully!', 'success');

                    // Clear cart after successful order
                    setTimeout(() => {
                        // You can add cart clearing logic here
                        console.log('Order completed, cart should be cleared');
                    }, 1000);
                } else {
                    throw new Error(result.message || 'Failed to complete order');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Failed to complete order. Please try again.', 'error');
            } finally {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });

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

        // Initialize first delivery option
        document.querySelector('input[name="delivery_option"]:checked').parentElement.classList.add('border-custom-accent', 'bg-blue-50');

        // Track order function
        function trackOrder() {
            document.getElementById('orderSuccessModal').classList.add('hidden');
            document.getElementById('orderTrackingModal').classList.remove('hidden');
        }

        // Close tracking modal
        function closeTrackingModal() {
            document.getElementById('orderTrackingModal').classList.add('hidden');
        }

        // Continue shopping
        function continueShopping() {
            window.location.href = 'dashboard.php';
        }

        // Contact support
        function contactSupport() {
            showToast('Redirecting to support...', 'success');
            // You can redirect to support page, open chat, or show contact info
            setTimeout(() => {
                // Replace with your actual support contact method
                window.open('mailto:support@shopease.com?subject=Order Support Request', '_blank');
            }, 1500);
        }

        // Set estimated delivery date (3-5 days from now)
        function setEstimatedDelivery() {
            const today = new Date();
            const deliveryDate = new Date(today.getTime() + (4 * 24 * 60 * 60 * 1000)); // 4 days from now
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            document.getElementById('estimatedDelivery').textContent = deliveryDate.toLocaleDateString('en-US', options);
        }

        // Initialize estimated delivery date
        setEstimatedDelivery();
    </script>
</body>

</html>