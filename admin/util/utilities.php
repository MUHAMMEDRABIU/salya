<?php

/**
 * Get virtual account details for a user
 * Returns array with keys: account_number, account_name, currency_code (with fallback if missing)
 */
function getUserVirtualAccount($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT account_number, account_name, currency_code FROM virtual_accounts WHERE user_id = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$user_id]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    return [
        'account_number' => $account['account_number'] ?? 'Unavailable',
        'account_name' => $account['account_name'] ?? 'Unavailable',
        'currency_code' => $account['currency_code'] ?? 'NGN',
    ];
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Fetch admin dashboard statistics: total orders, total revenue, active users, total products
 * @param PDO $pdo
 * @return array
 */
function getDashboardStats($pdo)
{
    try {
        // Total orders
        $stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM orders");
        $orders = $stmt->fetch(PDO::FETCH_ASSOC);

        // Total revenue
        $stmt = $pdo->query("SELECT SUM(total_amount) AS total_revenue FROM orders");
        $revenue = $stmt->fetch(PDO::FETCH_ASSOC);

        // Active users (users who have placed at least 1 order)
        $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) AS active_users FROM orders");
        $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC);

        // Total products
        $stmt = $pdo->query("SELECT COUNT(*) AS total_products FROM products");
        $products = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_orders'   => $orders['total_orders'] ?? 0,
            'total_revenue'  => $revenue['total_revenue'] ?? 0,
            'active_users'   => $activeUsers['active_users'] ?? 0,
            'total_products' => $products['total_products'] ?? 0,
        ];
    } catch (PDOException $e) {
        error_log("Error fetching dashboard stats: " . $e->getMessage());
        return [
            'total_orders'   => 0,
            'total_revenue'  => 0,
            'active_users'   => 0,
            'total_products' => $products['total_products'] ?? 0,
        ];
    } catch (PDOException $e) {
        error_log("Error fetching dashboard stats: " . $e->getMessage());
        return [
            'total_orders'   => 0,
            'total_revenue'  => 0,
            'active_users'   => 0,
            'total_products' => 0,
        ];
    }
}

function getAdminProfile($pdo, $admin_id)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            header("Location: logout.php");
            exit;
        }

        return $admin;
    } catch (PDOException $th) {
        error_log("[ADMIN FETCH ERRO]: Couldn't fetch admin data");
        return [];
    }
}


/**
 * Get unread notification count for a user
 * @param PDO $pdo
 * @param int $admin_id
 * @return int
 */
function getUnreadNotificationCount($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_notifications");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Error getting admin unread notification count: " . $e->getMessage());
        return 0;
    }
}
function getRecentOrders($pdo, $limit = 5)
{
    try {
        $limit = (int)$limit;
        $stmt = $pdo->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT $limit");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching recent orders: " . $e->getMessage());
        return [];
    }
}

function getTopProducts($pdo, $limit = 3)
{
    try {
        $limit = (int)$limit;
        if ($limit <= 0) {
            return [];
        }
        // Get all products
        $stmt = $pdo->prepare("SELECT id, name, image FROM products ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // For each product, get orders_count and total_revenue
        foreach ($products as &$product) {
            $orderStmt = $pdo->prepare("SELECT COUNT(*) AS orders_count, SUM(quantity * unit_price) AS total_revenue 
                                        FROM order_items 
                                        WHERE product_id = ?
                                        ");
            $orderStmt->execute([$product['id']]);
            $orderStats = $orderStmt->fetch(PDO::FETCH_ASSOC);
            $product['orders_count'] = (int)($orderStats['orders_count'] ?? 0);
            $product['total_revenue'] = (float)($orderStats['total_revenue'] ?? 0);
        }
        unset($product);

        return $products;
    } catch (PDOException $e) {
        error_log("Error fetching top products: " . $e->getMessage());
        return [];
    }
}
function getUsersData(PDO $pdo): array
{
    try {
        // Fetch all users
        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($users)) return [];

        // Extract user IDs
        $userIds = array_column($users, 'id');
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));

        // Fetch orders per user with total amount
        $orderStmt = $pdo->prepare("
            SELECT user_id, COUNT(*) AS order_count, SUM(total_amount) AS total_spent
            FROM orders
            WHERE user_id IN ($placeholders)
            GROUP BY user_id
        ");
        $orderStmt->execute($userIds);
        $orderData = [];
        foreach ($orderStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $orderData[$row['user_id']] = $row;
        }

        // Attach order stats to each user
        foreach ($users as &$user) {
            $uid = $user['id'];
            $user['order_count'] = $orderData[$uid]['order_count'] ?? 0;
            $user['total_spent'] = $orderData[$uid]['total_spent'] ?? 0;
        }

        return $users;
    } catch (PDOException $e) {
        error_log("[USERS FETCH ERROR] " . $e->getMessage());
        return [];
    }
}

