<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../initialize.php';
require __DIR__ . '/../util/utilities.php';
require __DIR__ . '/../../config/constants.php';

function jsonResponse(int $statusCode, array $payload)
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function slugify(string $str): string
{
    $s = strtolower(trim($str));
    $s = preg_replace('/[^\w\s-]/', '', $s);
    $s = preg_replace('/[\s_-]+/', '-', $s);
    return trim($s, '-');
}

function uniqueSlugExclId(PDO $pdo, string $base, int $excludeId): string
{
    $slug = $base ?: 'product';
    $try  = $slug;
    $i    = 1;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE slug = ? AND id <> ?');
    while (true) {
        $stmt->execute([$try, $excludeId]);
        if ((int)$stmt->fetchColumn() === 0) return $try;
        $try = $slug . '-' . $i++;
        if ($i > 100) return $slug . '-' . uniqid();
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

try {
    // Validate and normalize inputs
    $id         = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $name       = trim((string)($_POST['name'] ?? ''));
    $categoryId = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $price      = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
    $inStock    = filter_var($_POST['in_stock'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

    $errors = [];
    if (!$id)                $errors[] = 'Product ID is required';
    if ($name === '')        $errors[] = 'Product name is required';
    if (!$categoryId)        $errors[] = 'Category is required';
    if ($price === false || $price < 0) $errors[] = 'Price must be a valid positive number';
    if ($inStock === false || $inStock < 0)  $errors[] = 'Stock must be a valid non-negative integer';

    if ($errors) {
        jsonResponse(400, ['success' => false, 'message' => implode(', ', $errors)]);
    }

    // Ensure category exists
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    if (!$stmt->fetchColumn()) {
        jsonResponse(400, ['success' => false, 'message' => 'Invalid category selected']);
    }

    // Fetch existing product (and current image)
    $stmt = $pdo->prepare("SELECT id, image, slug FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$existing) {
        jsonResponse(404, ['success' => false, 'message' => 'Product not found']);
    }
    $existingImage = $existing['image'] ?? null;

    // Optional fields
    $slugInput   = trim((string)($_POST['slug'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $weightIn    = trim((string)($_POST['weight'] ?? ''));
    $dimensions  = trim((string)($_POST['dimensions'] ?? ''));
    $metaTitle   = trim((string)($_POST['meta_title'] ?? ''));
    $metaDesc    = trim((string)($_POST['meta_description'] ?? ''));
    $isActive    = isset($_POST['is_active']) ? 1 : 0;
    $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;

    // Weight validation (optional)
    $weight = null;
    if ($weightIn !== '') {
        $w = filter_var($weightIn, FILTER_VALIDATE_FLOAT);
        if ($w === false || $w < 0) {
            jsonResponse(400, ['success' => false, 'message' => 'Weight must be a valid non-negative number']);
        }
        $weight = $w;
    }

    // Slug handling (replace SKU with slug)
    $baseSlug = $slugInput !== '' ? slugify($slugInput) : slugify($name);
    $slug     = uniqueSlugExclId($pdo, $baseSlug, (int)$id);

    // Handle image upload (if provided)
    $newImageName = null;
    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(400, ['success' => false, 'message' => 'File upload error occurred']);
        }
        if ($file['size'] > MAX_PRODUCT_IMAGE_SIZE) {
            jsonResponse(400, [
                'success' => false,
                'message' => 'File size too large. Max: ' . number_format(MAX_PRODUCT_IMAGE_SIZE / (1024 * 1024), 0) . 'MB'
            ]);
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

        $newImageName = 'product_' . uniqid('', true) . '_' . time() . '.' . $ext;
        $destPath     = PRODUCT_IMAGE_DIR . $newImageName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            jsonResponse(500, ['success' => false, 'message' => 'Failed to upload image']);
        }
        // Note: we delete old image only after DB update succeeds
    }

    // Update inside a transaction. If DB fails, remove newly uploaded image to avoid orphan files.
    $pdo->beginTransaction();

    $sql = "UPDATE products SET 
                name = ?, slug = ?, description = ?, category_id = ?, price = ?, 
                in_stock = ?, stock_quantity = ?, weight = ?, dimensions = ?, image = ?, 
                is_active = ?, is_featured = ?, meta_title = ?, meta_description = ?, updated_at = NOW()
            WHERE id = ?";

    $imageForDb = $newImageName ?: $existingImage;

    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        $name,
        $slug,
        $description,
        $categoryId,
        $price,
        $inStock,
        $inStock,     // mirror stock_quantity
        $weight,
        $dimensions,
        $imageForDb,
        $isActive,
        $isFeatured,
        $metaTitle,
        $metaDesc,
        $id
    ]);

    if (!$ok) {
        $pdo->rollBack();
        if ($newImageName && file_exists(PRODUCT_IMAGE_DIR . $newImageName)) {
            @unlink(PRODUCT_IMAGE_DIR . $newImageName);
        }
        jsonResponse(500, ['success' => false, 'message' => 'Failed to update product in database']);
    }

    $pdo->commit();

    // Delete old image after successful update (if replaced and old is not default)
    if ($newImageName && $existingImage && $existingImage !== DEFAULT_PRODUCT_IMAGE) {
        $oldPath = PRODUCT_IMAGE_DIR . $existingImage;
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    $finalImage = $imageForDb ?: DEFAULT_PRODUCT_IMAGE;

    jsonResponse(200, [
        'success'    => true,
        'message'    => 'Product updated successfully',
        'product_id' => $id,
        'image_url'  => PRODUCT_IMAGE_URL . $finalImage,
        'slug'       => $slug
    ]);
} catch (Throwable $e) {
    error_log('[update-product.php] ' . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'An unexpected error occurred']);
}
