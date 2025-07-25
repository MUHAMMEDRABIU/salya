<?php
require_once 'util/util.php';
require_once 'initialize.php';
require_once 'partials/headers.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$product = getProductById($pdo, $product_id);

// Cart count
$cartCount = isset($_SESSION['cart']) ? array_sum(array: array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<body class="bg-custom-gray min-h-screen">
  <div class="container mx-auto pt-6">
    <!-- Header -->
    <?php include 'partials/top-nav.php'; ?>

    <!-- Product Content -->
    <div class="p-4 product-content px-4 sm:px-8 relative">
      <div class="flex items-center relative min-h-[250px]">
        <!-- Product Info -->
        <div class="flex-1 z-10">
          <div>
            <div class="flex items-center">
              <p class="text-3xl font-bold text-custom-dark mb-4"><?php echo htmlspecialchars($product['name']); ?>
              </p>
              <div class="mb-4 ml-2 flex items-center space-x-2">
                <?php if (!empty($product['in_stock'])): ?>
                  <span class="bg-green-500 text-white inline flex-nowrap text-xs font-semibold px-2 py-1 rounded-full flex items-center">
                    In Stock
                  </span>
                <?php else: ?>
                  <span class="bg-red-500 text-white inline flex-nowrap text-xs font-semibold px-2 py-1 rounded-full flex items-center">
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
              <p class="text-gray-300 text-md mb-4">Choice quantity</p>
              <div class="flex items-center space-x-2">
                <button id="decreaseBtn" class="bg-white quantity-btn w-12 h-12 flex items-center justify-center text-xl font-bold rounded-lg shadow-xl text-custom-dark hover:border-custom-accent hover-accent">
                  -
                </button>
                <span id="quantity" class="text-2xl font-bold text-custom-dark min-w-[3rem] text-center">1</span>
                <button id="increaseBtn" class="bg-white quantity-btn w-12 h-12 flex items-center justify-center text-xl font-bold rounded-lg shadow-xl text-custom-dark hover:border-custom-accent hover-accent">
                  +
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Product Image -->
        <div class="flex-1 relative">
          <div class="absolute right-0 top-1/2 -translate-y-1/2 z-20 pointer-events-none select-none">
            <img
              src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>"
              alt="<?php echo htmlspecialchars($product['name']); ?>"
              class="w-40 h-40 sm:w-56 sm:h-56 md:w-64 md:h-64 object-cover"
              style="max-width: none;">
          </div>
        </div>
      </div>
    </div>

    <!-- Description Section -->
    <div class="mt-8 bg-white w-full py-8 px-6 shadow-lg" style="border-radius: 24px 24px 0 0;">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-base font-bold text-custom-dark">Description</h2>
        <div class="flex items-center space-x-1">
          <div class="flex items-center">
            <svg class="w-4 h-4 star-rating" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
          </div>
          <span class="text-base font-bold text-custom-accent"><?php echo isset($product['rating']) ? $product['rating'] : '4.9'; ?></span>
        </div>
      </div>

      <p class="text-gray-300 text-base leading-relaxed mb-6">
        <?php echo htmlspecialchars($product['description']); ?>
      </p>

      <!-- Features Section -->
      <?php if (!empty($product['features'])):
        $features = is_array($product['features']) ? $product['features'] : json_decode($product['features'], true);
      ?>
        <div class="mt-8">
          <h3 class="text-lg font-semibold text-custom-dark mb-4">Features</h3>
          <ul class="list-disc pl-6 text-gray-700 space-y-2">
            <?php foreach ($features as $feature): ?>
              <li><?php echo htmlspecialchars($feature); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>


      <!-- Action Buttons -->
      <div class="flex flex-row gap-3 mt-8 flex-wrap sm:flex-nowrap">
        <button id="orderBtn" class="flex-1 bg-custom-accent text-white py-4 px-6 rounded-2xl font-semibold text-sm hover:opacity-90 transition-opacity duration-200 min-w-[140px]">
          Order Now
        </button>
        <button id="addCartBtn" class="flex-1 border-2 border-accent text-custom-dark py-4 px-6 rounded-2xl font-semibold text-sm hover:border-custom-accent hover-accent transition-all duration-200 min-w-[140px]">
          Add to Cart
        </button>
      </div>
    </div>
  </div>

  <script src="js/script.js"></script>
  <script src="../assets/js/toast.js"></script>
  <script>
    // Product data
    const productData = {
      name: <?php echo json_encode($product['name']); ?>,
      price: <?php echo json_encode($product['price']); ?>,
      rating: <?php echo json_encode(isset($product['rating']) ? $product['rating'] : 4.9); ?>,
      description: <?php echo json_encode($product['description']); ?>
    };

    let currentQuantity = 1;

    // DOM elements
    const quantityDisplay = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decreaseBtn');
    const increaseBtn = document.getElementById('increaseBtn');
    const orderBtn = document.getElementById('orderBtn');
    const addCartBtn = document.getElementById('addCartBtn');
    const backBtn = document.getElementById('backBtn');

    // Quantity functions
    function updateQuantity(newQuantity) {
      if (newQuantity >= 1) {
        currentQuantity = newQuantity;
        quantityDisplay.textContent = currentQuantity;
        quantityDisplay.style.transform = 'scale(1.2)';
        setTimeout(() => {
          quantityDisplay.style.transform = 'scale(1)';
        }, 150);
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

    decreaseBtn.addEventListener('click', decreaseQuantity);
    increaseBtn.addEventListener('click', increaseQuantity);

    orderBtn.addEventListener('click', () => {
      window.location.href = "cart.php"
    });

    addCartBtn.addEventListener('click', async () => {
      // Prepare cart data
      const cartData = {
        product_id: <?php echo json_encode($product_id); ?>,
        quantity: currentQuantity
      };

      // Disable button and show loading state
      addCartBtn.disabled = true;
      addCartBtn.textContent = 'Adding...';

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
          showToasted('Added to cart!', 'success');

          // if undefined
          if (result.cart_count !== undefined) {
            document.getElementById('cartCount').textContent = result.cart_count;
          }

          // Update button state
          addCartBtn.textContent = 'Added!';
          addCartBtn.style.backgroundColor = 'var(--accent-color)';
          addCartBtn.style.color = 'white';
          addCartBtn.style.borderColor = 'var(--accent-color)';

          // Update cart count
          let cartCountElem = document.getElementById('cartCount');
          let currentCount = parseInt(cartCountElem.textContent) || 0;
          cartCountElem.textContent = currentCount + currentQuantity;

        } else {
          showToasted(result.message || 'Failed to add to cart', 'error');
          addCartBtn.textContent = 'Add to Cart';
        }
      } catch (err) {
        showToasted('An error occurred. Please try again.', 'error');
        addCartBtn.textContent = 'Add to Cart';
        console.error('Add to cart error:', err);
      } finally {
        setTimeout(() => {
          addCartBtn.disabled = false;
          addCartBtn.textContent = 'Add to Cart';
          addCartBtn.style.backgroundColor = '';
          addCartBtn.style.color = '';
          addCartBtn.style.borderColor = '';
        }, 1500);
      }
    });

    backBtn.addEventListener('click', () => {
      window.history.length > 1 ? window.history.back() : window.location.href = 'dashboard.php';
    });

    quantityDisplay.style.transition = 'transform 0.15s ease';

    document.addEventListener('DOMContentLoaded', () => {
      console.log('Product details page loaded');
      console.log('Product:', productData);
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowUp' || e.key === '+') {
        e.preventDefault();
        increaseQuantity();
      } else if (e.key === 'ArrowDown' || e.key === '-') {
        e.preventDefault();
        decreaseQuantity();
      }
    });

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
  </script>
</body>

</html>