// Get all users stats i.e total users, active users, new this month, premium users
function getAllUsersStats(PDO $pdo): array
{
    try {
        // Total users
        $stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
        $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'] ?? 0;

        // Active users (users who have placed at least 1 order)
        $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) AS active_users FROM orders");
        $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'] ?? 0;

        // New users this month
        $stmt = $pdo->query("SELECT COUNT(*) AS new_users FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
        $newUsersThisMonth = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'] ?? 0;

        // Premium users (assuming premium users have a specific role)
        $stmt = $pdo->query("SELECT COUNT(*) AS loyal_users FROM users WHERE role = 'loyal'");
        $premiumUsers = $stmt->fetch(PDO::FETCH_ASSOC)['loyal_users'] ?? 0;

        return [
            'total_users' => (int)$totalUsers,
            'active_users' => (int)$activeUsers,
            'new_users_this_month' => (int)$newUsersThisMonth,
            'loyal_users' => (int)$premiumUsers,
        ];
    } catch (PDOException $e) {
        error_log("[USERS STATS ERROR] " . $e->getMessage());
        return [
            'total_users' => 0,
            'active_users' => 0,
            'new_users_this_month' => 0,
            'loyal_users' => 0,
        ];
    }
}


/**
 * Fetch total orders count
 * @param PDO $pdo
 * @return int
 */
function getTotalOrders($pdo)
{
    try {
        $stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM orders");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total_orders'] ?? 0);
    } catch (PDOException $e) {
        error_log("Error fetching total orders: " . $e->getMessage());
        return 0;
    }
}

/**
 * Fetch total revenue
 * @param PDO $pdo
 * @return float
 */
function getTotalRevenue($pdo)
{
    try {
        $stmt = $pdo->query("SELECT SUM(total_amount) AS total_revenue FROM orders");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($row['total_revenue'] ?? 0);
    } catch (PDOException $e) {
        error_log("Error fetching total revenue: " . $e->getMessage());
        return 0;
    }
}

/**
 * Fetch active users (users who have placed at least 1 order)
 * @param PDO $pdo
 * @return int
 */
function getActiveUsers($pdo)
{
    try {
        $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) AS active_users FROM orders");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['active_users'] ?? 0);
    } catch (PDOException $e) {
        error_log("Error fetching active users: " . $e->getMessage());
        return 0;
    }
}

/**
 * Fetch total products count
 * @param PDO $pdo
 * @return int
 */
function getTotalProducts($pdo)
{
    try {
        $stmt = $pdo->query("SELECT COUNT(*) AS total_products FROM products");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total_products'] ?? 0);
    } catch (PDOException $e) {
        error_log("Error fetching total products: " . $e->getMessage());
        return 0;
    }
}

// Function that retrieves all orders table columns
function getAllOrders($pdo)
{
    try {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching orders: " . $e->getMessage());
        return [];
    }
}

function getOrderStatusCount($pdo, $status)
{
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM orders WHERE status = ?");
        $stmt->execute([$status]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['count'] ?? 0);
    } catch (PDOException $e) {
        error_log("Error fetching order status count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get order statistics
 * @param PDO $pdo
 * @param int $order_id
 * @return array
 */
function getOrderStatistics($pdo, $order_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_items,
                SUM(quantity) as total_quantity,
                COUNT(DISTINCT product_id) as unique_products,
                GROUP_CONCAT(DISTINCT p.category) as categories
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        error_log("Error fetching order statistics: " . $e->getMessage());
        return [];
    }
}

// function for getting user by id
function getUserById($pdo, $userId)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching user by ID: " . $e->getMessage());
        return null;
    }
}

function getProductById($pdo, $productId)
{
    try {
        $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE p.id = ?
        LIMIT 1
    ");
        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching product by ID: " . $e->getMessage());
        return null;
    }
}

function getOrderByNumber($pdo, $orderNumber)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
        $stmt->execute([$orderNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching order by ID: " . $e->getMessage());
        return null;
    }
}

function getOrderItems($pdo, $orderId)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching order items: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all products or filter by category
 */
function getAllProducts(PDO $pdo, $categoryId = null)
{
    try {
        if ($categoryId) {
            $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.category_id = ? ORDER BY p.created_at DESC");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p 
                                 LEFT JOIN categories c ON p.category_id = c.id 
                                 ORDER BY p.created_at DESC");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching products: " . $e->getMessage());
        return [];
    }
}

