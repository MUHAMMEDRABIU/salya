<?php
require_once __DIR__ . '/../initialize.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $file = $_FILES['avatar'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
    }

    // Validate file size
    if ($file['size'] > $maxSize) {
        throw new Exception('File size too large. Maximum size is 5MB.');
    }

    // Create upload directory if it doesn't exist
    $uploadDir = __DIR__ . '/../../assets/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    // Get current avatar to delete later
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $currentAvatar = $stmt->fetchColumn();

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to save uploaded file');
    }

    // Create thumbnail only if GD extension is available
    if (extension_loaded('gd')) {
        createThumbnail($targetPath, $uploadDir . 'thumb_' . $filename, 150, 150);
    } else {
        error_log('GD extension not available - skipping thumbnail creation');
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$filename, $user_id]);

    if (!$result) {
        // Delete uploaded file if database update fails
        unlink($targetPath);
        throw new Exception('Failed to update user avatar in database');
    }

    // Delete old avatar file
    if ($currentAvatar && $currentAvatar !== $filename) {
        $oldAvatarPath = $uploadDir . $currentAvatar;
        $oldThumbPath = $uploadDir . 'thumb_' . $currentAvatar;

        if (file_exists($oldAvatarPath)) {
            unlink($oldAvatarPath);
        }
        if (file_exists($oldThumbPath)) {
            unlink($oldThumbPath);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Avatar uploaded successfully',
        'avatar_url' => '../assets/uploads/' . $filename,
        'thumbnail_url' => extension_loaded('gd') ? '../assets/uploads/thumb_' . $filename : null
    ]);
} catch (Exception $e) {
    error_log('Avatar upload error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function createThumbnail($source, $destination, $width, $height)
{
    try {
        // Check if GD extension is loaded
        if (!extension_loaded('gd')) {
            throw new Exception('GD extension is not loaded');
        }

        $info = getimagesize($source);
        if (!$info) {
            throw new Exception('Invalid image file');
        }

        $mime = $info['mime'];

        // Create image resource based on type
        switch ($mime) {
            case 'image/jpeg':
                if (!function_exists('imagecreatefromjpeg')) {
                    throw new Exception('JPEG support not available in GD');
                }
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                if (!function_exists('imagecreatefrompng')) {
                    throw new Exception('PNG support not available in GD');
                }
                $image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                if (!function_exists('imagecreatefromgif')) {
                    throw new Exception('GIF support not available in GD');
                }
                $image = imagecreatefromgif($source);
                break;
            case 'image/webp':
                if (!function_exists('imagecreatefromwebp')) {
                    throw new Exception('WebP support not available in GD');
                }
                $image = imagecreatefromwebp($source);
                break;
            default:
                throw new Exception('Unsupported image type: ' . $mime);
        }

        if (!$image) {
            throw new Exception('Failed to create image resource');
        }

        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Calculate dimensions to maintain aspect ratio
        $ratio = min($width / $originalWidth, $height / $originalHeight);
        $newWidth = round($originalWidth * $ratio);
        $newHeight = round($originalHeight * $ratio);

        $thumbnail = imagecreatetruecolor($width, $height);

        if (!$thumbnail) {
            imagedestroy($image);
            throw new Exception('Failed to create thumbnail canvas');
        }

        // Handle transparency for PNG and GIF
        if ($mime == 'image/png' || $mime == 'image/gif') {
            $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
            imagecolortransparent($thumbnail, $transparent);
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            imagefill($thumbnail, 0, 0, $transparent);
        } else {
            // Fill background with white for other formats
            $backgroundColor = imagecolorallocate($thumbnail, 255, 255, 255);
            imagefill($thumbnail, 0, 0, $backgroundColor);
        }

        // Center the image
        $x = ($width - $newWidth) / 2;
        $y = ($height - $newHeight) / 2;

        // Resample the image
        $resampleResult = imagecopyresampled(
            $thumbnail,
            $image,
            $x,
            $y,
            0,
            0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        if (!$resampleResult) {
            imagedestroy($image);
            imagedestroy($thumbnail);
            throw new Exception('Failed to resample image');
        }

        // Save the thumbnail
        $saveResult = false;
        switch ($mime) {
            case 'image/jpeg':
                $saveResult = imagejpeg($thumbnail, $destination, 90);
                break;
            case 'image/png':
                $saveResult = imagepng($thumbnail, $destination, 9);
                break;
            case 'image/gif':
                $saveResult = imagegif($thumbnail, $destination);
                break;
            case 'image/webp':
                $saveResult = imagewebp($thumbnail, $destination, 90);
                break;
        }

        // Cleanup
        imagedestroy($image);
        imagedestroy($thumbnail);

        if (!$saveResult) {
            throw new Exception('Failed to save thumbnail');
        }

        return true;
    } catch (Exception $e) {
        error_log('Thumbnail creation error: ' . $e->getMessage());
        return false;
    }
}
