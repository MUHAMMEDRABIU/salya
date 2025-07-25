<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../util/utilities.php';

// Set JSON response header
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Function to send JSON response
function sendResponse($success, $message, $data = null)
{
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method');
}

try {
    // Validate required fields
    $required_fields = ['user_id', 'first_name', 'last_name', 'email'];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        sendResponse(false, 'Missing required fields: ' . implode(', ', $missing_fields));
    }

    // Sanitize and validate input
    $user_id = (int)$_POST['user_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $role = trim($_POST['role'] ?? 'customer');
    $status = trim($_POST['status'] ?? 'Active');

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, 'Invalid email format');
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $existing_user = $stmt->fetch();

    if (!$existing_user) {
        sendResponse(false, 'User not found');
    }

    // Check if email is already taken by another user
    if ($existing_user['email'] !== $email) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            sendResponse(false, 'Email address is already taken by another user');
        }
    }

    // Handle avatar upload
    $avatar_filename = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../assets/uploads/';

        // Create upload directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file = $_FILES['avatar'];
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];
        $file_type = $file['type'];

        // Validate file size (5MB max)
        if ($file_size > 5 * 1024 * 1024) {
            sendResponse(false, 'Avatar file size too large. Maximum size is 5MB.');
        }

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file_type, $allowed_types)) {
            sendResponse(false, 'Invalid avatar file type. Only JPG, PNG, GIF, and WebP are allowed.');
        }

        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $avatar_filename = 'avatar_' . $user_id . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $avatar_filename;

        // Move uploaded file
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            sendResponse(false, 'Failed to upload avatar image');
        }

        // Delete old avatar if it exists and is not the default
        $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $old_user = $stmt->fetch();
        if ($old_user && $old_user['avatar'] && $old_user['avatar'] !== 'default.png') {
            $old_avatar_path = $upload_dir . $old_user['avatar'];
            if (file_exists($old_avatar_path)) {
                unlink($old_avatar_path);
            }
        }
    }

    // Prepare update query
    $update_fields = [
        'first_name = ?',
        'last_name = ?',
        'email = ?',
        'phone = ?',
        'address = ?',
        'role = ?',
        'status = ?',
        'updated_at = NOW()'
    ];

    $update_values = [
        $first_name,
        $last_name,
        $email,
        $phone,
        $address,
        $role,
        $status,
    ];

    // Add avatar to update if uploaded
    if ($avatar_filename) {
        $update_fields[] = 'avatar = ?';
        $update_values[] = $avatar_filename;
    }

    $update_values[] = $user_id; // For WHERE clause

    $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute($update_values)) {
        sendResponse(true, 'User updated successfully', [
            'user_id' => $user_id,
            'avatar' => $avatar_filename
        ]);
    } else {
        sendResponse(false, 'Failed to update user in database');
    }
} catch (PDOException $e) {
    error_log("Database error in update-user.php: " . $e->getMessage());
    sendResponse(false, 'Database error occurred');
} catch (Exception $e) {
    error_log("General error in update-user.php: " . $e->getMessage());
    sendResponse(false, 'An unexpected error occurred');
}