/**
 * Count total number of products
 */
function getTotalProductCount(PDO $pdo)
{
    try {
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM products");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    } catch (PDOException $e) {
        error_log("Error fetching total product count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Count products with stock < threshold
 */
function getLowStockCount(PDO $pdo, $threshold = 10)
{
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS in_stock FROM products WHERE stock_quantity < ?");
        $stmt->execute([$threshold]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['in_stock'];
    } catch (PDOException $e) {
        error_log("Error fetching low stock count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Count products with 0 stock
 */
function getOutOfStockCount(PDO $pdo)
{
    try {
        $stmt = $pdo->query("SELECT COUNT(*) AS out_of_stock FROM products WHERE stock_quantity = 0");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['out_of_stock'];
    } catch (PDOException $e) {
        error_log("Error fetching out-of-stock count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Count total number of categories
 */
function getCategoryCount(PDO $pdo)
{
    try {
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM categories");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    } catch (PDOException $e) {
        error_log("Error fetching category count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Combine product stats for dashboard
 */
function getProductStats(PDO $pdo)
{
    return [
        'total'       => getTotalProductCount($pdo),
        'in_stock'   => getLowStockCount($pdo),
        'out_of_stock' => getOutOfStockCount($pdo),
        'categories'  => getCategoryCount($pdo),
    ];
}

/**
 * Fetch all categories (used in dropdowns)
 */
function getAllCategories(PDO $pdo)
{
    try {
        $stmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Product Utility Functions
 * Contains all database operations and helper functions for product management from Bolt AI
 */

/**
 * Get product images (additional images beyond main image)
 */
function getProductImages($pdo, $productId)
{
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM products 
            WHERE id = ? 
            ORDER BY created_at ASC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching product images: " . $e->getMessage());
        return [];
    }
}

/**
 * Get recent orders for a specific product
 */
function getRecentOrdersForProduct(PDO $pdo, int $productId, int $limit = 5): array
{
    if ($productId <= 0) return [];

    $limit = max(1, (int)$limit);

    $sql = "
        SELECT
            o.id AS order_id,
            o.order_number,
            COALESCE(o.status, 'pending') AS status,
            COALESCE(
                o.total_amount,
                SUM(oi.quantity * oi.unit_price)
            ) AS total_amount,
            o.created_at,
            COALESCE(NULLIF(TRIM(CONCAT(u.first_name, ' ', u.last_name)), ''), 'Guest') AS customer_name
        FROM order_items oi
        INNER JOIN orders o ON oi.order_id = o.id
        LEFT JOIN users u ON o.user_id = u.id
        WHERE oi.product_id = :pid
        GROUP BY
            o.id, o.order_number, o.status, o.total_amount, o.created_at, u.first_name, u.last_name
        ORDER BY o.created_at DESC
        LIMIT :lim
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * Get product statistics
 */

/**
 * Update product information
 */
function updateProduct($pdo, $productId, $data)
{
    try {
        $sql = "UPDATE products SET ";
        $fields = [];
        $values = [];

        // Build dynamic update query based on provided data
        $allowedFields = [
            'name',
            'description',
            'price',
            'in_stock',
            'category_id',
            'sku',
            'weight',
            'dimensions',
            'is_active',
            'is_featured',
            'meta_title',
            'meta_description',
            'image'
        ];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql .= implode(', ', $fields);
        $sql .= ", updated_at = NOW() WHERE id = ?";
        $values[] = $productId;

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($values);
    } catch (PDOException $e) {
        error_log("Error updating product: " . $e->getMessage());
        return false;
    }
}

/**
 * Create new product
 */
function createProduct($pdo, $data)
{
    try {
        $sql = "INSERT INTO products (
            name, description, price, in_stock, category_id, 
            sku, weight, dimensions, is_active, is_featured,
            meta_title, meta_description, image, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['price'],
            $data['in_stock'] ?? 0,
            $data['category_id'] ?? null,
            $data['sku'] ?? null,
            $data['weight'] ?? null,
            $data['dimensions'] ?? null,
            $data['is_active'] ?? 1,
            $data['is_featured'] ?? 0,
            $data['meta_title'] ?? null,
            $data['meta_description'] ?? null,
            $data['image'] ?? null
        ]);

        if ($result) {
            return $pdo->lastInsertId();
        }
        return false;
    } catch (PDOException $e) {
        error_log("Error creating product: " . $e->getMessage());
        return false;
    }
}


/**
 * Search products
 */
function searchProducts($pdo, $searchTerm, $categoryId = null, $limit = 20, $offset = 0)
{
    try {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)
        ";

        $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];

        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error searching products: " . $e->getMessage());
        return [];
    }
}

/**
 * Get products by category
 */
function getProductsByCategory($pdo, $categoryId, $limit = 20, $offset = 0)
{
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = ?
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$categoryId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching products by category: " . $e->getMessage());
        return [];
    }
}

/**
 * Get low stock products
 */
function getLowStockProducts($pdo, $threshold = 10)
{
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.in_stock < ? AND p.in_stock > 0
            ORDER BY p.in_stock ASC
        ");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching low stock products: " . $e->getMessage());
        return [];
    }
}

/**
 * Get out of stock products
 */
function getOutOfStockProducts($pdo)
{
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.in_stock <= 0
            ORDER BY p.updated_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching out of stock products: " . $e->getMessage());
        return [];
    }
}

/**
 * Update product stock
 */
function updateProductStock($pdo, $productId, $newStock)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE products 
            SET in_stock = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$newStock, $productId]);
    } catch (PDOException $e) {
        error_log("Error updating product stock: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete product image
 */
function deleteProductImage($pdo, $imageId)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM product_images WHERE id = ?");
        return $stmt->execute([$imageId]);
    } catch (PDOException $e) {
        error_log("Error deleting product image: " . $e->getMessage());
        return false;
    }
}

/**
 * Get featured products
 */
function getFeaturedProducts($pdo, $limit = 10)
{
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_featured = 1 AND p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching featured products: " . $e->getMessage());
        return [];
    }
}

/**
 * Toggle product status (active/inactive)
 */
function toggleProductStatus($pdo, $productId)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE products 
            SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END,
                updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$productId]);
    } catch (PDOException $e) {
        error_log("Error toggling product status: " . $e->getMessage());
        return false;
    }
}

