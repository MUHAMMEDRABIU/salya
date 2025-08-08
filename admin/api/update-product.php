<?php
header('Content-Type: application/json');

require __DIR__ . '/../initialize.php';
require __DIR__ . '/../util/utilities.php';
require __DIR__ . '/../../config/constants.php';

function logError($message, $data = [])
{
    $log = "[UPDATE PRODUCT ERROR] $message\n";
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

    // Required fields
    $id = $_POST['product_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $in_stock = $_POST['in_stock'] ?? '';

    if (empty($id)) $errors[] = 'Product ID is required';
    if (empty(trim($name))) $errors[] = 'Product name is required';
    if (empty(trim($category_id))) $errors[] = 'Category is required';
    if (empty(trim($price))) $errors[] = 'Price is required';
    if (empty(trim($in_stock))) $errors[] = 'Stock quantity is required';

    if (!is_numeric($price) || $price < 0) $errors[] = 'Price must be a valid positive number';
    if (!filter_var($in_stock, FILTER_VALIDATE_INT) || $in_stock < 0) $errors[] = 'Stock must be a valid positive integer';

    // Validate category exists
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    if (!$stmt->fetch()) {
        $errors[] = 'Invalid category selected';
    }

    // Check product exists
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingProduct) {
        $errors[] = 'Product not found';
    }

    // Image validation if provided
    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error occurred';
        } elseif ($file['size'] > MAX_PRODUCT_IMAGE_SIZE) {
            $errors[] = 'File size too large. Max: ' . number_format(MAX_PRODUCT_IMAGE_SIZE / (1024 * 1024), 0) . 'MB';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowed = [
                ALLOWED_IMAGE_JPEG,
                ALLOWED_IMAGE_PNG,
                ALLOWED_IMAGE_GIF,
                ALLOWED_IMAGE_WEBP
            ];
            if (!in_array($mime, $allowed)) {
                $errors[] = 'Invalid file type. Only JPG, PNG, GIF, and WebP allowed';
            }
        }
    }

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

    // Default to existing image
    $imageName = $existingProduct['image'];

    // Handle image upload
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = PRODUCT_IMAGE_DIR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $imageName = 'product_' . uniqid('', true) . '_' . time() . '.' . $ext;
        $uploadPath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            jsonError('Failed to upload new image', $_FILES);
        }

        // Delete old image if it exists and is not a default image
        if ($existingProduct['image'] && 
            $existingProduct['image'] !== DEFAULT_PRODUCT_IMAGE && 
            file_exists(PRODUCT_IMAGE_DIR . $existingProduct['image'])) {
            unlink(PRODUCT_IMAGE_DIR . $existingProduct['image']);
        }
    }

    // Update the product in DB
    $sql = "UPDATE products SET 
        name = ?, sku = ?, description = ?, category_id = ?, price = ?, 
        in_stock = ?, weight = ?, dimensions = ?, image = ?, 
        is_active = ?, is_featured = ?, meta_title = ?, meta_description = ?, updated_at = NOW()
        WHERE id = ?";

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
        $meta_description,
        $id
    ]);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Product updated successfully',
            'product_id' => $id,
            'image_url' => $imageName ? PRODUCT_IMAGE_URL . $imageName : PRODUCT_IMAGE_URL . DEFAULT_PRODUCT_IMAGE
        ]);
    } else {
        jsonError('Failed to update product in database');
    }

} catch (Exception $e) {
    jsonError('An unexpected error occurred: ' . $e->getMessage(), [
        'exception' => $e->getTraceAsString(),
        'input' => $_POST
    ]);
}
?>