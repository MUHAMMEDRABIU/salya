<!-- USERS UTIL FUNCTIONS -->
<?php
/**
 * Get user profile by ID
 * @param int $user_id
 * @return array|null
 */
function getUserProfile($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching user profile: " . $e->getMessage());
        return null;
    }
}

/**
 * Get user's cart items with product details from database
 */
function getUserCartItems($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                ci.id as cart_id,
                ci.quantity,
                ci.created_at,
                p.id as product_id,
                p.name,
                p.price,
                p.image,
                p.stock_quantity
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = ?
            ORDER BY ci.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching cart items: " . $e->getMessage());
        return [];
    }
}

/**
 * Get cart totals for user
 */
function getUserCartTotals($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                SUM(ci.quantity * p.price) as subtotal,
                SUM(ci.quantity) as total_items
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $subtotal = $result['subtotal'] ?? 0;
        $delivery_fee = $subtotal >= 10000 ? 0 : 500;
        $tax = 0;
        $total = $subtotal + $delivery_fee + $tax;

        return [
            'subtotal' => $subtotal,
            'delivery_fee' => $delivery_fee,
            'tax' => $tax,
            'total' => $total,
            'item_count' => $result['total_items'] ?? 0
        ];
    } catch (PDOException $e) {
        error_log("Error calculating cart totals: " . $e->getMessage());
        return [
            'subtotal' => 0,
            'delivery_fee' => 500,
            'tax' => 0,
            'total' => 500,
            'item_count' => 0
        ];
    }
}

/**
 * Update user profile
 * @param PDO $pdo
 * @param int $user_id
 * @param array $profile_data
 * @return bool
 */