/**
 * Toggle product featured status
 */
function toggleProductFeatured($pdo, $productId)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE products 
            SET is_featured = CASE WHEN is_featured = 1 THEN 0 ELSE 1 END,
                updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$productId]);
    } catch (PDOException $e) {
        error_log("Error toggling product featured status: " . $e->getMessage());
        return false;
    }
}

/**
 * Get product count by status
 */
function getProductCountByStatus($pdo, $status = 'active')
{
    try {
        $sql = "SELECT COUNT(*) as count FROM products WHERE ";

        switch ($status) {
            case 'active':
                $sql .= "is_active = 1";
                break;
            case 'inactive':
                $sql .= "is_active = 0";
                break;
            case 'featured':
                $sql .= "is_featured = 1";
                break;
            case 'low_stock':
                $sql .= "in_stock < 10 AND in_stock > 0";
                break;
            case 'out_of_stock':
                $sql .= "in_stock <= 0";
                break;
            default:
                $sql .= "1 = 1"; // All products
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (PDOException $e) {
        error_log("Error getting product count by status: " . $e->getMessage());
        return 0;
    }
}

/**
 * Handle file upload for product images
 */
function handleProductImageUpload($file, $uploadDir = '../assets/uploads/')
{
    try {
        // Validate file
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'message' => 'No file uploaded'];
        }

        // Check file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File size too large (max 10MB)'];
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed'];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('product_') . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
        } else {
            return ['success' => false, 'message' => 'Failed to move uploaded file'];
        }
    } catch (Exception $e) {
        error_log("Error handling product image upload: " . $e->getMessage());
        return ['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()];
    }
}

/**
 * Analytics utility functions
 * These functions can be used to fetch analytics
 */


