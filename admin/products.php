<?php
require __DIR__ . '/initialize.php';
require __DIR__ . '/util/utilities.php';
require __DIR__ . '/../config/constants.php';

$productStats = getProductStats($pdo);
$products = getAllProducts($pdo);

require __DIR__ . '/partials/headers.php';
?>

<body class="bg-gray-50 font-sans">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content lg:ml-64">
        <!-- Top Navigation -->
        <?php require __DIR__ . '/partials/top-navbar.php'; ?>
        <!-- Products Content -->
        <main class="p-6">
            <!-- Products Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Products -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= $productStats['total'] ?? 0 ?>
                            </p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <i data-lucide="package" class="w-6 h-6 text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Low Stock -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Low Stock</p>
                            <p class="text-2xl font-bold text-red-600">
                                <?= $productStats['in_stock'] ?? 0 ?>
                            </p>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Categories</p>
                            <p class="text-2xl font-bold text-orange-600">
                                <?= $productStats['categories'] ?? 0 ?>
                            </p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg">
                            <i data-lucide="layers" class="w-6 h-6 text-orange-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Out of Stock -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                            <p class="text-2xl font-bold text-gray-600">
                                <?= $productStats['out_of_stock'] ?? 0 ?>
                            </p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <i data-lucide="x-circle" class="w-6 h-6 text-gray-600"></i>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Category Management Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Category Management</h3>
                        <button onclick="openAddCategoryModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            <i data-lucide="folder-plus" class="w-4 h-4 mr-2 inline"></i>
                            Add Category
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <?php
                        // Fetch categories from database
                        try {
                            $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($categories as $category):
                        ?>
                                <div class="category-card bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors border border-gray-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                                <i data-lucide="folder" class="w-4 h-4 text-orange-600"></i>
                                            </div>
                                            <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($category['name']); ?></h4>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <button onclick="editCategory(<?php echo $category['id']; ?>)" class="text-gray-400 hover:text-blue-500 p-1">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            <button onclick="deleteCategory(<?php echo $category['id']; ?>)" class="text-gray-400 hover:text-red-500 p-1">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <?php if (!empty($category['description'])): ?>
                                        <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars(substr($category['description'], 0, 80)) . (strlen($category['description']) > 80 ? '...' : ''); ?></p>
                                    <?php endif; ?>

                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">
                                            <?php
                                            // Get product count for this category
                                            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                                            $countStmt->execute([$category['id']]);
                                            $productCount = $countStmt->fetchColumn();
                                            echo $productCount . ' product' . ($productCount !== 1 ? 's' : '');
                                            ?>
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            <?php echo date('M j, Y', strtotime($category['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                        <?php
                            endforeach;
                        } catch (Exception $e) {
                            echo '<div class="col-span-full text-center text-gray-500 py-8">Error loading categories</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Products Management -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Product Inventory</h3>
                        <div class="flex items-center space-x-4">
                            <select class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option>All Categories</option>
                                <option>Chicken</option>
                                <option>Fish</option>
                                <option>Turkey</option>
                            </select>
                            <button onclick="openAddProductModal()" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                                <i data-lucide="plus" class="w-4 h-4 mr-2 inline"></i>
                                Add Product
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-gray-500 py-6">No products found.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($products as $product): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace">
                                        <div class="flex items-center">
                                            <?php
                                            // Generate product image URL with fallback
                                            $productImage = !empty($product['image']) && $product['image'] !== DEFAULT_PRODUCT_IMAGE
                                                ? PRODUCT_IMAGE_URL . htmlspecialchars($product['image'])
                                                : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE;
                                            ?>
                                            <img onerror="this.src='<?= PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE ?>'" src="<?php echo $productImage; ?>" alt="Product" class="w-12 h-12 rounded-lg object-cover mr-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($product['name']) ?></div>
                                                <div class="text-sm text-gray-500"><?= htmlspecialchars($product['description']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <?= htmlspecialchars($product['category_name']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo CURRENCY_SYMBOL; ?><?= number_format($product['price'], 2) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm <?= $product['in_stock'] < 10 ? 'text-red-600' : 'text-gray-900' ?>">
                                        <?= $product['in_stock'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($product['in_stock'] <= 0): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                Out of Stock
                                            </span>
                                        <?php elseif ($product['in_stock'] < 10): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Low Stock
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                In Stock
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="view-product.php?product_id=<?= $product['id'] ?>" class="text-xs bg-gray-100 px-3 rounded py-1 text-orange-600 hover:text-orange-900 mr-3">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing 1 to 10 of 245 products
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</button>
                            <button class="px-3 py-1 text-sm text-white bg-orange-500 border border-orange-500 rounded">1</button>
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">2</button>
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">3</button>
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="addCategoryModalContent">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Add New Category</h2>
                    <button id="closeCategoryModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <form id="addCategoryForm" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category Name *</label>
                    <input type="text" name="name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        placeholder="e.g., Chicken, Fish, Turkey">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                        placeholder="Brief description of this category..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category Image</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <p class="text-xs text-gray-500 mt-1">Optional: Upload an image for this category</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="cancelCategoryBtn" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 flex items-center">
                        <i data-lucide="folder-plus" class="w-4 h-4 mr-2"></i>
                        Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="editCategoryModalContent">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Edit Category</h2>
                    <button id="closeEditCategoryModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <form id="editCategoryForm" class="p-6 space-y-4">
                <input type="hidden" name="category_id" id="editCategoryId">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category Name *</label>
                    <input type="text" name="name" id="editCategoryName" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="editCategoryDescription" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category Image</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="cancelEditCategoryBtn" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="addModalContent">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Add New Product</h2>
                        <p class="text-sm text-gray-600 mt-1">Create a new product for your inventory</p>
                    </div>
                    <button id="closeAddModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="addProductForm" class="p-6 space-y-8" enctype="multipart/form-data">
                <!-- Basic Information -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Product Name *</label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                placeholder="Enter product name">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">SKU</label>
                            <input type="text" name="sku"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                placeholder="Product SKU (optional)">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Description</label>
                        <textarea name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 resize-none"
                            placeholder="Describe your product..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Category *</label>
                            <select name="category_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white">
                                <option value="">Select Category</option>
                                <?php
                                $categories = getAllCategories($pdo);
                                foreach ($categories as $category):
                                ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Price (<?php echo CURRENCY_SYMBOL; ?>) *</label>
                            <div class="relative z-10">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"><?php echo CURRENCY_SYMBOL; ?></span>
                                <input type="number" name="price" step="0.01" min="0" required
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                    placeholder="0.00">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Stock Quantity *</label>
                            <input type="number" name="in_stock" min="0" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Weight</label>
                            <input type="text" name="weight"
                                placeholder="e.g., 1kg, 500g"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Dimensions</label>
                            <input type="text" name="dimensions"
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
                                <img name="img" id="imagePreview" src="data:image/svg+xml,%3csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100' height='100' fill='%23f3f4f6'/%3e%3ctext x='50%25' y='50%25' font-size='14' text-anchor='middle' dy='.3em' fill='%236b7280'%3eNo Image%3c/text%3e%3c/svg%3e"
                                    alt="Product preview" class="w-24 h-24 rounded-lg object-cover border border-gray-300">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Product Image</label>
                                <input type="file" name="image" accept="image/*" id="imageInput"
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
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Featured Product</label>
                                    <p class="text-xs text-gray-500">Highlight this product on homepage</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_featured" value="1" class="sr-only peer">
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
                                        <input type="text" name="meta_title"
                                            class="w-full px-3 py-2 text-sm border border-blue-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-blue-700 mb-1">Meta Description</label>
                                        <textarea name="meta_description" rows="2"
                                            class="w-full px-3 py-2 text-sm border border-blue-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
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
                        All fields marked with * are required
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" id="cancelAddBtn" class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Cancel
                        </button>
                        <button id="submitProductBtn" type="submit" form="addProductForm" class="px-6 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all duration-200 font-medium hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Add Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <script src="js/script.js"></script>
    <script src="../assets/js/confirmation-modal.js"></script>
    <script src="../assets/js/loading-overlay.js"></script>
    <script src="../assets/js/toast.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal elements
            const addProductModal = document.getElementById('addProductModal');
            const addModalContent = document.getElementById('addModalContent');
            const addProductForm = document.getElementById('addProductForm');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');

            // Category modal elements
            const addCategoryModal = document.getElementById('addCategoryModal');
            const addCategoryModalContent = document.getElementById('addCategoryModalContent');
            const editCategoryModal = document.getElementById('editCategoryModal');
            const editCategoryModalContent = document.getElementById('editCategoryModalContent');

            // =============================================================================
            // CATEGORY MANAGEMENT FUNCTIONS
            // =============================================================================

            // Make functions global by attaching to window
            window.openAddCategoryModal = function() {
                addCategoryModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    addCategoryModalContent.classList.remove('scale-95', 'opacity-0');
                    addCategoryModalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            };

            window.editCategory = function(categoryId) {
                fetch(`api/get-category.php?id=${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('editCategoryId').value = data.category.id;
                            document.getElementById('editCategoryName').value = data.category.name;
                            document.getElementById('editCategoryDescription').value = data.category.description || '';

                            editCategoryModal.classList.remove('hidden');
                            document.body.style.overflow = 'hidden';
                            setTimeout(() => {
                                editCategoryModalContent.classList.remove('scale-95', 'opacity-0');
                                editCategoryModalContent.classList.add('scale-100', 'opacity-100');
                            }, 10);
                        } else {
                            showToasted(data.message || 'Failed to load category', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToasted('Failed to load category', 'error');
                    });
            };

            window.deleteCategory = function(categoryId) {
                if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                    fetch('api/delete-category.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `category_id=${categoryId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showToasted('Category deleted successfully', 'success');
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                showToasted(data.message || 'Failed to delete category', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToasted('Failed to delete category', 'error');
                        });
                }
            };

            function closeAddCategoryModal() {
                addCategoryModalContent.classList.remove('scale-100', 'opacity-100');
                addCategoryModalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    addCategoryModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    document.getElementById('addCategoryForm').reset();
                }, 300);
            }

            function closeEditCategoryModal() {
                editCategoryModalContent.classList.remove('scale-100', 'opacity-100');
                editCategoryModalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    editCategoryModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    document.getElementById('editCategoryForm').reset();
                }, 300);
            }

            // =============================================================================
            // PRODUCT MANAGEMENT FUNCTIONS
            // =============================================================================

            window.openAddProductModal = function() {
                addProductModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    addModalContent.classList.remove('scale-95', 'opacity-0');
                    addModalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            };

            function closeAddProductModal() {
                addModalContent.classList.remove('scale-100', 'opacity-100');
                addModalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    addProductModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    addProductForm.reset();
                    imagePreview.src = "<?php echo PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE; ?>";
                }, 300);
            }

            async function submitProduct(formData) {
                const submitBtn = document.getElementById('submitProductBtn');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Adding Product...';
                submitBtn.disabled = true;

                try {
                    const response = await fetch('api/add-product.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const result = await response.json();

                    if (result.success) {
                        showToasted('Product added successfully!', 'success');
                        setTimeout(() => {
                            closeAddProductModal();
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToasted(result.message || 'Failed to add product', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    if (error.name === 'TypeError' && error.message.includes('fetch')) {
                        showToasted('Network error. Please check your connection and try again.', 'error');
                    } else if (error.message.includes('HTTP error')) {
                        showToasted('Server error. Please try again later.', 'error');
                    } else {
                        showToasted('An unexpected error occurred while adding the product', 'error');
                    }
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }

            // =============================================================================
            // EVENT LISTENERS
            // =============================================================================

            // Category form submissions
            document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('api/add-category.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToasted('Category added successfully', 'success');
                            closeAddCategoryModal();
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToasted(data.message || 'Failed to add category', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToasted('Failed to add category', 'error');
                    });
            });

            document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('api/edit-category.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToasted('Category updated successfully', 'success');
                            closeEditCategoryModal();
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToasted(data.message || 'Failed to update category', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToasted('Failed to update category', 'error');
                    });
            });

            // Category modal close buttons
            document.getElementById('closeCategoryModalBtn').addEventListener('click', closeAddCategoryModal);
            document.getElementById('cancelCategoryBtn').addEventListener('click', closeAddCategoryModal);
            document.getElementById('closeEditCategoryModalBtn').addEventListener('click', closeEditCategoryModal);
            document.getElementById('cancelEditCategoryBtn').addEventListener('click', closeEditCategoryModal);

            // Product modal close buttons
            document.getElementById('closeAddModalBtn').addEventListener('click', closeAddProductModal);
            document.getElementById('cancelAddBtn').addEventListener('click', closeAddProductModal);

            // Product form submission
            addProductForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(addProductForm);
                submitProduct(formData);
            });

            // Image preview
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Validate file size
                if (file.size > <?php echo MAX_PRODUCT_IMAGE_SIZE; ?>) {
                    showToasted('File size too large. Maximum size is <?php echo number_format(MAX_PRODUCT_IMAGE_SIZE / (1024 * 1024), 0); ?>MB.', 'error');
                    this.value = '';
                    return;
                }

                // Validate file type
                const allowedTypes = ['<?php echo ALLOWED_IMAGE_JPEG; ?>', '<?php echo ALLOWED_IMAGE_PNG; ?>', '<?php echo ALLOWED_IMAGE_GIF; ?>', '<?php echo ALLOWED_IMAGE_WEBP; ?>'];
                if (!allowedTypes.includes(file.type)) {
                    showToasted('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.', 'error');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => imagePreview.src = e.target.result;
                reader.readAsDataURL(file);
            });

            // Close modals when clicking outside
            [addProductModal, addCategoryModal, editCategoryModal].forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        if (modal === addProductModal) closeAddProductModal();
                        else if (modal === addCategoryModal) closeAddCategoryModal();
                        else if (modal === editCategoryModal) closeEditCategoryModal();
                    }
                });
            });

            // Close modals with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (!addProductModal.classList.contains('hidden')) closeAddProductModal();
                    if (!addCategoryModal.classList.contains('hidden')) closeAddCategoryModal();
                    if (!editCategoryModal.classList.contains('hidden')) closeEditCategoryModal();
                }
            });

            // Form validation for required fields
            const requiredFields = addProductForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    this.classList.toggle('border-red-500', !this.value.trim());
                    this.classList.toggle('border-gray-300', this.value.trim());
                });

                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-300');
                    }
                });
            });

            // Price and stock validation
            const priceInput = addProductForm.querySelector('input[name="price"]');
            const stockInput = addProductForm.querySelector('input[name="in_stock"]');

            [priceInput, stockInput].forEach(input => {
                input.addEventListener('input', function() {
                    const value = input === priceInput ? parseFloat(this.value) : parseInt(this.value);
                    if (!isNaN(value) && value >= 0) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-300');
                    }
                });
            });

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>

</html>