function updateUserProfile($pdo, $user_id, $profile_data)
{
    $required_fields = ['name', 'email', 'phone', 'address'];
    foreach ($required_fields as $field) {
        if (!isset($profile_data[$field]) || empty(trim($profile_data[$field]))) {
            return false;
        }
    }

    if (!filter_var($profile_data['email'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    if (!validatePhoneNumber($profile_data['phone'])) {
        return false;
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :user_id");
        $stmt->bindParam(':name', $profile_data['name']);
        $stmt->bindParam(':email', $profile_data['email']);
        $stmt->bindParam(':phone', $profile_data['phone']);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Optionally update address in user_addresses table
        $stmt2 = $pdo->prepare("UPDATE user_addresses SET street_address = :address WHERE user_id = :user_id");
        $stmt2->bindParam(':address', $profile_data['address']);
        $stmt2->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt2->execute();

        return true;
    } catch (PDOException $e) {
        error_log("Error updating user profile: " . $e->getMessage());
        return false;
    }
}

/**
 * Change user password
 * @param PDO $pdo
 * @param int $user_id
 * @param string $current_password
 * @param string $new_password
 * @return bool
 */
function changeUserPassword($pdo, $user_id, $current_password, $new_password)
{
    if (strlen($new_password) < 6) {
        return false;
    }

    try {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($current_password, $user['password'])) {
            return false;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt2 = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
        $stmt2->bindParam(':password', $hashed_password);
        $stmt2->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt2->execute();

        return true;
    } catch (PDOException $e) {
        error_log("Error changing password: " . $e->getMessage());
        return false;
    }
}

function getAllNotifications($pdo, $user_id = null)
{
    try {
        if ($user_id) {
            $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching notifications: " . $e->getMessage());
        return [];
    }
}

/**
 * Update notification preferences
 * @param PDO $pdo
 * @param int $user_id
 * @param array $preferences
 * @return bool
 */
function updateNotificationPreferences($pdo, $user_id, $preferences)
{
    $valid_preferences = ['email_notifications', 'sms_notifications', 'marketing_notifications'];
    $prefs = [];
    foreach ($valid_preferences as $pref) {
        $prefs[$pref] = isset($preferences[$pref]) ? (int)$preferences[$pref] : 0;
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET email_notifications = :email, sms_notifications = :sms, marketing_notifications = :marketing WHERE id = :user_id");
        $stmt->bindParam(':email', $prefs['email_notifications'], PDO::PARAM_INT);
        $stmt->bindParam(':sms', $prefs['sms_notifications'], PDO::PARAM_INT);
        $stmt->bindParam(':marketing', $prefs['marketing_notifications'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Error updating notification preferences: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user statistics
 * @param PDO $pdo
 * @param int $user_id
 * @return array
 */
function getUserStatistics($pdo, $user_id)
{
    try {
        // Total orders
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetch(PDO::FETCH_ASSOC);

        // Total spent
        $stmt2 = $pdo->prepare("SELECT SUM(total_amount) as total_amount FROM orders WHERE user_id = ?");
        $stmt2->execute([$user_id]);
        $spent = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Member since
        $stmt4 = $pdo->prepare("SELECT created_at FROM users WHERE id = ?");
        $stmt4->execute([$user_id]);
        $user = $stmt4->fetch(PDO::FETCH_ASSOC);

        return [
            'total_orders' => $orders['total_orders'] ?? 0,
            'total_amount' => $spent['total_amount'] ?? 0,
            'last_order_date' => $orders['last_order_date'] ?? null,
            'member_since' => $user['created_at'] ?? null
        ];
    } catch (PDOException $e) {
        error_log("Error fetching user statistics: " . $e->getMessage());
        return [];
    }
}
/**
 * Get user's recent orders
 * @param PDO $pdo
 * @param int $user_id
 * @param int $limit
 * @return array
 */
function getUserRecentOrders($pdo, $user_id, $limit = 5)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching recent orders: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user's favorite products
 * @param PDO $pdo
 * @param int $user_id
 * @return array
 */

/**
 * Add product to user favorites
 * @param PDO $pdo
 * @param int $user_id
 * @param int $product_id
 * @return bool
 */
function addToFavorites($pdo, $user_id, $product_id)
{
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_favorites (user_id, product_id) VALUES (:user_id, :product_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Error adding to favorites: " . $e->getMessage());
        return false;
    }
}

/**
 * Remove product from user favorites
 * @param PDO $pdo
 * @param int $user_id
 * @param int $product_id
 * @return bool
 */
function removeFromFavorites($pdo, $user_id, $product_id)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Error removing from favorites: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if product is in user favorites
 * @param PDO $pdo
 * @param int $user_id
 * @param int $product_id
 * @return bool
 */
function isProductFavorite($pdo, $user_id, $product_id)
{
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_favorites WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}


function getStatusInfo($status)
{
    switch ($status) {
        case 'delivered':
            return ['color' => 'text-green-600', 'bg' => 'bg-green-100', 'icon' => 'fas fa-check-circle'];
        case 'processing':
            return ['color' => 'text-blue-600', 'bg' => 'bg-blue-100', 'icon' => 'fas fa-clock'];
        case 'pending':
            return ['color' => 'text-yellow-600', 'bg' => 'bg-yellow-100', 'icon' => 'fas fa-hourglass-half'];
        case 'cancelled':
            return ['color' => 'text-red-600', 'bg' => 'bg-red-100', 'icon' => 'fas fa-times-circle'];
        default:
            return ['color' => 'text-gray-600', 'bg' => 'bg-gray-100', 'icon' => 'fas fa-question-circle'];
    }
}

function getUserActivityLog($user_id)
{
    // In real app, fetch from database
    return [
        [
            'action' => 'Profile Updated',
            'timestamp' => '2025-01-01 14:30:00',
            'details' => 'Updated delivery address'
        ],
        [
            'action' => 'Order Placed',
            'timestamp' => '2025-01-01 12:15:00',
            'details' => 'Order #FF20250101001'
        ],
        [
            'action' => 'Password Changed',
            'timestamp' => '2024-12-28 09:45:00',
            'details' => 'Password updated successfully'
        ]
    ];
}

/**
 * Cart utility functions for Frozen Foods
 */

/**
 * Add item to cart session
 * @param int $product_id
 * @param int $quantity
 * @return bool
 */

/**
 * Remove item from cart
 * @param int $product_id
 * @return bool
 */

function removeFromCart($product_id)
{
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        return true;
    }

    return false;
}

/**
 * Update cart item quantity
 * @param int $product_id
 * @param int $quantity
 * @return bool
 */
function updateCartQuantity($product_id, $quantity)
{
    if (isset($_SESSION['cart'][$product_id])) {
        if ($quantity <= 0) {
            return removeFromCart($product_id);
        }

        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        return true;
    }

    return false;
}

/**
 * Get cart contents
 * @return array
 */
function getCart()
{
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

/**
 * Get cart total
 * @return float
 */
function getCartTotal()
{
    $cart = getCart();
    $total = 0;

    foreach ($cart as $item) {
        $total += $item['product']['price'] * $item['quantity'];
    }

    return $total;
}

/**
 * Get cart item count
 * @return int
 */
function getCartItemCount()
{
    $cart = getCart();
    $count = 0;

    foreach ($cart as $item) {
        $count += $item['quantity'];
    }

    return $count;
}

/**
 * Clear cart
 * @return bool
 */
function clearCart()
{
    unset($_SESSION['cart']);
    return true;
}



/**
 * Calculate cart subtotal
 * @return float
 */
function getCartSubtotal()
{
    return getCartTotal();
}

/**
 * Calculate delivery fee
 * @param float $subtotal
 * @return float
 */
function calculateDeliveryFee($subtotal)
{
    // Free delivery for orders above ₦10,000
    if ($subtotal >= 10000) {
        return 0;
    }

    // Standard delivery fee
    return 500;
}

/**
 * Calculate total with delivery
 * @return array
 */
function getCartTotals()
{
    $subtotal = getCartSubtotal();
    $delivery = calculateDeliveryFee($subtotal);
    $total = $subtotal + $delivery;

    return [
        'subtotal' => $subtotal,
        'delivery' => $delivery,
        'total' => $total
    ];
}

/**
 * Create a new order
 * @param array $order_data
 * @return array|false
 */
function createOrder($order_data)
{
    // Validate required fields
    $required_fields = ['customer_name', 'customer_phone', 'delivery_address', 'items'];

    foreach ($required_fields as $field) {
        if (!isset($order_data[$field]) || empty($order_data[$field])) {
            return false;
        }
    }

    // Generate order ID
    $order_id = generateOrderId();

    // Calculate totals
    $totals = calculateOrderTotals($order_data['items']);

    // Create order array
    $order = [
        'id' => $order_id,
        'customer_name' => sanitizeInput($order_data['customer_name']),
        'customer_phone' => sanitizeInput($order_data['customer_phone']),
        'customer_email' => isset($order_data['customer_email']) ? sanitizeInput($order_data['customer_email']) : '',
        'delivery_address' => sanitizeInput($order_data['delivery_address']),
        'items' => $order_data['items'],
        'subtotal' => $totals['subtotal'],
        'delivery_fee' => $totals['delivery_fee'],
        'total' => $totals['total'],
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // In a real application, save to database
    // For now, we'll save to a JSON file or session
    saveOrder($order);

    return $order;
}

/**
 * Generate unique order ID
 * @return string
 */
function generateOrderId()
{
    return 'FF' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Calculate order totals
 * @param array $items
 * @return array
 */
function calculateOrderTotals($items)
{
    $subtotal = 0;

    foreach ($items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    $delivery_fee = calculateDeliveryFee($subtotal);
    $total = $subtotal + $delivery_fee;

    return [
        'subtotal' => $subtotal,
        'delivery_fee' => $delivery_fee,
        'total' => $total
    ];
}

/**
 * Save order (in real app, this would save to database)
 * @param array $order
 * @return bool
 */
function saveOrder($order)
{
    // For demonstration, we'll save to session
    if (!isset($_SESSION['orders'])) {
        $_SESSION['orders'] = [];
    }

    $_SESSION['orders'][$order['id']] = $order;

    return true;
}

/**
 * Get order by ID
 * @param string $order_id
 * @return array|null
 */
function getOrderById($order_id)
{
    if (isset($_SESSION['orders'][$order_id])) {
        return $_SESSION['orders'][$order_id];
    }

    return null;
}

/**
 * Get all orders for current session
 * @return array
 */
function getAllOrders()
{
    return isset($_SESSION['orders']) ? $_SESSION['orders'] : [];
}

/**
 * Update order status
 * @param string $order_id
 * @param string $status
 * @return bool
 */
function updateOrderStatus($order_id, $status)
{
    $valid_statuses = ['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled'];

    if (!in_array($status, $valid_statuses)) {
        return false;
    }

    if (isset($_SESSION['orders'][$order_id])) {
        $_SESSION['orders'][$order_id]['status'] = $status;
        $_SESSION['orders'][$order_id]['updated_at'] = date('Y-m-d H:i:s');
        return true;
    }

    return false;
}

/**
 * Get order status display text
 * @param string $status
 * @return string
 */
function getOrderStatusText($status)
{
    $status_texts = [
        'pending' => 'Order Pending',
        'confirmed' => 'Order Confirmed',
        'preparing' => 'Preparing Order',
        'out_for_delivery' => 'Out for Delivery',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    ];

    return isset($status_texts[$status]) ? $status_texts[$status] : 'Unknown Status';
}

/**
 * Get order status color class
 * @param string $status
 * @return string
 */
function getOrderStatusColor($status)
{
    $status_colors = [
        'pending' => 'text-yellow-600 bg-yellow-100',
        'confirmed' => 'text-blue-600 bg-blue-100',
        'preparing' => 'text-orange-600 bg-orange-100',
        'out_for_delivery' => 'text-purple-600 bg-purple-100',
        'delivered' => 'text-green-600 bg-green-100',
        'cancelled' => 'text-red-600 bg-red-100'
    ];

    return isset($status_colors[$status]) ? $status_colors[$status] : 'text-gray-600 bg-gray-100';
}

/**
 * Sanitize input data
 * @param string $input
 * @return string
 */
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate phone number (Nigerian format)
 * @param string $phone
 * @return bool
 */
function validatePhoneNumber($phone)
{
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Check if it's a valid Nigerian phone number
    if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
        return true;
    }

    if (strlen($phone) === 13 && substr($phone, 0, 3) === '234') {
        return true;
    }

    return false;
}

/**
 * Format phone number for display
 * @param string $phone
 * @return string
 */
function formatPhoneNumber($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);

    if (strlen($phone) === 11) {
        return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
    }

    return $phone;
}


function getAllProducts($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT id, name, slug, description, price, image, category_id FROM products");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            // Fetch category name for each product
            $catStmt = $pdo->prepare("SELECT name FROM categories WHERE id = :cat_id");
            $catStmt->bindParam(':cat_id', $product['category_id'], PDO::PARAM_INT);
            $catStmt->execute();
            $cat = $catStmt->fetch(PDO::FETCH_ASSOC);
            $product['category'] = $cat ? $cat['name'] : '';

            // Simple image fallback for development
            if (empty($product['image_url'])) {
                $product['image_url'] = "https://source.unsplash.com/400x300/?food," . urlencode($product['category']);
            }
        }
        return $products;
    } catch (PDOException $th) {
        error_log("Some error occured" . $th->getMessage());
        return [];
    }
}

function getProductImage($product)
{
    return !empty($product['image_url'])
        ? $product['image_url']
        : "https://source.unsplash.com/400x300/?food," . urlencode($product['category'] ?? 'frozen');
}

function getProductCategories($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT name FROM categories");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $categories;
    } catch (PDOException $th) {
        error_log("Some error occured" . $th->getMessage());
        return [];
    }
}

/**
 * Get product by ID
 * @param PDO $pdo
 * @param int $id
 * @return array|null
 */
function getProductById($pdo, $id)
{
    try {
        // Fetch product details
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        // Fetch category name
        $catStmt = $pdo->prepare("SELECT name FROM categories WHERE id = :cat_id");
        $catStmt->bindParam(':cat_id', $product['category_id'], PDO::PARAM_INT);
        $catStmt->execute();
        $cat = $catStmt->fetch(PDO::FETCH_ASSOC);
        $product['category'] = $cat ? $cat['name'] : '';

        // Optionally, fetch more details (e.g. nutritional info, features) if you have those tables

        return $product;
    } catch (PDOException $e) {
        error_log("Error fetching product by ID: " . $e->getMessage());
        return null;
    }
}
