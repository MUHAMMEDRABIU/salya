<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../initialize.php';
require __DIR__ . '/../util/utilities.php';
require __DIR__ . '/../../config/constants.php';

function jsonResponse(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}
function slugify(string $str): string
{
    $str = strtolower(trim($str));
    $str = preg_replace('/[^\w\s-]/', '', $str);
    $str = preg_replace('/[\s_-]+/', '-', $str);
    return trim($str, '-');
}
function uniqueSlug(PDO $pdo, string $base): string
{
    $slug = $base ?: 'product';
    $try = $slug;
    $i = 1;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE slug = ?');
    while (true) {
        $stmt->execute([$try]);
        if ((int)$stmt->fetchColumn() === 0) return $try;
        $try = $slug . '-' . $i++;
        if ($i > 100) return $slug . '-' . uniqid();
    }
}
function isValidJson(string $s): bool
{
    json_decode($s);
    return json_last_error() === JSON_ERROR_NONE;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

try {
    // Required
    $name       = trim($_POST['name'] ?? '');
    $categoryId = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $price      = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
    $inStock    = filter_var($_POST['in_stock'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

    $errors = [];
    if ($name === '')                       $errors[] = 'Product name is required';
    if (!$categoryId)                        $errors[] = 'Category is required';
    if ($price === false || $price < 0)     $errors[] = 'Price must be a valid non-negative number';
    if ($inStock === false || $inStock < 0) $errors[] = 'Stock must be a valid non-negative integer';
    if ($errors) jsonResponse(400, ['success' => false, 'message' => implode(', ', $errors)]);

    // Ensure category exists
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE id = ?');
    $stmt->execute([$categoryId]);
    if (!$stmt->fetchColumn()) {
        jsonResponse(400, ['success' => false, 'message' => 'Invalid category selected']);
    }

    // Optionals
    $slugInput       = trim($_POST['slug'] ?? '');
    $description     = trim($_POST['description'] ?? '');
    $featuresRaw     = trim($_POST['features'] ?? '');
    $nutritionRaw    = trim($_POST['nutritional_info'] ?? '');
    $weightInput     = trim($_POST['weight'] ?? '');
    $dimensions      = trim($_POST['dimensions'] ?? '');
    $metaTitle       = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $isActive        = isset($_POST['is_active']) ? 1 : 1; // default active
    $isFeatured      = isset($_POST['is_featured']) ? 1 : 0;

    // Weight validation
    $weight = null;
    if ($weightInput !== '') {
        $w = filter_var($weightInput, FILTER_VALIDATE_FLOAT);
        if ($w === false || $w < 0) {
            jsonResponse(400, ['success' => false, 'message' => 'Weight must be a valid non-negative number']);
        }
        $weight = $w;
    }

    // Slug
    $baseSlug = $slugInput !== '' ? slugify($slugInput) : slugify($name);
    $slug     = uniqueSlug($pdo, $baseSlug);

    // Features -> JSON (array of strings). Default to [] to satisfy CHECK(json_valid(features))
    if ($featuresRaw !== '' && isValidJson($featuresRaw)) {
        $featuresJson = $featuresRaw;
    } else {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $featuresRaw)), fn($l) => $l !== ''));
        $featuresJson = json_encode($lines, JSON_UNESCAPED_UNICODE);
    }
    if ($featuresJson === '' || !isValidJson($featuresJson)) {
        $featuresJson = '[]';
    }

    // Nutritional info -> JSON. Try to parse JSON first; else parse key:value lines into an object.
    if ($nutritionRaw !== '' && isValidJson($nutritionRaw)) {
        $nutritionJson = $nutritionRaw;
    } else {
        $obj = [];
        foreach (preg_split('/\r\n|\r|\n/', $nutritionRaw) as $row) {
            $row = trim($row);
            if ($row === '') continue;
            // Accept "Key: Value" or "Key - Value"
            if (preg_match('/^\s*([^:-]+)\s*[:\-]\s*(.+)\s*$/u', $row, $m)) {
                $key = trim($m[1]);
                $val = trim($m[2]);
                if ($key !== '') $obj[$key] = $val;
            } else {
                // Fallback: push as array entry
                $obj[] = $row;
            }
        }
        // If empty, default to {} (valid JSON)
        $nutritionJson = json_encode(empty($obj) ? (object)[] : $obj, JSON_UNESCAPED_UNICODE);
    }
    if ($nutritionJson === '' || !isValidJson($nutritionJson)) {
        $nutritionJson = '{}';
    }

    // Image (optional)
    $imageName = "default-product.jpg";
    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(400, ['success' => false, 'message' => 'File upload error occurred']);
        }
        if ($file['size'] > MAX_PRODUCT_IMAGE_SIZE) {
            jsonResponse(400, ['success' => false, 'message' => 'File size too large. Max: ' . number_format(MAX_PRODUCT_IMAGE_SIZE / (1024 * 1024), 0) . 'MB']);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowed = [ALLOWED_IMAGE_JPEG, ALLOWED_IMAGE_PNG, ALLOWED_IMAGE_GIF, ALLOWED_IMAGE_WEBP];
        if (!in_array($mime, $allowed, true)) {
            jsonResponse(400, ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP allowed']);
        }

        if (!is_dir(PRODUCT_IMAGE_DIR)) {
            mkdir(PRODUCT_IMAGE_DIR, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $ext = match ($mime) {
                ALLOWED_IMAGE_JPEG => 'jpg',
                ALLOWED_IMAGE_PNG  => 'png',
                ALLOWED_IMAGE_GIF  => 'gif',
                ALLOWED_IMAGE_WEBP => 'webp',
                default            => 'jpg',
            };
        }

        $imageName = 'product_' . uniqid('', true) . '_' . time() . '.' . $ext;
        $destPath  = PRODUCT_IMAGE_DIR . $imageName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            jsonResponse(500, ['success' => false, 'message' => 'Failed to upload image']);
        }
    }

    // Derived/defaults
    $stockQuantity = $inStock;
    $rating = 4;
    $reviewsCount = 0;

    // Insert
    $sql = "INSERT INTO products (
                category_id, name, slug, description, price, image,
                rating, reviews_count, stock_quantity, in_stock,
                weight, dimensions, features, nutritional_info,
                is_active, is_featured, meta_title, meta_description,
                created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?,
                NOW(), NOW()
            )";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        $categoryId,
        $name,
        $slug,
        $description,
        $price,
        $imageName,
        $rating,
        $reviewsCount,
        $stockQuantity,
        $inStock,
        $weight,
        $dimensions,
        $featuresJson,
        $nutritionJson,
        $isActive,
        $isFeatured,
        $metaTitle,
        $metaDescription
    ]);

    if (!$ok) {
        if ($imageName && is_file(PRODUCT_IMAGE_DIR . $imageName)) @unlink(PRODUCT_IMAGE_DIR . $imageName);
        jsonResponse(500, ['success' => false, 'message' => 'Failed to insert product into database']);
    }

    jsonResponse(200, [
        'success'    => true,
        'message'    => 'Product added successfully',
        'product_id' => $pdo->lastInsertId(),
        'image_url'  => PRODUCT_IMAGE_URL . ($imageName ?: DEFAULT_PRODUCT_IMAGE),
        'slug'       => $slug
    ]);
} catch (Throwable $e) {
    error_log('[add-product.php] ' . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'An unexpected error occurred']);
}
