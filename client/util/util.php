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
 * Get user's wallet balance
 * @param PDO $pdo
 * @param int $user_id
 * @return float
 */
function getUserWalletBalance($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($result['balance']) ? (float)$result['balance'] : 0.00;
    } catch (Exception $e) {
        error_log("Error getting wallet balance: " . $e->getMessage());
        return 0.00;
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

function getAllNotifications($pdo, $user_id = null)
{
    try {
        if ($user_id) {
            $stmt = $pdo->prepare("SELECT * FROM user_notifications WHERE user_id = :user_id ORDER BY `time` DESC");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $pdo->query("SELECT * FROM user_notifications ORDER BY `time` DESC");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching notifications: " . $e->getMessage());
        return [];
    }
}

/**
 * Get unread notification count for a user
 * @param PDO $pdo
 * @param int $user_id
 * @return int
 */
function getUnreadNotificationCount($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id = ? AND (`read` = 0 OR `read` IS NULL)");
        $stmt->execute([$user_id]);
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Error getting unread notification count: " . $e->getMessage());
        return 0;
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
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE is_active = '1'");
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

/**
 * Get user addresses
 * @param PDO $pdo
 * @param int $user_id
 * @return array
 */
function getUserAddresses($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT id, address_name, full_address, type, street_address, 
                   city, state, postal_code, landmark, is_default, 
                   created_at, updated_at
            FROM user_addresses 
            WHERE user_id = ? 
            ORDER BY is_default DESC, created_at DESC   
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching user addresses: " . $e->getMessage());
        return [];
    }
}

/**
 * Add new user address
 * @param PDO $pdo
 * @param int $user_id
 * @param array $address_data
 * @return bool
 */
function addUserAddress($pdo, $user_id, $address_data)
{
    try {
        // If this is set as default, unset other defaults first
        if (isset($address_data['is_default']) && $address_data['is_default']) {
            $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
            $stmt->execute([$user_id]);
        }

        $stmt = $pdo->prepare("
            INSERT INTO user_addresses 
            (user_id, address_name, full_address, type, street_address, city, state, postal_code, landmark, is_default) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $user_id,
            $address_data['address_name'] ?? 'Home',
            $address_data['full_address'],
            $address_data['type'] ?? 'home',
            $address_data['street_address'],
            $address_data['city'],
            $address_data['state'],
            $address_data['postal_code'] ?? null,
            $address_data['landmark'] ?? null,
            $address_data['is_default'] ?? 0
        ]);
    } catch (PDOException $e) {
        error_log("Error adding user address: " . $e->getMessage());
        return false;
    }
}

/**
 * Update user address
 * @param PDO $pdo
 * @param int $user_id
 * @param int $address_id
 * @param array $address_data
 * @return bool
 */
function updateUserAddress($pdo, $user_id, $address_id, $address_data)
{
    try {
        // If this is set as default, unset other defaults first
        if (isset($address_data['is_default']) && $address_data['is_default']) {
            $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ? AND id != ?");
            $stmt->execute([$user_id, $address_id]);
        }

        $stmt = $pdo->prepare("
            UPDATE user_addresses SET 
                address_name = ?, full_address = ?, type = ?, street_address = ?, 
                city = ?, state = ?, postal_code = ?, landmark = ?, is_default = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([
            $address_data['address_name'] ?? 'Home',
            $address_data['full_address'],
            $address_data['type'] ?? 'home',
            $address_data['street_address'],
            $address_data['city'],
            $address_data['state'],
            $address_data['postal_code'] ?? null,
            $address_data['landmark'] ?? null,
            $address_data['is_default'] ?? 0,
            $address_id,
            $user_id
        ]);
    } catch (PDOException $e) {
        error_log("Error updating user address: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete user address
 * @param PDO $pdo
 * @param int $user_id
 * @param int $address_id
 * @return bool
 */
function deleteUserAddress($pdo, $user_id, $address_id)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
        return $stmt->execute([$address_id, $user_id]);
    } catch (PDOException $e) {
        error_log("Error deleting user address: " . $e->getMessage());
        return false;
    }
}

/**
 * Set default address
 * @param PDO $pdo
 * @param int $user_id
 * @param int $address_id
 * @return bool
 */
function setDefaultAddress($pdo, $user_id, $address_id)
{
    try {
        // First, unset all defaults for this user
        $stmt1 = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
        $stmt1->execute([$user_id]);

        // Then set the new default
        $stmt2 = $pdo->prepare("UPDATE user_addresses SET is_default = 1 WHERE id = ? AND user_id = ?");
        return $stmt2->execute([$address_id, $user_id]);
    } catch (PDOException $e) {
        error_log("Error setting default address: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user preferences
 * @param PDO $pdo
 * @param int $user_id
 * @return array
 */

function getUserPreferences($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT push_notifications, email_updates, language, theme FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $prefs = $stmt->fetch(PDO::FETCH_ASSOC);

        // Always return a proper array with defaults
        return [
            'push_notifications' => (int)($prefs['push_notifications'] ?? 1),
            'email_updates' => (int)($prefs['email_updates'] ?? 0),
            'language' => $prefs['language'] ?? 'en',
            'theme' => $prefs['theme'] ?? 'light'
        ];
    } catch (PDOException $e) {
        error_log("Error fetching user preferences: " . $e->getMessage());
        return [
            'push_notifications' => 1,
            'email_updates' => 0,
            'language' => 'en',
            'theme' => 'light'
        ];
    }
}

/**
 * Update user preferences
 * @param PDO $pdo
 * @param int $user_id
 * @param array $preferences
 * @return bool
 */
function updateUserPreferences($pdo, $user_id, $preferences)
{
    try {
        // Check if preferences exist
        $stmt = $pdo->prepare("SELECT id FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Update existing preferences
            $stmt = $pdo->prepare("
                UPDATE user_preferences SET 
                    push_notifications = ?, email_updates = ?, language = ?, theme = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ?
            ");
            return $stmt->execute([
                $preferences['push_notifications'] ?? 1,
                $preferences['email_updates'] ?? 0,
                $preferences['language'] ?? 'en',
                $preferences['theme'] ?? 'light',
                $user_id
            ]);
        } else {
            // Insert new preferences
            $stmt = $pdo->prepare("
                INSERT INTO user_preferences 
                (user_id, push_notifications, email_updates, language, theme) 
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $user_id,
                $preferences['push_notifications'] ?? 1,
                $preferences['email_updates'] ?? 0,
                $preferences['language'] ?? 'en',
                $preferences['theme'] ?? 'light'
            ]);
        }
    } catch (PDOException $e) {
        error_log("Error updating user preferences: " . $e->getMessage());
        return false;
    }
}

/**
 * Get Nigerian states
 * @param PDO $pdo
 * @return array
 */
function getNigerianStates($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT id, state_name, state_code FROM nigerian_states ORDER BY state_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching states: " . $e->getMessage());
        return [];
    }
}

/**
 * Get cities by state
 * @param PDO $pdo
 * @param int $state_id
 * @return array
 */
function getCitiesByState($pdo, $state_id)
{
    try {
        $stmt = $pdo->prepare("SELECT id, city_name FROM nigerian_cities WHERE state_id = ? ORDER BY city_name");
        $stmt->execute([$state_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching cities: " . $e->getMessage());
        return [];
    }
}

/**
 * Get areas by city
 * @param PDO $pdo
 * @param int $city_id
 * @return array
 */
function getAreasByCity($pdo, $city_id)
{
    try {
        $stmt = $pdo->prepare("SELECT id, area_name FROM nigerian_areas WHERE city_id = ? ORDER BY area_name");
        $stmt->execute([$city_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching areas: " . $e->getMessage());
        return [];
    }
}

/**
 * Upload user avatar
 * @param int $user_id
 * @param array $file
 * @return array
 */
function uploadUserAvatar($pdo, $user_id, $file)
{
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Validate file
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size too large. Maximum 5MB allowed.'];
    }

    // Create upload directory if it doesn't exist
    $upload_dir = USER_AVATAR_DIR;
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        try {
            // Update user avatar in database
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$filename, $user_id]);

            return [
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'avatar_filename' => $filename
            ];
        } catch (PDOException $e) {
            // Delete uploaded file if database update fails
            unlink($filepath);
            error_log("Error updating user avatar: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update avatar in database'];
        }
    }

    return ['success' => false, 'message' => 'Failed to upload file'];
}

/**
 * Remove user avatar
 * @param PDO $pdo
 * @param int $user_id
 * @return array
 */
function removeUserAvatar($pdo, $user_id)
{
    try {
        // Get current avatar
        $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['avatar'] && $user['avatar'] !== DEFAULT_USER_AVATAR) {
            // Delete file if it exists
            $filepath = USER_AVATAR_DIR . $user['avatar'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        // Reset avatar to default in database
        $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->execute([DEFAULT_USER_AVATAR, $user_id]);

        return ['success' => true, 'message' => 'Avatar removed successfully'];
    } catch (PDOException $e) {
        error_log("Error removing user avatar: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to remove avatar'];
    }
}
