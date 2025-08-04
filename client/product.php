<?php
require_once 'util/util.php';
require_once 'initialize.php';
require_once 'partials/headers.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$product = getProductById($pdo, $product_id);

// Cart count
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<body class="bg-custom-gray min-h-screen">
  <div class="container mx-auto pt-6">
    <!-- Header -->
    <?php include 'partials/top-nav.php'; ?>

    <!-- Product Content -->
    <div class="p-4 product-content px-4 sm:px-8 relative fade-in">
      <div class="flex items-center relative min-h-[250px]">
        <!-- Product Info -->
        <div class="flex-1 z-10">
          <div>
            <div class="flex items-center flex-wrap">
              <h1 class="text-3xl sm:text-4xl font-bold text-custom-dark mb-4 mr-4">
                <?php echo htmlspecialchars($product['name']); ?>
              </h1>
              <div class="mb-4 flex items-center space-x-2">
                <?php if (!empty($product['in_stock'])): ?>
                  <span class="bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    In Stock
                  </span>
                <?php else: ?>
                  <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Out of Stock
                  </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="mb-6">
              <p class="text-gray-300 text-sm">Price</p>
              <p class="text-lg text-accent font-bold text-custom-dark">₦<?php echo number_format($product['price'], 2); ?></p>
            </div>

            <div>
              <p class="text-gray-200 text-md mb-4">Choose quantity</p>
              <div class="flex items-center">
                <button id="decreaseBtn" class="bg-white quantity-btn w-12 h-12 flex items-center justify-center text-xl font-bold rounded-xl shadow-lg text-custom-dark">
                  -
                </button>
                <span id="quantity" class="text-base font-bold text-custom-dark min-w-[4rem] text-center">1</span>
                <button id="increaseBtn" class="bg-white quantity-btn w-12 h-12 flex items-center justify-center text-xl font-bold rounded-xl shadow-lg text-custom-dark">
                  +
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Product Image -->
        <div class="flex-1 relative">
          <div class="absolute right-0 top-1/2 -translate-y-1/2 z-20">
            <img
              src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>"
              alt="<?php echo htmlspecialchars($product['name']); ?>"
              class="w-40 h-40 sm:w-56 sm:h-56 md:w-64 md:h-64 object-cover rounded-2xl product-image"
              style="max-width: none;">
          </div>
        </div>
      </div>
    </div>

    <!-- Description Section -->
    <div class="mt-8 bg-white w-full py-8 px-6 shadow-2xl fade-in" style="border-radius: 24px 24px 0 0;">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-custom-dark">Description</h2>
        <div class="flex items-center space-x-1">
          <div class="flex items-center">
            <?php
            $rating = isset($product['rating']) ? $product['rating'] : 4.9;
            $fullStars = floor($rating);
            $hasHalfStar = $rating - $fullStars >= 0.5;

            for ($i = 0; $i < $fullStars; $i++): ?>
              <svg class="w-5 h-5 star-rating" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
              </svg>
            <?php endfor; ?>
          </div>
          <span class="text-lg font-bold text-custom-accent"><?php echo $rating; ?></span>
        </div>
      </div>

      <p class="text-gray-600 text-base leading-relaxed mb-6">
        <?php echo htmlspecialchars($product['description']); ?>
      </p>

      <!-- Features Section -->
      <?php if (!empty($product['features'])):
        $features = is_array($product['features']) ? $product['features'] : json_decode($product['features'], true);
      ?>
        <div class="mt-8">
          <h3 class="text-lg font-semibold text-custom-dark mb-4">Key Features</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php foreach ($features as $feature): ?>
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700"><?php echo htmlspecialchars($feature); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-4 mt-10">
        <button id="orderBtn" class="flex-1 bg-custom-accent text-white py-4 px-8 rounded-2xl font-semibold text-base action-button hover:bg-opacity-90 transition-all duration-300 min-w-[160px] flex items-center justify-center">
          <span id="orderBtnText">Order Now</span>
          <div id="orderBtnSpinner" class="loading-spinner ml-2 hidden"></div>
        </button>
        <button id="addCartBtn" class="flex-1 border-2 border-custom-accent text-custom-accent py-4 px-8 rounded-2xl font-semibold text-base action-button hover:bg-custom-accent hover:text-orange-600 transition-all duration-300 min-w-[160px] flex items-center justify-center">
          <span id="addCartBtnText">Add to Cart</span>
          <div id="addCartBtnSpinner" class="loading-spinner ml-2 hidden"></div>
        </button>
      </div>
    </div>
  </div>

  <script src="js/script.js"></script>
  <script src="../assets/js/toast.js"></script>
  <script>
    // Product data
    const productData = {
      id: <?php echo json_encode($product_id); ?>,
      name: <?php echo json_encode($product['name']); ?>,
      price: <?php echo json_encode($product['price']); ?>,
      rating: <?php echo json_encode(isset($product['rating']) ? $product['rating'] : 4.9); ?>,
      description: <?php echo json_encode($product['description']); ?>,
      inStock: <?php echo json_encode(!empty($product['in_stock'])); ?>
    };

    let currentQuantity = 1;

    // DOM elements
    const quantityDisplay = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decreaseBtn');
    const increaseBtn = document.getElementById('increaseBtn');
    const orderBtn = document.getElementById('orderBtn');
    const addCartBtn = document.getElementById('addCartBtn');
    const backBtn = document.getElementById('backBtn');

    // Quantity functions with improved animations
    function updateQuantity(newQuantity) {
      if (newQuantity >= 1) {
        currentQuantity = newQuantity;
        quantityDisplay.textContent = currentQuantity;

        // Add pulse animation
        quantityDisplay.style.transform = 'scale(1.2)';
        quantityDisplay.style.transition = 'transform 0.2s ease';

        setTimeout(() => {
          quantityDisplay.style.transform = 'scale(1)';
        }, 200);
      }
    }

    function decreaseQuantity() {
      if (currentQuantity > 1) {
        updateQuantity(currentQuantity - 1);
      }
    }

    function increaseQuantity() {
      updateQuantity(currentQuantity + 1);
    }

    // Enhanced cart functionality
    async function addToCart(isOrderNow = false) {
      if (!productData.inStock) {
        showToasted('Product is out of stock', 'error');
        return;
      }

      const button = isOrderNow ? orderBtn : addCartBtn;
      const buttonText = isOrderNow ? document.getElementById('orderBtnText') : document.getElementById('addCartBtnText');
      const spinner = isOrderNow ? document.getElementById('orderBtnSpinner') : document.getElementById('addCartBtnSpinner');

      // Show loading state
      button.disabled = true;
      buttonText.textContent = isOrderNow ? 'Processing...' : 'Adding...';
      spinner.classList.remove('hidden');

      const cartData = {
        product_id: productData.id,
        quantity: currentQuantity
      };

      try {
        const response = await fetch('api/add-to-cart.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(cartData)
        });

        const result = await response.json();

        if (result.success) {
          showToasted(isOrderNow ? 'Added to cart! Redirecting to checkout...' : 'Added to cart!', 'success');

          // Update cart count
          if (result.cart_count !== undefined) {
            const cartCountElem = document.getElementById('cartCount');
            if (cartCountElem) {
              cartCountElem.textContent = result.cart_count;
            }
          }

          // Show success state
          buttonText.textContent = isOrderNow ? 'Added!' : 'Added!';
          button.style.backgroundColor = 'var(--success-color)';
          button.style.borderColor = 'var(--success-color)';
          button.style.color = 'white';

          // Redirect for order now
          if (isOrderNow) {
            setTimeout(() => {
              window.location.href = 'checkout.php';
            }, 1500);
          }
        } else {
          showToasted(result.message || 'Failed to add to cart', 'error');
        }
      } catch (err) {
        showToasted('An error occurred. Please try again.', 'error');
        console.error('Add to cart error:', err);
      } finally {
        spinner.classList.add('hidden');

        setTimeout(() => {
          button.disabled = false;
          buttonText.textContent = isOrderNow ? 'Order Now' : 'Add to Cart';
          button.style.backgroundColor = '';
          button.style.borderColor = '';
          button.style.color = '';
        }, 2000);
      }
    }

    // Event listeners
    decreaseBtn.addEventListener('click', decreaseQuantity);
    increaseBtn.addEventListener('click', increaseQuantity);
    orderBtn.addEventListener('click', () => addToCart(true));
    addCartBtn.addEventListener('click', () => addToCart(false));

    if (backBtn) {
      backBtn.addEventListener('click', () => {
        window.history.length > 1 ? window.history.back() : window.location.href = 'dashboard.php';
      });
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowUp' || e.key === '+') {
        e.preventDefault();
        increaseQuantity();
      } else if (e.key === 'ArrowDown' || e.key === '-') {
        e.preventDefault();
        decreaseQuantity();
      }
    });

    // Touch gestures for quantity
    let touchStartY = 0;
    quantityDisplay.addEventListener('touchstart', (e) => {
      touchStartY = e.touches[0].clientY;
    });

    quantityDisplay.addEventListener('touchend', (e) => {
      const touchEndY = e.changedTouches[0].clientY;
      const difference = touchStartY - touchEndY;

      if (Math.abs(difference) > 30) {
        if (difference > 0) {
          increaseQuantity();
        } else {
          decreaseQuantity();
        }
      }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
      console.log('Enhanced product details page loaded');
      console.log('Product:', productData);
    });
  </script>
</body>

</html>