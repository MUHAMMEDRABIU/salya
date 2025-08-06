<?php

// =============================================================================
// SALYA FROZEN FOODS - APPLICATION CONSTANTS
// =============================================================================

// Application Information
define('APP_NAME', 'Salya Frozen Foods');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/salya');
define('APP_TIMEZONE', 'Africa/Lagos');

// =============================================================================
// FILE UPLOAD CONFIGURATION
// =============================================================================

// Base upload directory
define('BASE_UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('BASE_UPLOAD_URL', APP_URL . '/assets/uploads/');

// Upload directories
define('USER_AVATAR_DIR', BASE_UPLOAD_DIR . 'avatars/users/');
define('ADMIN_AVATAR_DIR', BASE_UPLOAD_DIR . 'avatars/admins/');
define('PRODUCT_IMAGE_DIR', BASE_UPLOAD_DIR . 'products/main/');
define('PRODUCT_GALLERY_DIR', BASE_UPLOAD_DIR . 'products/gallery/');
define('PRODUCT_THUMBNAIL_DIR', BASE_UPLOAD_DIR . 'products/thumbnails/');
define('CATEGORY_IMAGE_DIR', BASE_UPLOAD_DIR . 'categories/');
define('ORDER_RECEIPT_DIR', BASE_UPLOAD_DIR . 'orders/receipts/');
define('ORDER_ATTACHMENT_DIR', BASE_UPLOAD_DIR . 'orders/attachments/');
define('BANNER_IMAGE_DIR', BASE_UPLOAD_DIR . 'banners/');
define('TEMP_UPLOAD_DIR', BASE_UPLOAD_DIR . 'temp/');

// Upload URLs (for web access)
define('USER_AVATAR_URL', BASE_UPLOAD_URL . 'avatars/users/');
define('ADMIN_AVATAR_URL', BASE_UPLOAD_URL . 'avatars/admins/');
define('PRODUCT_IMAGE_URL', BASE_UPLOAD_URL . 'products/main/');
define('PRODUCT_GALLERY_URL', BASE_UPLOAD_URL . 'products/gallery/');
define('PRODUCT_THUMBNAIL_URL', BASE_UPLOAD_URL . 'products/thumbnails/');
define('CATEGORY_IMAGE_URL', BASE_UPLOAD_URL . 'categories/');
define('ORDER_RECEIPT_URL', BASE_UPLOAD_URL . 'orders/receipts/');
define('BANNER_IMAGE_URL', BASE_UPLOAD_URL . 'banners/');

// File size limits (in bytes)
define('MAX_AVATAR_SIZE', 2 * 1024 * 1024);      // 2MB
define('MAX_PRODUCT_IMAGE_SIZE', 10 * 1024 * 1024); // 10MB
define('MAX_DOCUMENT_SIZE', 5 * 1024 * 1024);    // 5MB
define('MAX_BANNER_SIZE', 15 * 1024 * 1024);     // 15MB

// Default files
define('DEFAULT_USER_AVATAR', 'default.png');
define('DEFAULT_ADMIN_AVATAR', 'default.png');
define('DEFAULT_PRODUCT_IMAGE', 'default-product.png');
define('DEFAULT_CATEGORY_IMAGE', 'default-category.png');

// =============================================================================
// ALLOWED FILE TYPES
// =============================================================================

// Image file types
$ALLOWED_IMAGE_TYPES = [
    'image/jpeg',
    'image/jpg', 
    'image/png',
    'image/gif',
    'image/webp'
];

// Image extensions
$ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Document file types
$ALLOWED_DOCUMENT_TYPES = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

// Document extensions
$ALLOWED_DOCUMENT_EXTENSIONS = ['pdf', 'doc', 'docx'];

// =============================================================================
// UPLOAD DIRECTORY MAPPING
// =============================================================================

$UPLOAD_DIRECTORIES = [
    'user_avatars' => USER_AVATAR_DIR,
    'admin_avatars' => ADMIN_AVATAR_DIR,
    'products' => PRODUCT_IMAGE_DIR,
    'product_gallery' => PRODUCT_GALLERY_DIR,
    'product_thumbnails' => PRODUCT_THUMBNAIL_DIR,
    'categories' => CATEGORY_IMAGE_DIR,
    'order_receipts' => ORDER_RECEIPT_DIR,
    'order_attachments' => ORDER_ATTACHMENT_DIR,
    'banners' => BANNER_IMAGE_DIR,
    'temp' => TEMP_UPLOAD_DIR
];

$UPLOAD_URLS = [
    'user_avatars' => USER_AVATAR_URL,
    'admin_avatars' => ADMIN_AVATAR_URL,
    'products' => PRODUCT_IMAGE_URL,
    'product_gallery' => PRODUCT_GALLERY_URL,
    'product_thumbnails' => PRODUCT_THUMBNAIL_URL,
    'categories' => CATEGORY_IMAGE_URL,
    'order_receipts' => ORDER_RECEIPT_URL,
    'banners' => BANNER_IMAGE_URL
];

// =============================================================================
// USER & ADMIN CONSTANTS
// =============================================================================

// User roles
define('USER_ROLE_REGULAR', 'regular');
define('USER_ROLE_LOYAL', 'loyal');

// User statuses
define('USER_STATUS_ACTIVE', 'Active');
define('USER_STATUS_INACTIVE', 'Inactive');
define('USER_STATUS_SUSPENDED', 'Suspended');
define('USER_STATUS_PENDING', 'Pending');

// User verification statuses
define('USER_VERIFIED', 'verified');
define('USER_UNVERIFIED', 'unverified');

// Admin roles
define('ADMIN_ROLE_SUPER', 'Super Admin');
define('ADMIN_ROLE_ADMIN', 'Admin');
define('ADMIN_ROLE_MANAGER', 'Manager');
define('ADMIN_ROLE_STAFF', 'Staff');

// =============================================================================
// ORDER CONSTANTS
// =============================================================================

// Order statuses
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_PROCESSING', 'processing');
define('ORDER_STATUS_SHIPPED', 'shipped');
define('ORDER_STATUS_DELIVERED', 'delivered');
define('ORDER_STATUS_CANCELLED', 'cancelled');
define('ORDER_STATUS_REFUNDED', 'refunded');

// Payment statuses
define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_PAID', 'paid');
define('PAYMENT_STATUS_FAILED', 'failed');
define('PAYMENT_STATUS_REFUNDED', 'refunded');

// Payment methods
define('PAYMENT_METHOD_CARD', 'card');
define('PAYMENT_METHOD_TRANSFER', 'bank_transfer');
define('PAYMENT_METHOD_WALLET', 'wallet');
define('PAYMENT_METHOD_MONNIFY', 'monnify');

// =============================================================================
// WALLET CONSTANTS
// =============================================================================

// Wallet transaction types
define('WALLET_TRANSACTION_CREDIT', 'credit');
define('WALLET_TRANSACTION_DEBIT', 'debit');

// Wallet transaction statuses
define('WALLET_STATUS_PENDING', 'pending');
define('WALLET_STATUS_COMPLETED', 'completed');
define('WALLET_STATUS_FAILED', 'failed');
define('WALLET_STATUS_CANCELLED', 'cancelled');

// Currency
define('DEFAULT_CURRENCY', 'NGN');
define('CURRENCY_SYMBOL', '₦');

// =============================================================================
// PRODUCT CONSTANTS
// =============================================================================

// Product statuses
define('PRODUCT_STATUS_ACTIVE', 1);
define('PRODUCT_STATUS_INACTIVE', 0);

// Stock statuses
define('PRODUCT_IN_STOCK', 1);
define('PRODUCT_OUT_OF_STOCK', 0);

// Featured status
define('PRODUCT_FEATURED', 1);
define('PRODUCT_NOT_FEATURED', 0);

// =============================================================================
// NOTIFICATION CONSTANTS
// =============================================================================

// Notification types
define('NOTIFICATION_TYPE_INFO', 'info');
define('NOTIFICATION_TYPE_SUCCESS', 'success');
define('NOTIFICATION_TYPE_WARNING', 'warning');
define('NOTIFICATION_TYPE_ERROR', 'error');

// Feedback types
define('FEEDBACK_TYPE_BUG', 'bug');
define('FEEDBACK_TYPE_SUGGESTION', 'suggestion');
define('FEEDBACK_TYPE_COMPLAINT', 'complaint');
define('FEEDBACK_TYPE_PRAISE', 'praise');
define('FEEDBACK_TYPE_OTHER', 'other');

// Feedback statuses
define('FEEDBACK_STATUS_OPEN', 'open');
define('FEEDBACK_STATUS_IN_PROGRESS', 'in_progress');
define('FEEDBACK_STATUS_RESOLVED', 'resolved');
define('FEEDBACK_STATUS_CLOSED', 'closed');

// =============================================================================
// PAGINATION CONSTANTS
// =============================================================================

define('ITEMS_PER_PAGE_DEFAULT', 20);
define('ITEMS_PER_PAGE_ADMIN', 25);
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 15);
define('USERS_PER_PAGE', 20);

