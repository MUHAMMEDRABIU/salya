<!-- Cart Items -->
<div class="lg:col-span-2">
    <div class="frosted-glass rounded-2xl p-4 sm:p-6 lg:p-8 shadow-2xl border border-white/20 backdrop-blur-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-custom-dark flex items-center">
                <i class="fas fa-shopping-bag mr-2 sm:mr-3 text-accent text-lg sm:text-xl"></i>
                <span class="bg-gradient-to-r from-custom-dark to-gray-700 bg-clip-text text-transparent">Cart Items</span>
            </h2>
            <button id="clear-cart-btn" class="text-red-500 hover:text-red-600 text-xs sm:text-sm font-semibold px-3 sm:px-4 py-2 rounded-xl hover:bg-red-50 transition-all duration-300 transform hover:scale-105 border border-red-200 hover:border-red-300 shadow-sm">
                <i class="fas fa-trash mr-1 sm:mr-2"></i>
                Clear Cart
            </button>
        </div>

        <!-- Cart Items List -->
        <div id="cart-items" class="space-y-3 sm:space-y-4">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item group bg-white/70 backdrop-blur-sm border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-500 overflow-hidden" data-item-id="<?php echo $item['product_id']; ?>" data-price="<?php echo $item['price']; ?>">
                    <!-- Mobile Layout -->
                    <div class="block sm:hidden p-4">
                        <div class="flex items-start space-x-4 mb-4">
                            <div class="relative overflow-hidden rounded-xl shadow-lg">
                                <img src="../assets/uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-16 h-16 object-cover transition-transform duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-custom-dark text-sm leading-tight mb-1 truncate"><?php echo $item['name']; ?></h3>
                                <p class="text-accent font-bold text-base">₦<?php echo number_format($item['price']); ?></p>
                            </div>
                            <button class="remove-item-btn text-red-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition-all duration-300" data-item-id="<?php echo $item['product_id']; ?>">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <button class="quantity-btn decrease-btn w-8 h-8 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center hover:from-accent hover:to-orange-600 hover:text-white transition-all duration-300 shadow-md" data-action="decrease">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="quantity-display font-bold text-custom-dark text-sm bg-white/90 px-3 py-1 rounded-lg shadow-md min-w-[2.5rem] text-center"><?php echo $item['quantity']; ?></span>
                                <button class="quantity-btn increase-btn w-8 h-8 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center hover:from-accent hover:to-orange-600 hover:text-white transition-all duration-300 shadow-md" data-action="increase">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <div class="text-right">
                                <p class="item-total font-bold text-custom-dark text-lg">₦<?php echo number_format($item['price'] * $item['quantity']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop/Tablet Layout -->
                    <div class="hidden sm:flex items-center p-4 lg:p-6 space-x-4 lg:space-x-6">
                        <div class="relative overflow-hidden rounded-2xl shadow-lg">
                            <img src="../assets/uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 object-cover transition-transform duration-300 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-custom-dark text-sm sm:text-base lg:text-lg mb-1"><?php echo $item['name']; ?></h3>
                            <p class="text-accent font-bold text-base sm:text-lg lg:text-xl">₦<?php echo number_format($item['price']); ?></p>
                        </div>

                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <button class="quantity-btn decrease-btn w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center hover:from-accent hover:to-orange-600 hover:text-white transition-all duration-300 shadow-lg hover:shadow-xl" data-action="decrease">
                                <i class="fas fa-minus text-xs sm:text-sm"></i>
                            </button>
                            <span class="quantity-display font-bold text-custom-dark text-sm sm:text-base lg:text-lg bg-white/90 px-3 py-2 rounded-xl shadow-lg min-w-[3rem] text-center"><?php echo $item['quantity']; ?></span>
                            <button class="quantity-btn increase-btn w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center hover:from-accent hover:to-orange-600 hover:text-white transition-all duration-300 shadow-lg hover:shadow-xl" data-action="increase">
                                <i class="fas fa-plus text-xs sm:text-sm"></i>
                            </button>
                        </div>

                        <div class="text-right">
                            <p class="item-total font-bold text-custom-dark text-base sm:text-lg lg:text-xl mb-2">₦<?php echo number_format($item['price'] * $item['quantity']); ?></p>
                            <button class="remove-item-btn text-red-500 hover:text-red-600 text-xs sm:text-sm font-medium px-2 sm:px-3 py-1 rounded-lg hover:bg-red-50 transition-all duration-300 transform hover:scale-105 border border-red-200 hover:border-red-300" data-item-id="<?php echo $item['product_id']; ?>">
                                <i class="fas fa-times mr-1"></i>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty Cart State -->
        <div id="empty-cart" class="hidden text-center py-12 sm:py-16 lg:py-20">
            <div class="w-24 h-24 sm:w-32 sm:h-32 lg:w-40 lg:h-40 mx-auto mb-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 rounded-full flex items-center justify-center shadow-2xl">
                <i class="fas fa-shopping-cart text-gray-400 text-2xl sm:text-3xl lg:text-4xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-600 mb-3 bg-gradient-to-r from-gray-600 to-gray-800 bg-clip-text text-transparent">Your cart is empty</h3>
            <p class="text-gray-500 text-sm sm:text-base mb-6 sm:mb-8 max-w-sm sm:max-w-md mx-auto leading-relaxed px-4">Add some delicious frozen foods to get started on your culinary journey!</p>
            <a href="dashboard.php" class="btn-primary text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold inline-flex items-center space-x-2 shadow-accent hover:shadow-2xl transition-all duration-300 transform hover:scale-105 text-sm sm:text-base">
                <i class="fas fa-utensils text-sm sm:text-base"></i>
                <span>Continue Shopping</span>
            </a>
        </div>
    </div>
</div>