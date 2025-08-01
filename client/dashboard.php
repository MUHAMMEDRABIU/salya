<?php
require_once 'util/util.php';
require_once 'initialize.php';
require_once 'partials/headers.php';

// Get all products for the dashboard
$products = getAllProducts($pdo);
$categories = getProductCategories($pdo);
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
                <div class="relative">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl border border-white/30 flex items-center justify-center">
                        <!-- <i class="fas fa-snowflake text-white text-xl"></i> -->
                        <div class="w-10 h-10 flex items-center justify-center">
                            <!-- Cart Icon SVG -->
                            <a href="cart.php">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Cart body -->
                                    <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.5 5.1 16.5H17M17 13V16.5"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round" />

                                    <!-- Cart wheels -->
                                    <circle cx="9" cy="20" r="1"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round" />

                                    <circle cx="20" cy="20" r="1"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round" />

                                    <!-- Premium accent line (optional highlight) -->
                                    <path d="M8 9H19"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        opacity="0.6" />
                                </svg>
                            </a>
                        </div>

                    </div>
                </div>
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

        <!-- Search Bar -->
        <div class="search-container animate-slide-up" style="animation-delay: 0.1s;">
            <div class="relative">
                <input
                    type="text"
                    id="search-input"
                    placeholder="Search for chicken, fish, turkey..."
                    class="w-full pl-14 pr-4 py-4 rounded-2xl border-0 bg-white shadow-lg focus:ring-2 focus:ring-accent focus:outline-none text-base transition-all duration-300 hover:shadow-xl">
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-search text-gray-400 text-lg"></i>
                </div>
                <button class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 bg-accent rounded-xl flex items-center justify-center text-white hover:bg-orange-600 transition-colors">
                    <i class="fas fa-sliders-h text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="overflow-x-auto hide-scrollbar animate-slide-up" style="animation-delay: 0.2s;">
            <div class="flex space-x-3 pb-2">
                <button class="tab-button active px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap bg-accent text-white shadow-lg hover:shadow-xl transform hover:scale-105" data-category="all">
                    All
                </button>
                <?php foreach ($categories as $category): ?>
                    <button class="tab-button px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap bg-white text-gray-600 shadow-md hover:shadow-lg hover:bg-gray-50 transform hover:scale-105" data-category="<?php echo strtolower($category); ?>">
                        <?php echo ucfirst($category); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Products Section -->
        <div class="animate-slide-up" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-dark">Popular Items</h3>
                    <p class="text-gray-500 text-sm">Fresh and quality guaranteed</p>
                </div>
                <button class="text-accent text-sm font-semibold bg-orange-50 px-4 py-2 rounded-xl hover:bg-orange-100 transition-colors">
                    See all
                </button>
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
                        <div class="product-card bg-white rounded-3xl shadow-lg overflow-hidden animate-scale-in" data-category="<?php echo strtolower($product['category']); ?>" data-name="<?php echo strtolower($product['name']); ?>" style="animation-delay: <?php echo rand(1, 6) * 0.1; ?>s;">
                            <div class="relative">
                                <img src="../assets/uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-36 object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                <button class="favorite-btn absolute top-3 right-3 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg flex items-center justify-center hover:bg-white hover:scale-110 transition-all duration-300">
                                    <i class="far fa-heart text-gray-600 text-sm"></i>
                                </button>
                                <div class="absolute bottom-3 left-3">
                                    <span class="bg-accent text-white text-xs font-semibold px-2 py-1 rounded-lg">
                                        Fresh
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-dark text-sm mb-1 line-clamp-1"><?php echo $product['name']; ?></h4>
                                <p class="text-gray-500 text-xs mb-3 line-clamp-2"><?php echo $product['description']; ?></p>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-accent">₦<?php echo number_format($product['price']); ?></span>
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="bg-dark text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-gray-800 transition-all duration-300 hover:scale-105 active:scale-95">
                                        View
                                    </a>
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

    <script src="js/dashboard.js"></script>
    <script>
        // Premium mobile interactions with enhanced animations
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced tab functionality with smooth transitions
            const tabButtons = document.querySelectorAll('.tab-button');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons with animation
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-accent', 'text-white');
                        btn.classList.add('bg-white', 'text-gray-600');
                        btn.style.transform = 'scale(1)';
                    });

                    // Add active class to clicked button with bounce animation
                    this.classList.add('active', 'bg-accent', 'text-white');
                    this.classList.remove('bg-white', 'text-gray-600');
                    this.style.transform = 'scale(1.05)';

                    // Add bounce animation
                    this.classList.add('animate-bounce-gentle');
                    setTimeout(() => {
                        this.classList.remove('animate-bounce-gentle'); 
                        this.style.transform = 'scale(1)';
                    }, 600);
                });
            });

            // Enhanced favorite button functionality
            const favoriteButtons = document.querySelectorAll('.favorite-btn');

            favoriteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const icon = this.querySelector('i');

                    // Add scale animation
                    this.style.transform = 'scale(1.2)';

                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');

                        // Add heart beat animation
                        icon.style.animation = 'bounce-gentle 0.6s ease-in-out';
                    } else {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far');
                        icon.style.animation = '';
                    }

                    // Reset scale
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 200);
                });
            });

            // Enhanced bottom navigation with premium interactions
            const navItems = document.querySelectorAll('.nav-item');

            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Don't prevent default to allow navigation

                    // Remove active class from all nav items
                    navItems.forEach(nav => {
                        nav.classList.remove('nav-item-active');
                    });

                    // Add active class to clicked item
                    this.classList.add('nav-item-active');

                    // Add ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(249, 115, 22, 0.3)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.left = '50%';
                    ripple.style.top = '50%';
                    ripple.style.width = '60px';
                    ripple.style.height = '60px';
                    ripple.style.marginLeft = '-30px';
                    ripple.style.marginTop = '-30px';

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });

                // Add hover effects for desktop
                item.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('nav-item-active')) {
                        const icon = this.querySelector('.nav-icon');
                        icon.style.transform = 'translateY(-2px) scale(1.05)';
                    }
                });

                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('nav-item-active')) {
                        const icon = this.querySelector('.nav-icon');
                        icon.style.transform = 'translateY(0) scale(1)';
                    }
                });
            });

            // Enhanced search functionality with animations
            const searchInput = document.getElementById('search-input');

            searchInput.addEventListener('focus', function() {
                this.parentElement.parentElement.style.transform = 'scale(1.02)';
            });

            searchInput.addEventListener('blur', function() {
                this.parentElement.parentElement.style.transform = 'scale(1)';
            });

            // Stagger animation for product cards
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>

</html>