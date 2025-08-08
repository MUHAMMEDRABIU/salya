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
            const closeAddModalBtn = document.getElementById('closeAddModalBtn');
            const cancelAddBtn = document.getElementById('cancelAddBtn');
            const addProductForm = document.getElementById('addProductForm');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');

            // Function to open add product modal
            window.openAddProductModal = function() {
                addProductModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Trigger animation
                setTimeout(() => {
                    addModalContent.classList.remove('scale-95', 'opacity-0');
                    addModalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            };

            // Function to close modal
            function closeAddModal() {
                addModalContent.classList.remove('scale-100', 'opacity-100');
                addModalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    addProductModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    resetForm();
                }, 300);
            }

            // Function to reset form
            function resetForm() {
                addProductForm.reset();
                imagePreview.src = "<?php echo PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE; ?>";
            }

            // Close modal events
            closeAddModalBtn.addEventListener('click', closeAddModal);
            cancelAddBtn.addEventListener('click', closeAddModal);

            // Close modal when clicking outside
            addProductModal.addEventListener('click', (e) => {
                if (e.target === addProductModal) {
                    closeAddModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !addProductModal.classList.contains('hidden')) {
                    closeAddModal();
                }
            });

            // Image preview functionality
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size using PHP constant
                    if (file.size > <?php echo MAX_PRODUCT_IMAGE_SIZE; ?>) {
                        showToasted('File size too large. Maximum size is <?php echo number_format(MAX_PRODUCT_IMAGE_SIZE / (1024 * 1024), 0); ?>MB.', 'error');
                        this.value = '';
                        return;
                    }

                    // Validate file type
                    const allowedTypes = [
                        '<?php echo ALLOWED_IMAGE_JPEG; ?>',
                        '<?php echo ALLOWED_IMAGE_PNG; ?>',
                        '<?php echo ALLOWED_IMAGE_GIF; ?>',
                        '<?php echo ALLOWED_IMAGE_WEBP; ?>'
                    ];
                    if (!allowedTypes.includes(file.type)) {
                        showToasted('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.', 'error');
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Form submission with confirmation
            addProductForm.addEventListener('submit', (e) => {
                e.preventDefault();

                // Get form data
                const formData = new FormData(addProductForm);
                const productName = formData.get('name');
                const productPrice = formData.get('price');

                const submitBtn = document.getElementById('submitProductBtn');
                const originalText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Adding Product...';
                submitBtn.disabled = true;

                try {
                    // Submit form with AJAX
                    submitProduct(formData, submitBtn, originalText);
                } catch (error) {
                    console.error('Error:', error);
                    showToasted('An error occurred while adding the product', 'error');
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });

            // AJAX form submission function
            async function submitProduct(formData, submitBtn, originalText) {
                try {
                    const response = await fetch('api/add-product.php', {
                        method: 'POST',
                        body: formData
                    });

                    // Check if response is ok
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        // Show success message
                        showToasted('Product added successfully!', 'success');

                        // Close modal after short delay
                        setTimeout(() => {
                            closeAddModal();
                            // Optionally reload the page to show new product
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
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }

            // Form validation
            const requiredFields = addProductForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.classList.add('border-red-500');
                        this.classList.remove('border-gray-300');
                    } else {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-300');
                    }
                });

                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-300');
                    }
                });
            });

            // Price formatting
            const priceInput = addProductForm.querySelector('input[name="price"]');
            priceInput.addEventListener('input', function() {
                const value = parseFloat(this.value);
                if (!isNaN(value) && value >= 0) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-gray-300');
                }
            });

            // Stock validation
            const stockInput = addProductForm.querySelector('input[name="in_stock"]');
            stockInput.addEventListener('input', function() {
                const value = parseInt(this.value);
                if (!isNaN(value) && value >= 0) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-gray-300');
                }
            });

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>

</html>