function getAnalyticsData(PDO $pdo): array
{
    $analytics = [
        'total_revenue' => 0,
        'total_orders' => 0,
        'active_users' => 0,
        'avg_order_value' => 0,
        'top_products' => [],
        'peak_times' => []
    ];

    try {
        // Total revenue
        $stmt = $pdo->query("SELECT SUM(total_amount) AS total FROM orders");
        $analytics['total_revenue'] = (float) $stmt->fetchColumn();

        // Total orders
        $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
        $analytics['total_orders'] = (int) $stmt->fetchColumn();

        // Active users (last 30 days)
        $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM orders WHERE created_at >= NOW() - INTERVAL 30 DAY");
        $analytics['active_users'] = (int) $stmt->fetchColumn();

        // Average order value
        $analytics['avg_order_value'] = $analytics['total_orders'] > 0
            ? round($analytics['total_revenue'] / $analytics['total_orders'], 2)
            : 0;

        // Top selling products
        $stmt = $pdo->query("SELECT p.name, p.image, SUM(oi.quantity) AS total_sold, SUM(oi.quantity * oi.unit_price) AS total_amount
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            GROUP BY oi.product_id
            ORDER BY total_sold DESC
            LIMIT 4");
        $analytics['top_products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Peak hours
        $stmt = $pdo->query("SELECT HOUR(created_at) AS hr, COUNT(*) AS cnt FROM orders GROUP BY hr ORDER BY cnt DESC LIMIT 4");
        $peaks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($peaks as $row) {
            $hour = (int) $row['hr'];
            $end = ($hour + 2) % 24;
            $label = sprintf("%02d:00 - %02d:00", $hour, $end);
            $tag = match (true) {
                $hour >= 10 && $hour < 12 => 'Morning Orders',
                $hour >= 12 && $hour < 14 => 'Lunch Rush',
                $hour >= 18 && $hour < 20 => 'Dinner Time',
                $hour >= 20 && $hour < 22 => 'Evening Orders',
                default => 'Other'
            };
            $percent = $analytics['total_orders'] > 0 ? round(($row['cnt'] / $analytics['total_orders']) * 100) : 0;

            $analytics['peak_times'][] = [
                'label' => $label,
                'tag' => $tag,
                'count' => (int) $row['cnt'],
                'percentage' => $percent
            ];
        }
    } catch (PDOException $e) {
        error_log("[ANALYTICS ERROR] " . $e->getMessage());
    }

    return $analytics;
}

// Function for getting user orders 
function getUserOrders(PDO $pdo, int $userId, int $limit = 5): array
{
    try {
        // Order count
        $stmt = $pdo->prepare("SELECT COUNT(*) AS order_count FROM orders WHERE user_id = ?");
        $stmt->execute([$userId]);
        $total_count = $stmt->fetch();

        // Total spent
        $stmt = $pdo->prepare("SELECT SUM(total_amount) AS total_spent FROM orders WHERE user_id = ?");
        $stmt->execute([$userId]);
        $total_spent = $stmt->fetch();

        // Latest orders (limited)
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :userId ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'order_count' => (int) $total_count['order_count'],
            'total_spent' => (float) $total_spent['total_spent'],
            'orders' => $orders
        ];
    } catch (PDOException $e) {
        error_log("Error fetching user orders: " . $e->getMessage());
        return [];
    }
}

function getUserWallet(PDO $pdo, int $userId): array
{
    try {
        // Fetch user wallet balance
        $stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
        $stmt->execute([$userId]);
        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($wallet) {
            return [
                'user_id' => $userId,
                'balance' => (float) $wallet['balance']
            ];
        } else {
            return [
                'user_id' => $userId,
                'balance' => 0.0
            ];
        }
    } catch (PDOException $e) {
        error_log("Error fetching user wallet: " . $e->getMessage());
        return [
            'user_id' => $userId,
            'balance' => 0.0
        ];
    }
}

// function for getting admin activity log
function getAdminActivityLog(PDO $pdo, int $adminId, int $limit = 10): array
{
    try {
        $limit = (int)$limit; // Ensure it's an integer
        $stmt = $pdo->prepare("SELECT * FROM admin_activity_log WHERE admin_id = ? ORDER BY created_at DESC LIMIT $limit");
        $stmt->execute([$adminId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching admin activity log: " . $e->getMessage());
        return [];
    }
}

// Function for login admin activity
function logAdminActivity($pdo, $adminId, $action, $details = '')
{
    $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$adminId, $action, $details]);
}

/**
 * Get system overview statistics for the admin dashboard.
 * Returns an associative array with keys:
 * - total_users
 * - orders_today
 * - products_live
 * - revenue_today
 * - system_uptime
 * - pending_tasks
 */
function getSystemOverview(PDO $pdo): array
{
    try {
        $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()")->fetchColumn() ?: 0;
        $ordersToday = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn() ?: 0;
        // FIX: Correct query for active products
        $productsLive = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn() ?: 0;
        $revenueToday = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
        $revenueToday = $revenueToday !== null ? $revenueToday : 0;
        // You can replace this with a real uptime calculation if available
        $systemUptime = '99.9%';
        // Add pending tasks if you have a tasks table
        $pendingTasks = 0;

        return [
            'total_users'   => (int)$totalUsers,
            'orders_today'  => (int)$ordersToday,
            'products_live' => (int)$productsLive,
            'revenue_today' => (float)$revenueToday,
            'system_uptime' => $systemUptime,
            'pending_tasks' => (int)$pendingTasks,
        ];
    } catch (PDOException $e) {
        error_log("Error fetching system overview: " . $e->getMessage());
        return [
            'total_users'   => 0,
            'orders_today'  => 0,
            'products_live' => 0,
            'revenue_today' => 0.0,
            'system_uptime' => 'N/A',
            'pending_tasks' => 0,
        ];
    }
}