// =============================================================================
// VALIDATION CONSTANTS
// =============================================================================

// Password requirements
define('MIN_PASSWORD_LENGTH', 6);
define('MAX_PASSWORD_LENGTH', 255);

// Name requirements
define('MIN_NAME_LENGTH', 2);
define('MAX_NAME_LENGTH', 100);

// Phone number validation
define('PHONE_REGEX', '/^[\+]?[0-9\-\(\)\s]{10,15}$/');

// Nigerian phone number validation
define('NIGERIAN_PHONE_REGEX', '/^(\+234|234|0)(70|80|81|90|91|80|81|70)[0-9]{8}$/');

// =============================================================================
// SESSION CONSTANTS
// =============================================================================

define('SESSION_USER_KEY', 'user_id');
define('SESSION_ADMIN_KEY', 'admin_id');
define('SESSION_CART_KEY', 'cart_items');
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// =============================================================================
// SECURITY CONSTANTS
// =============================================================================

define('CSRF_TOKEN_NAME', 'csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 300); // 5 minutes in seconds

// =============================================================================
// EMAIL CONSTANTS
// =============================================================================

define('SUPPORT_EMAIL', 'support@salya.com');
define('ADMIN_EMAIL', 'admin@salya.com');
define('NOREPLY_EMAIL', 'noreply@salya.com');

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Get upload directory path by type
 */
function getUploadDir($type) {
    global $UPLOAD_DIRECTORIES;
    return $UPLOAD_DIRECTORIES[$type] ?? TEMP_UPLOAD_DIR;
}

/**
 * Get upload URL by type
 */
function getUploadUrl($type) {
    global $UPLOAD_URLS;
    return $UPLOAD_URLS[$type] ?? BASE_UPLOAD_URL . 'temp/';
}

/**
 * Ensure directory exists
 */
function ensureDirectoryExists($path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
    return $path;
}

/**
 * Check if file type is allowed
 */
function isAllowedImageType($mimeType) {
    global $ALLOWED_IMAGE_TYPES;
    return in_array($mimeType, $ALLOWED_IMAGE_TYPES);
}

/**
 * Check if document type is allowed
 */
function isAllowedDocumentType($mimeType) {
    global $ALLOWED_DOCUMENT_TYPES;
    return in_array($mimeType, $ALLOWED_DOCUMENT_TYPES);
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Generate unique filename
 */
function generateUniqueFilename($prefix, $extension) {
    return $prefix . '_' . uniqid('', true) . '_' . time() . '.' . $extension;
}

// =============================================================================
// INITIALIZE UPLOAD DIRECTORIES
// =============================================================================

// Create upload directories if they don't exist
foreach ($UPLOAD_DIRECTORIES as $dir) {
    ensureDirectoryExists($dir);
}

// Set timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set(APP_TIMEZONE);
}