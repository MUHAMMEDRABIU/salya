<?php
header('Content-Type: application/json');

require __DIR__ . '/initialize.php';
require __DIR__ . '/../util/utilities.php';

// Enable error reporting internally
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep display off for production

function logError($message, $data = [])
{
    $log = "[ADD PRODUCT ERROR] $message\n";
    if (!empty($data)) {
        $log .= print_r($data, true);
    }
    error_log($log);
}

function jsonError($message, $data = [])
{
    logError($message, $data);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit;
}

function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Invalid request method');
}

try {
    $errors = [];

    // Sanitize & validate required fields
    $name = $_POST['name'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $in_stock = $_POST['in_stock'] ?? '';

    if (empty(trim($name))) $errors[] = 'Product name is required';
    if (empty(trim($category_id))) $errors[] = 'Category is required';
    if (empty(trim($price))) $errors[] = 'Price is required';
    if (empty(trim($in_stock))) $errors[] = 'Stock quantity is required';

    if (!is_numeric($price) || $price < 0) $errors[] = 'Price must be a valid positive number';
    if (!filter_var($in_stock, FILTER_VALIDATE_INT) || $in_stock < 0) $errors[] = 'Stock must be a valid positive integer';

    // Validate image if provided
    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error occurred';
        } elseif ($file['size'] > 10 * 1024 * 1024) {
            $errors[] = 'File size too large. Max: 10MB';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mime, $allowed)) {
                $errors[] = 'Invalid file type. Only JPG, PNG, GIF, and WebP allowed';
            }
        }
    }

    // Check category exists
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    if (!$stmt->fetch()) {
        $errors[] = 'Invalid category selected';
    }

    // Return errors
    if (!empty($errors)) {
        jsonError(implode(', ', $errors), $_POST);
    }

    // Sanitize optional fields
    $sku = sanitize($_POST['sku'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $weight = sanitize($_POST['weight'] ?? '');
    $dimensions = sanitize($_POST['dimensions'] ?? '');
    $meta_title = sanitize($_POST['meta_title'] ?? '');
    $meta_description = sanitize($_POST['meta_description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Handle image
    $imageName = null;
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('product_', true) . '.' . $ext;
        $uploadPath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            jsonError('Failed to upload image', $_FILES);
        }
    } else {
        // If no image provided, send AJAX response
        if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            jsonError('Image upload error', $_FILES);
        }
    }

    // Insert into DB
    $sql = "INSERT INTO products (
        name, sku, description, category_id, price, in_stock, weight,
        dimensions, image, is_active, is_featured, meta_title, meta_description, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        sanitize($name),
        $sku,
        $description,
        $category_id,
        $price,
        $in_stock,
        $weight,
        $dimensions,
        $imageName,
        $is_active,
        $is_featured,
        $meta_title,
        $meta_description
    ]);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Product added successfully',
            'product_id' => $pdo->lastInsertId()
        ]);
    } else {
        jsonError('Failed to insert product into database');
    }
} catch (Exception $e) {
    jsonError('An unexpected error occurred: ' . $e->getMessage(), [
        'exception' => $e->getTraceAsString(),
        'input' => $_POST
    ]);
}
