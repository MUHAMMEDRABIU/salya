<?php
require_once 'util/util.php';
require_once 'initialize.php';
require __DIR__ . '/../config/constants.php';

// Get cart count for logged in users
$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items FROM cart_items WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cartCount = (int)($result['total_items'] ?? 0);
    } catch (Exception $e) {
        error_log("Error getting cart count in dashboard: " . $e->getMessage());
        $cartCount = 0;
    }
}

// Get all products for the dashboard
$products = getAllProducts($pdo);
$categories = getProductCategories($pdo);
require_once 'partials/headers.php';
?>

<body class="bg-gray font-dm pb-24 overflow-x-hidden">
    <!-- Main Content -->
    <main class="px-4 pt-6 space-y-6 animate-fade-in">
        <!-- Welcome Section -->
        <div class="gradient-bg rounded-3xl p-6 text-white floating-card animate-slide-up">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Welcome <?= $user['first_name']; ?>!</h2>
                    <p class="text-orange-100 text-sm">Discover fresh frozen foods for your family</p>
                </div>

                <!-- Updated Cart Icon with Count -->
                <a href="cart.php" class="transform hover:scale-105 transition-all duration-300">
                    <div class="relative">
                        <div id="cart-icon" class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl border border-white/30 flex items-center justify-center hover:bg-white/30 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-custom-dark">
                                <circle cx="8" cy="21" r="1" />
                                <circle cx="19" cy="21" r="1" />
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                            </svg>
                        </div>
                        <!-- Cart Count Badge -->
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg animate-bounce-gentle"
                            style="<?php echo $cartCount > 0 ? 'display: flex;' : 'display: none;'; ?>">
                            <span id="cartCount" class="cart-badge text-white text-xs font-bold"><?php echo $cartCount; ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="flex items-center space-x-4 text-orange-100 text-sm">
                <div class="flex items-center">
                    <i class="fas fa-clock mr-2"></i>
                    <span>Fresh daily</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-truck mr-2"></i>
                    <span>Fast delivery</span>
                </div>
            </div>
        </div>

        <!-- Updated Search Bar with Register/Login styling -->
        <div class="search-container animate-slide-up" style="animation-delay: 0.1s;">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </div>
                <input
                    type="text"
                    id="search-input"
                    placeholder="Search for chicken, fish, turkey..."
                    class="w-full pl-12 pr-16 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-200 hover:bg-white">
                <button class="absolute right-3 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white hover:bg-orange-600 transition-all duration-300 hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" x2="21" y1="6" y2="6" />
                        <line x1="9" x2="21" y1="12" y2="12" />
                        <line x1="9" x2="21" y1="18" y2="18" />
                        <circle cx="5" cy="12" r="1" />
                        <circle cx="5" cy="6" r="1" />
                        <circle cx="5" cy="18" r="1" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Responsive Category Tabs (like notifications.php) -->
        <div class="animate-slide-up" style="animation-delay: 0.2s;">
            <div class="overflow-x-auto hide-scrollbar">
                <div class="flex space-x-3 pb-2 min-w-max">
                    <button class="tab-button active px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap bg-orange-500 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300" data-category="all">
                        All Products
                    </button>
                    <?php foreach ($categories as $category): ?>
                        <button class="tab-button px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap bg-white text-gray-600 shadow-md hover:shadow-lg hover:bg-gray-50 transform hover:scale-105 transition-all duration-300" data-category="<?php echo strtolower($category); ?>">
                            <?php echo ucfirst($category); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="animate-slide-up" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-dark">Popular Items</h3>
                    <p class="text-gray-500 text-sm">Fresh and quality guaranteed</p>
                </div>
                <a href="products.php" class="text-orange-500 text-sm font-semibold bg-orange-50 px-4 py-2 rounded-xl hover:bg-orange-100 transition-all duration-300 hover:scale-105 transform">
                    See all
                </a>
            </div>

            <!-- Product Grid -->
            <div id="products-grid" class="grid grid-cols-2 gap-4">
                <?php if (empty($products)): ?>
                    <!-- Empty State for Products -->
                    <div class="col-span-2 text-center py-16">
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M9 3v12a3 3 0 006 0V3"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-custom-dark mb-2">No Products Found</h3>
                        <p class="text-gray-500">There are currently no products available. Please check back later!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card bg-white rounded-3xl shadow-lg overflow-hidden animate-scale-in hover:shadow-xl transition-all duration-300 cursor-pointer"
                            data-category="<?php echo strtolower($product['category']); ?>"
                            data-name="<?php echo strtolower($product['name']); ?>"
                            onclick="viewProduct(<?php echo $product['id']; ?>)"
                            style="animation-delay: <?php echo rand(1, 6) * 0.1; ?>s;">
                            <div class="relative">
                                <?php
                                // Generate product image URL with fallback
                                $productImage = !empty($product['image']) && $product['image'] !== DEFAULT_PRODUCT_IMAGE
                                    ? PRODUCT_IMAGE_URL . htmlspecialchars($product['image'])
                                    : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE;
                                ?>
                                <img src="<?php echo $productImage; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-36 object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                <button class="favorite-btn absolute top-3 right-3 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg flex items-center justify-center hover:bg-white hover:scale-110 transition-all duration-300" onclick="event.stopPropagation();">
                                    <i class="far fa-heart text-gray-600 text-sm"></i>
                                </button>
                                <div class="absolute bottom-3 left-3">
                                    <span class="bg-orange-500 text-white text-xs font-semibold px-2 py-1 rounded-lg">
                                        Fresh
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-dark text-sm mb-1 line-clamp-1"><?php echo htmlspecialchars($product['name']); ?></h4>
                                <p class="text-gray-500 text-xs mb-3 line-clamp-2"><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-orange-500"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($product['price']); ?></span>
                                    <!-- Updated Cart Icon Button -->
                                    <button onclick="handleAddToCart(<?php echo $product['id']; ?>); event.stopPropagation();" class="add-to-cart-btn bg-gray-900 text-white w-10 h-10 rounded-xl flex items-center justify-center hover:bg-gray-800 transition-all duration-300 hover:scale-110 active:scale-95 shadow-md" data-product-id="<?php echo $product['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#F97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="8" cy="21" r="1" />
                                            <circle cx="19" cy="21" r="1" />
                                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Bottom navigation include -->
    <?php include 'partials/bottom-nav.php'; ?>

    <script src="js/script.js"></script>
    <script src="js/dashboard.js"></script>
    <script>
        // Function to navigate to product details
        function viewProduct(productId) {
            window.location.href = `product.php?id=${productId}`;
        }

        // Enhanced dashboard functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize cart count on page load
            updateCartCount();

            // Get DOM elements once
            const tabButtons = document.querySelectorAll('.tab-button');
            const productCards = document.querySelectorAll('.product-card');
            const searchInput = document.getElementById('search-input');
            const favoriteButtons = document.querySelectorAll('.favorite-btn');
            const categoryContainer = document.querySelector('.overflow-x-auto');

            // Enhanced add to cart function with immediate feedback
            window.handleAddToCart = async function(productId) {
                const button = document.querySelector(`[data-product-id="${productId}"]`);
                const originalContent = button.innerHTML;

                // Add loading state
                button.innerHTML = '<svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>';
                button.disabled = true;
                button.style.transform = 'scale(0.95)';

                try {
                    const success = await addToCart(productId, 1);

                    if (success) {
                        // Success animation
                        button.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
                        button.style.backgroundColor = '#22c55e';
                        button.style.transform = 'scale(1.1)';

                        // Update cart count with animation
                        const cartBadge = document.getElementById('cartCount');
                        if (cartBadge) {
                            cartBadge.parentElement.classList.add('animate-bounce-gentle');
                        }

                        setTimeout(() => {
                            button.innerHTML = originalContent;
                            button.style.backgroundColor = '';
                            button.style.transform = 'scale(1)';
                            button.disabled = false;

                            if (cartBadge) {
                                cartBadge.parentElement.classList.remove('animate-bounce-gentle');
                            }
                        }, 1000);
                    } else {
                        // Error state
                        button.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                        button.style.backgroundColor = '#ef4444';

                        setTimeout(() => {
                            button.innerHTML = originalContent;
                            button.style.backgroundColor = '';
                            button.style.transform = 'scale(1)';
                            button.disabled = false;
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Add to cart error:', error);
                    button.innerHTML = originalContent;
                    button.style.transform = 'scale(1)';
                    button.disabled = false;
                }
            };

            // Enhanced tab functionality with smooth transitions
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');

                    // Remove active class from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-orange-500', 'text-white');
                        btn.classList.add('bg-white', 'text-gray-600');
                    });

                    // Add active class to clicked button
                    this.classList.add('active', 'bg-orange-500', 'text-white');
                    this.classList.remove('bg-white', 'text-gray-600');

                    // Smooth scroll active tab into view
                    this.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });

                    // Filter products with stagger animation
                    productCards.forEach((card, index) => {
                        const cardCategory = card.getAttribute('data-category');

                        if (category === 'all' || cardCategory === category) {
                            card.style.display = 'block';
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';

                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                                card.style.transition = 'all 0.3s ease';
                            }, index * 50);
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // Add bounce animation to button
                    this.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });

            // Enhanced search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();

                    productCards.forEach(card => {
                        const productName = card.getAttribute('data-name');
                        const isVisible = productName.includes(searchTerm);

                        if (isVisible) {
                            card.style.display = 'block';
                            card.classList.add('animate-fade-in');
                        } else {
                            card.style.display = 'none';
                            card.classList.remove('animate-fade-in');
                        }
                    });
                });

                // Enhanced search bar focus effects
                searchInput.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.style.backgroundColor = '#ffffff';
                });

                searchInput.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                    if (!this.value) {
                        this.style.backgroundColor = '#f9fafb';
                    }
                });
            }

            // Enhanced favorite button functionality
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const icon = this.querySelector('i');

                    // Add scale animation
                    this.style.transform = 'scale(1.2)';

                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');

                        // Add heart beat animation
                        icon.style.animation = 'heartbeat 0.6s ease-in-out';
                    } else {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far', 'text-gray-600');
                        icon.style.animation = '';
                    }

                    // Reset scale
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 200);
                });
            });

            // Enhanced product card interactions
            productCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Responsive scroll behavior for category tabs
            if (categoryContainer) {
                let isScrolling = false;

                categoryContainer.addEventListener('scroll', () => {
                    if (!isScrolling) {
                        isScrolling = true;
                        requestAnimationFrame(() => {
                            // Add any scroll-based animations here
                            isScrolling = false;
                        });
                    }
                });
            }
        });

        // CSS animations for enhanced interactions
        const style = document.createElement('style');
        style.textContent = `
            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }
            
            @keyframes heartbeat {
                0%, 100% { transform: scale(1); }
                25% { transform: scale(1.1); }
                50% { transform: scale(1.2); }
                75% { transform: scale(1.1); }
            }
            
            .animate-bounce-gentle {
                animation: bounce-gentle 0.6s ease-in-out;
            }
            
            @keyframes bounce-gentle {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            
            .product-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .tab-button {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .line-clamp-1 {
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
            }
            
            .line-clamp-2 {
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }
            
            .animate-fade-in {
                animation: fadeIn 0.4s ease-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>