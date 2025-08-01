<?php
// Get current page filename
$currentPage = basename($_SERVER['PHP_SELF']);

// Get cart count for logged in users
$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items FROM cart_items WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cartCount = (int)($result['total_items'] ?? 0);
    } catch (Exception $e) {
        error_log("Error getting cart count in top-nav: " . $e->getMessage());
        $cartCount = 0;
    }
}

// Set header and subheader based on page
switch ($currentPage) {
    case 'cart.php':
        $header = 'Shopping Cart';
        $subheader = 'Review your items';
        break;
    case 'checkout.php':
        $header = 'Checkout';
        $subheader = 'Complete your order';
        break;
    case 'dashboard.php':
        $header = 'Dashboard';
        $subheader = 'Welcome back!';
        break;
    case 'product.php':
        $header = 'Product Details';
        $subheader = 'See product information';
        break;
    case 'orders.php':
        $header = 'Your Orders';
        $subheader = 'View your past orders';
        break;
    default:
        $header = 'Frozen Foods';
        $subheader = '';
        break;
}
?>

<!-- Header -->
<div class="flex justify-between items-center mb-8 animate-slide-down">
    <button id="backBtn" class="p-3 hover:bg-white/80 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-soft backdrop-blur-sm">
        <svg class="w-6 h-6 text-custom-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>

    <div class="text-center">
        <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-custom-dark"><?php echo $header; ?></h2>
        <?php if ($subheader): ?>
            <p class="text-gray-600 text-sm"><?php echo $subheader; ?></p>
        <?php endif; ?>
    </div>

    <a href="cart.php" class="transform hover:scale-105 transition-all duration-300">
        <div class="relative">
            <div id="cart-icon" class="w-12 h-12 frosted-glass rounded-xl flex items-center justify-center shadow-soft hover:shadow-medium transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-custom-dark">
                    <circle cx="8" cy="21" r="1" />
                    <circle cx="19" cy="21" r="1" />
                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                </svg>
            </div>
            <!-- Cart badge - hide if count is 0 -->
            <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full flex items-center justify-center shadow-medium animate-bounce-gentle"
                style="<?php echo $cartCount > 0 ? 'display: flex;' : 'display: none;'; ?>">
                <span id="cartCount" class="cart-badge text-white text-xs font-bold"><?php echo $cartCount; ?></span>
            </div>
        </div>
    </a>
</div>