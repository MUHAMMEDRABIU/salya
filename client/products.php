<?php
require_once 'util/util.php';
require_once 'initialize.php';
require_once '../config/constants.php';

// Get cart count for logged in users
$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items FROM cart_items WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cartCount = (int)($result['total_items'] ?? 0);
    } catch (Exception $e) {
        error_log("Error getting cart count in products: " . $e->getMessage());
        $cartCount = 0;
    }
}

// Get selected category from URL parameter
$selectedCategory = isset($_GET['category']) ? strtolower(trim($_GET['category'])) : 'all';

// Get all products and categories
$products = getAllProducts($pdo);
$categories = getProductCategories($pdo);

// Filter products based on selected category
$filteredProducts = $products;
if ($selectedCategory !== 'all') {
    $filteredProducts = array_filter($products, function ($product) use ($selectedCategory) {
        return strtolower($product['category']) === $selectedCategory;
    });
}

// Sort options
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Apply sorting
if (!empty($filteredProducts)) {
    usort($filteredProducts, function ($a, $b) use ($sortBy, $sortOrder) {
        $valueA = $a[$sortBy] ?? '';
        $valueB = $b[$sortBy] ?? '';

        if ($sortBy === 'price') {
            $valueA = (float)$valueA;
            $valueB = (float)$valueB;
        }

        $result = $valueA <=> $valueB;
        return $sortOrder === 'desc' ? -$result : $result;
    });
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$itemsPerPage = 12;
$totalItems = count($filteredProducts);
$totalPages = ceil($totalItems / $itemsPerPage);
$offset = ($page - 1) * $itemsPerPage;
$paginatedProducts = array_slice($filteredProducts, $offset, $itemsPerPage);

require_once 'partials/headers.php';
?>

<body class="bg-gray-50 font-dm pb-24 overflow-x-hidden">
    <!-- Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-orange-500 opacity-5 rounded-full filter blur-3xl animate-float"></div>
        <div class="absolute top-1/2 -left-32 w-64 h-64 bg-purple-500 opacity-5 rounded-full filter blur-3xl animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 right-1/4 w-48 h-48 bg-orange-500 opacity-8 rounded-full filter blur-3xl animate-float" style="animation-delay: 2s;"></div>
    </div>

    <!-- Main Content -->
    <main class="relative z-10">
        <div class="container mx-auto px-4 pt-6">
            <!-- Header -->
            <?php include 'partials/top-nav.php'; ?>

            <!-- Hero Section -->
            <div class="bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 rounded-3xl p-8 text-white mb-8 relative overflow-hidden animate-slide-up">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10">
                    <div class="max-w-2xl">
                        <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold mb-4">Premium Frozen Foods</h1>
                        <p class="text-orange-100 text-lg mb-6">Discover our complete collection of fresh, high-quality frozen foods delivered right to your doorstep.</p>
                        <div class="flex items-center space-x-6 text-orange-100">
                            <div class="flex items-center">
                                <i class="fas fa-snowflake mr-2"></i>
                                <span class="text-sm">Always Fresh</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-truck mr-2"></i>
                                <span class="text-sm">Fast Delivery</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-star mr-2"></i>
                                <span class="text-sm">Premium Quality</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white rounded-2xl p-6 mb-8 shadow-lg animate-slide-up" style="animation-delay: 0.1s;">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <!-- Search Bar -->
                    <div class="flex-1 lg:max-w-md">
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
                                placeholder="Search products..."
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <select id="sort-select" class="appearance-none bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pr-10 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                <option value="name-asc" <?php echo ($sortBy === 'name' && $sortOrder === 'asc') ? 'selected' : ''; ?>>Name (A-Z)</option>
                                <option value="name-desc" <?php echo ($sortBy === 'name' && $sortOrder === 'desc') ? 'selected' : ''; ?>>Name (Z-A)</option>
                                <option value="price-asc" <?php echo ($sortBy === 'price' && $sortOrder === 'asc') ? 'selected' : ''; ?>>Price (Low-High)</option>
                                <option value="price-desc" <?php echo ($sortBy === 'price' && $sortOrder === 'desc') ? 'selected' : ''; ?>>Price (High-Low)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Grid/List Toggle -->
                        <div class="flex bg-gray-100 rounded-xl p-1">
                            <button id="grid-view" class="view-toggle active px-3 py-2 rounded-lg text-gray-600 hover:text-orange-500 transition-colors">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button id="list-view" class="view-toggle px-3 py-2 rounded-lg text-gray-600 hover:text-orange-500 transition-colors">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="mb-8 animate-slide-up" style="animation-delay: 0.2s;">
                <div class="overflow-x-auto hide-scrollbar">
                    <div class="flex space-x-3 pb-2 min-w-max">
                        <a href="?category=all&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>"
                            class="category-tab <?php echo $selectedCategory === 'all' ? 'active' : ''; ?> px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300 hover:scale-105 shadow-md">
                            All Products
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="?category=<?php echo strtolower($category); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>"
                                class="category-tab <?php echo $selectedCategory === strtolower($category) ? 'active' : ''; ?> px-6 py-3 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300 hover:scale-105 shadow-md">
                                <?php echo ucfirst($category); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Results Info -->
            <div class="flex items-center justify-between mb-6 animate-slide-up" style="animation-delay: 0.3s;">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">
                        <?php echo $selectedCategory === 'all' ? 'All Products' : ucfirst($selectedCategory); ?>
                    </h2>
                    <p class="text-gray-600">
                        Showing <?php echo count($paginatedProducts); ?> of <?php echo $totalItems; ?> products
                        <?php if ($selectedCategory !== 'all'): ?>
                            in <?php echo ucfirst($selectedCategory); ?>
                        <?php endif; ?>
                    </p>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="text-sm text-gray-500">
                        Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Products Grid -->
            <div id="products-container" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                <?php if (empty($paginatedProducts)): ?>
                    <!-- Empty State -->
                    <div class="col-span-full text-center py-16 animate-fade-in">
                        <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">No Products Found</h3>
                        <p class="text-gray-600 mb-6 max-w-md mx-auto">
                            <?php if ($selectedCategory !== 'all'): ?>
                                No products found in the <?php echo ucfirst($selectedCategory); ?> category.
                            <?php else: ?>
                                No products match your search criteria.
                            <?php endif; ?>
                        </p>
                        <a href="products.php" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-arrow-left mr-2"></i>
                            View All Products
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($paginatedProducts as $index => $product): ?>
                        <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 cursor-pointer animate-scale-in"
                            data-category="<?php echo strtolower($product['category']); ?>"
                            data-name="<?php echo strtolower($product['name']); ?>"
                            onclick="viewProduct(<?php echo $product['id']; ?>)"
                            style="animation-delay: <?php echo ($index * 0.1); ?>s;">
                            <div class="relative">
                                <?php
                                // Generate product image URL with fallback
                                $productImage = !empty($product['image']) && $product['image'] !== DEFAULT_PRODUCT_IMAGE
                                    ? PRODUCT_IMAGE_URL . htmlspecialchars($product['image'])
                                    : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE;
                                ?>
                                <img src="<?php echo $productImage; ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                    class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>

                                <!-- Favorite Button -->
                                <button class="favorite-btn absolute top-3 right-3 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-xl shadow-lg flex items-center justify-center hover:bg-white hover:scale-110 transition-all duration-300" onclick="event.stopPropagation();">
                                    <i class="far fa-heart text-gray-600"></i>
                                </button>

                                <!-- Stock Badge -->
                                <div class="absolute top-3 left-3">
                                    <?php if (isset($product['stock_quantity']) && $product['stock_quantity'] > 0): ?>
                                        <span class="bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded-lg">
                                            In Stock
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-lg">
                                            Out of Stock
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Quick Add Button -->
                                <div class="absolute bottom-3 right-3 opacity-0 hover:opacity-100 transition-opacity duration-300">
                                    <button onclick="handleAddToCart(<?php echo $product['id']; ?>); event.stopPropagation();"
                                        class="quick-add-btn bg-orange-500 text-white w-10 h-10 rounded-xl flex items-center justify-center hover:bg-orange-600 transition-all duration-300 hover:scale-110 shadow-lg"
                                        data-product-id="<?php echo $product['id']; ?>">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="8" cy="21" r="1" />
                                            <circle cx="19" cy="21" r="1" />
                                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="p-5">
                                <div class="mb-3">
                                    <span class="text-xs font-medium text-orange-500 uppercase tracking-wide">
                                        <?php echo htmlspecialchars($product['category']); ?>
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-900 text-lg mb-2 line-clamp-2 leading-tight">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-relaxed">
                                    <?php echo htmlspecialchars($product['description']); ?>
                                </p>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-2xl font-bold text-orange-500">
                                            <?php echo CURRENCY_SYMBOL; ?><?php echo number_format($product['price']); ?>
                                        </span>
                                    </div>

                                    <!-- Rating -->
                                    <div class="flex items-center space-x-1">
                                        <div class="flex text-yellow-400">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-500 font-medium">4.9</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="flex justify-center items-center space-x-2 animate-slide-up">
                    <!-- Previous Button -->
                    <?php if ($page > 1): ?>
                        <a href="?category=<?php echo $selectedCategory; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>&page=<?php echo $page - 1; ?>"
                            class="pagination-btn px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);

                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="?category=<?php echo $selectedCategory; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>&page=<?php echo $i; ?>"
                            class="pagination-btn px-4 py-2 rounded-lg transition-all duration-300 <?php echo $i === $page ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-300'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Next Button -->
                    <?php if ($page < $totalPages): ?>
                        <a href="?category=<?php echo $selectedCategory; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>&page=<?php echo $page + 1; ?>"
                            class="pagination-btn px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Bottom navigation include -->
    <?php include 'partials/bottom-nav.php'; ?>

    <!-- Scripts -->
    <script src="js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize cart count
            updateCartCount();

            // View product function
            window.viewProduct = function(productId) {
                window.location.href = `product.php?id=${productId}`;
            };

            // Sort functionality
            const sortSelect = document.getElementById('sort-select');
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    const [sortBy, order] = this.value.split('-');
                    const url = new URL(window.location.href);
                    url.searchParams.set('sort', sortBy);
                    url.searchParams.set('order', order);
                    window.location.href = url.toString();
                });
            }

            // Search functionality
            const searchInput = document.getElementById('search-input');
            const productCards = document.querySelectorAll('.product-card');

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
            }

            // View toggle functionality
            const gridView = document.getElementById('grid-view');
            const listView = document.getElementById('list-view');
            const productsContainer = document.getElementById('products-container');

            if (gridView && listView && productsContainer) {
                gridView.addEventListener('click', function() {
                    document.querySelectorAll('.view-toggle').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    productsContainer.className = 'grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8';
                });

                listView.addEventListener('click', function() {
                    document.querySelectorAll('.view-toggle').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    productsContainer.className = 'grid grid-cols-1 gap-4 mb-8';
                });
            }

            // Enhanced add to cart function
            window.handleAddToCart = async function(productId) {
                const button = document.querySelector(`[data-product-id="${productId}"]`);
                const originalContent = button.innerHTML;

                // Add loading state
                button.innerHTML = '<svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>';
                button.disabled = true;

                try {
                    const success = await addToCart(productId, 1);

                    if (success) {
                        // Success animation
                        button.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
                        button.style.backgroundColor = '#22c55e';

                        setTimeout(() => {
                            button.innerHTML = originalContent;
                            button.style.backgroundColor = '';
                            button.disabled = false;
                        }, 1000);
                    } else {
                        // Error state
                        button.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                        button.style.backgroundColor = '#ef4444';

                        setTimeout(() => {
                            button.innerHTML = originalContent;
                            button.style.backgroundColor = '';
                            button.disabled = false;
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Add to cart error:', error);
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            };

            // Enhanced favorite functionality
            const favoriteButtons = document.querySelectorAll('.favorite-btn');
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const icon = this.querySelector('i');

                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');
                    } else {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far', 'text-gray-600');
                    }

                    // Scale animation
                    this.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 200);
                });
            });

            // Product card hover effects
            productCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';

                    // Show quick add button
                    const quickAddBtn = this.querySelector('.quick-add-btn');
                    if (quickAddBtn) {
                        quickAddBtn.parentElement.style.opacity = '1';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';

                    // Hide quick add button
                    const quickAddBtn = this.querySelector('.quick-add-btn');
                    if (quickAddBtn) {
                        quickAddBtn.parentElement.style.opacity = '0';
                    }
                });
            });

            // Stagger animation for product cards
            productCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>

</html>