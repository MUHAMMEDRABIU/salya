<?php
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
// ALLOWED FILE TYPES (as constants)
// =============================================================================

// Image MIME types
define('ALLOWED_IMAGE_JPEG', 'image/jpeg');
define('ALLOWED_IMAGE_JPG', 'image/jpg');
define('ALLOWED_IMAGE_PNG', 'image/png');
define('ALLOWED_IMAGE_GIF', 'image/gif');
define('ALLOWED_IMAGE_WEBP', 'image/webp');

// Image extensions
define('EXT_JPG', 'jpg');
define('EXT_JPEG', 'jpeg');
define('EXT_PNG', 'png');
define('EXT_GIF', 'gif');
define('EXT_WEBP', 'webp');

// Document MIME types
define('ALLOWED_DOC_PDF', 'application/pdf');
define('ALLOWED_DOC_WORD', 'application/msword');
define('ALLOWED_DOC_WORDX', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

// Document extensions
define('EXT_PDF', 'pdf');
define('EXT_DOC', 'doc');
define('EXT_DOCX', 'docx');

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

// Phone number validation patterns
define('PHONE_REGEX', '/^[\+]?[0-9\-\(\)\s]{10,15}$/');
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
// SIZE FORMATTING CONSTANTS
// =============================================================================

define('SIZE_BYTE', 1);
define('SIZE_KB', 1024);
define('SIZE_MB', 1048576);
define('SIZE_GB', 1073741824);

// =============================================================================
// INITIALIZE DIRECTORIES (Simple approach)
// =============================================================================

// Create upload directories if they don't exist (simple approach)
if (!is_dir(USER_AVATAR_DIR)) mkdir(USER_AVATAR_DIR, 0755, true);
if (!is_dir(ADMIN_AVATAR_DIR)) mkdir(ADMIN_AVATAR_DIR, 0755, true);
if (!is_dir(PRODUCT_IMAGE_DIR)) mkdir(PRODUCT_IMAGE_DIR, 0755, true);
if (!is_dir(PRODUCT_GALLERY_DIR)) mkdir(PRODUCT_GALLERY_DIR, 0755, true);
if (!is_dir(PRODUCT_THUMBNAIL_DIR)) mkdir(PRODUCT_THUMBNAIL_DIR, 0755, true);
if (!is_dir(CATEGORY_IMAGE_DIR)) mkdir(CATEGORY_IMAGE_DIR, 0755, true);
if (!is_dir(ORDER_RECEIPT_DIR)) mkdir(ORDER_RECEIPT_DIR, 0755, true);
if (!is_dir(ORDER_ATTACHMENT_DIR)) mkdir(ORDER_ATTACHMENT_DIR, 0755, true);
if (!is_dir(BANNER_IMAGE_DIR)) mkdir(BANNER_IMAGE_DIR, 0755, true);
if (!is_dir(TEMP_UPLOAD_DIR)) mkdir(TEMP_UPLOAD_DIR, 0755, true);

// Set timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set(APP_TIMEZONE);
}