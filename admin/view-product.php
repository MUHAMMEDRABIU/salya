<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/util/utilities.php';
require __DIR__ . '/partials/headers.php';

// Get product ID from URL parameter
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if (!$productId) {
    header('Location: products.php');
    exit;
}

// Get product details
$product = getProductById($pdo, $productId);
if (!$product) {
    header('Location: products.php');
    exit;
}

// Get related data
$categories = getAllCategories($pdo);
$productImages = getProductImages($pdo, $productId);
$recentOrders = getRecentOrdersForProduct($pdo, $productId, 5);
?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>
        <!-- Product Details Content -->
        <main class="p-6">
            <!-- Breadcrumb -->
            <div class="mb-8">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="dashboard.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-orange-600">
                                <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                                <a href="products.php" class="ml-1 text-sm font-medium text-gray-700 hover:text-orange-600 md:ml-2">Products</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?= htmlspecialchars($product['name']) ?></span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <a href="products.php" class="text-gray-600 hover:text-gray-900 transition-colors group">
                            <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform duration-200"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($product['name']); ?></h1>
                            <p class="text-gray-600 mt-1">Product ID: #<?php echo $product['id']; ?></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <?php
                        $stockStatus = $product['in_stock'] <= 0 ? 'out-of-stock' : ($product['in_stock'] < 10 ? 'low-stock' : 'in-stock');
                        $statusColors = [
                            'in-stock' => 'bg-green-100 text-green-800 border-green-200',
                            'low-stock' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'out-of-stock' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                        $statusLabels = [
                            'in-stock' => 'In Stock',
                            'low-stock' => 'Low Stock',
                            'out-of-stock' => 'Out of Stock',
                        ];
                        $color = $statusColors[$stockStatus];
                        $label = $statusLabels[$stockStatus];
                        ?>
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold border <?php echo $color; ?>">
                            <i data-lucide="package" class="w-4 h-4 mr-2"></i>
                            <?php echo $label; ?>
                        </span>
                        <button id="editProductBtn" class="bg-orange-500 text-white px-6 py-2.5 rounded-lg hover:bg-orange-600 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5 font-medium">
                            <i data-lucide="edit" class="w-4 h-4 mr-2 inline"></i>
                            Edit Product
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Product Images and Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Product Images -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="aspect-w-16 aspect-h-10 bg-gray-100">
                            <img id="mainProductImage"
                                src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                class="w-full h-96 object-cover transition-all duration-300 hover:scale-105">
                        </div>
                        <?php if (!empty($productImages)): ?>
                            <div class="p-4 border-t border-gray-200">
                                <div class="flex space-x-3 overflow-x-auto">
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="Main"
                                        class="w-16 h-16 rounded-lg object-cover cursor-pointer border-2 border-orange-500 opacity-100 hover:opacity-80 transition-opacity thumbnail-image">
                                    <?php foreach ($productImages as $image): ?>
                                        <img src="../assets/uploads/<?php echo htmlspecialchars($image['image_path']); ?>"
                                            alt="Product view"
                                            class="w-16 h-16 rounded-lg object-cover cursor-pointer border-2 border-transparent hover:border-orange-300 opacity-70 hover:opacity-100 transition-all thumbnail-image">
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Product Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-800">Product Information</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Description</h4>
                                <p class="text-gray-600 leading-relaxed"><?php echo htmlspecialchars($product['description'] ?? 'No description available.'); ?></p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Category</span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                            <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Price</span>
                                        <span class="text-lg font-bold text-gray-900">₦<?php echo number_format($product['price'], 2); ?></span>
                                    </div>
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Stock Quantity</span>
                                        <span class="text-lg font-semibold <?php echo $product['in_stock'] < 10 ? 'text-red-600' : 'text-green-600'; ?>">
                                            <?php echo $product['in_stock']; ?> units
                                        </span>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">SKU</span>
                                        <span class="text-sm font-mono text-gray-900"><?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Weight</span>
                                        <span class="text-sm text-gray-900"><?php echo htmlspecialchars($product['weight'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Created</span>
                                        <span class="text-sm text-gray-900"><?php echo date('M d, Y', strtotime($product['created_at'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <?php if (!empty($recentOrders)): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-xl font-semibold text-gray-800">Recent Orders</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <?php foreach ($recentOrders as $order): ?>
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                                    <i data-lucide="shopping-cart" class="w-5 h-5 text-orange-600"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">Order #<?php echo $order['order_number']; ?></p>
                                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-gray-900">₦<?php echo number_format($order['total_amount'], 2); ?></p>
                                                <p class="text-sm text-gray-600"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column - Quick Stats and Actions -->
                <div class="space-y-6">
                    <!-- Quick Stats -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Quick Stats</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Total Sales</span>
                                <span class="text-lg font-bold text-green-600">₦<?php echo number_format($product['total_sales'] ?? 0, 2); ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Units Sold</span>
                                <span class="text-lg font-bold text-blue-600"><?php echo $product['units_sold'] ?? 0; ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Views</span>
                                <span class="text-lg font-bold text-purple-600"><?php echo $product['views'] ?? 0; ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Rating</span>
                                <div class="flex items-center space-x-1">
                                    <?php
                                    $rating = $product['rating'] ?? 0;
                                    for ($i = 1; $i <= 5; $i++):
                                    ?>
                                        <i data-lucide="star" class="w-4 h-4 <?php echo $i <= $rating ? 'text-yellow-400 fill-current' : 'text-gray-300'; ?>"></i>
                                    <?php endfor; ?>
                                    <span class="text-sm text-gray-600 ml-2">(<?php echo $rating; ?>)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Status -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Product Status</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Visibility</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Featured</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" <?php echo ($product['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                    </label>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <p class="text-xs text-gray-500">Last updated: <?php echo date('M d, Y \a\t g:i A', strtotime($product['updated_at'] ?? $product['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button id="deleteProductBtn" class="w-full bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition-all duration-200 font-medium hover:shadow-lg transform hover:-translate-y-0.5">
                                <i data-lucide="trash-2" class="w-4 h-4 mr-2 inline"></i>
                                Delete Product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Edit Product</h2>
                        <p class="text-sm text-gray-600 mt-1">Update product information and settings</p>
                    </div>
                    <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="editProductForm" class="p-6 space-y-8" method="post">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                <!-- Basic Information -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Product Name *</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">SKU</label>
                            <input type="text" name="sku" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Description</label>
                        <textarea name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 resize-none"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Category *</label>
                            <select name="category_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Price (₦) *</label>
                            <div class="relative z-10">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                                <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01" min="0" required
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Stock Quantity *</label>
                            <input type="number" name="in_stock" value="<?php echo $product['in_stock']; ?>" min="0" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Weight</label>
                            <input type="text" name="weight" value="<?php echo htmlspecialchars($product['weight'] ?? ''); ?>"
                                placeholder="e.g., 1kg, 500g"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Dimensions</label>
                            <input type="text" name="dimensions" value="<?php echo htmlspecialchars($product['dimensions'] ?? ''); ?>"
                                placeholder="e.g., 10x5x3 cm"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>
                    </div>
                </div>

                <!-- Product Image -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Product Image</h3>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                <img name="image" id="currentImage" src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="Current product image" class="w-24 h-24 rounded-lg object-cover border border-gray-300">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload New Image</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 10MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Settings -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Product Settings</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Product Visibility</label>
                                    <p class="text-xs text-gray-500">Show this product in your store</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Featured Product</label>
                                    <p class="text-xs text-gray-500">Highlight this product on homepage</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_featured" value="1" class="sr-only peer" <?php echo ($product['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <h4 class="text-sm font-semibold text-blue-800 mb-2">SEO Settings</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-blue-700 mb-1">Meta Title</label>
                                        <input type="text" name="meta_title" value="<?php echo htmlspecialchars($product['meta_title'] ?? ''); ?>"
                                            class="w-full px-3 py-2 text-sm border border-blue-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-blue-700 mb-1">Meta Description</label>
                                        <textarea name="meta_description" rows="2"
                                            class="w-full px-3 py-2 text-sm border border-blue-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo htmlspecialchars($product['meta_description'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 rounded-b-xl">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                        Changes will be saved immediately
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" id="cancelBtn" class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Cancel
                        </button>
                        <button id="updateProductBtn" type="submit" form="editProductForm" class="px-6 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all duration-200 font-medium hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteConfirmModal" class="fixed inset-0 modal-overlay z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-content bg-white rounded-3xl p-6 w-full max-w-sm animate-modal-in">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="trash" class="text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-2">Delete</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to delete product?</p>
                    <div class="flex space-x-3">
                        <button onclick="hideModal()" class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-2xl font-semibold hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button id="deleteConfirmBtn" class="flex-1 bg-red-500 text-white py-3 rounded-2xl font-semibold hover:bg-red-600 transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script src="../assets/js/confirmation-modal.js"></script>
    <script src="../assets/js/loading-overlay.js"></script>
    <script src="../assets/js/toast.js"></script>
    <script>
        // Image gallery functionality
        document.querySelectorAll('.thumbnail-image').forEach(img => {
            img.addEventListener('click', function() {
                const mainImage = document.getElementById('mainProductImage');
                mainImage.src = this.src;

                // Update active thumbnail
                document.querySelectorAll('.thumbnail-image').forEach(thumb => {
                    thumb.classList.remove('border-orange-500', 'opacity-100');
                    thumb.classList.add('border-transparent', 'opacity-70');
                });
                this.classList.remove('border-transparent', 'opacity-70');
                this.classList.add('border-orange-500', 'opacity-100');
            });
        });

        // Modal functionality
        const editProductBtn = document.getElementById('editProductBtn');
        const editProductModal = document.getElementById('editProductModal');
        const modalContent = document.getElementById('modalContent');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const editProductForm = document.getElementById('editProductForm');

        // Open modal with animation
        editProductBtn.addEventListener('click', () => {
            editProductModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Trigger animation
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        });

        // Close modal function
        function closeModal() {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                editProductModal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        function hideModal() {
            const deleteConfirmModal = document.getElementById('deleteConfirmModal');
            deleteConfirmModal.classList.add('hidden');
        }

        // Close modal events
        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Close modal when clicking outside
        editProductModal.addEventListener('click', (e) => {
            if (e.target === editProductModal) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !editProductModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Image preview functionality
        const imageInput = document.querySelector('input[name="image"]');
        const currentImage = document.getElementById('currentImage');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        editProductForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const submitBtn = document.getElementById('updateProductBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Saving...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData(editProductForm);

                const response = fetch('api/update-product.php', {
                    method: 'POST',
                    body: formData
                });

                const result = JSON.stringify(response);
                console.log(result);

                if (result.success) {
                    // Show success message
                    showToasted('Product updated successfully!', 'success');

                    // Close modal after short delay
                    setTimeout(() => {
                        closeModal();
                        // Optionally reload the page to show updated data
                        window.location.reload();
                    }, 1500);
                } else {
                    showToasted(result.message || 'Failed to update product', 'error');
                    console.error('Update failed:', result);
                }
            } catch (error) {
                console.error('Error:', error);
                showToasted('An error occurred while updating the product', 'error');
            } finally {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        // Delete product confirmation
        document.getElementById('deleteProductBtn').addEventListener('click', () => {
            const deleteConfirmModal = document.getElementById('deleteConfirmModal');
            deleteConfirmModal.classList.remove('hidden');

            document.getElementById('deleteConfirmBtn').addEventListener('click', () => {
                const productId = <?php echo $product['id']; ?>;
                fetch('api/delete-product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId
                        })
                    })

                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToasted('Product deleted successfully!', 'success');
                            setTimeout(() => {
                                window.location.href = 'products.php';
                            }, 1500);
                        } else {
                            showToasted(data.message || 'Failed to delete product', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToasted('An error occurred while deleting the product', 'error');
                    })
                    .finally(() => {
                        deleteConfirmModal.classList.add('hidden');
                    });
            });
        });

        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>

</html>