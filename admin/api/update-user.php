<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../util/utilities.php';
require_once __DIR__ . '/../../config/constants.php';

// Set JSON response header
header('Content-Type: application/json');

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
    $role = trim($_POST['role'] ?? USER_ROLE_REGULAR);
    $status = trim($_POST['status'] ?? USER_STATUS_ACTIVE);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, 'Invalid email format');
    }

    // Validate phone number if provided
    if (!empty($phone) && !preg_match(PHONE_REGEX, $phone)) {
        sendResponse(false, 'Invalid phone number format');
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, email, avatar FROM users WHERE id = ?");
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
    $avatar_filename = $existing_user['avatar']; // Keep existing avatar by default
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = USER_AVATAR_DIR;

        // Create upload directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file = $_FILES['avatar'];
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];
        $file_type = $file['type'];

        // Validate file size using constant
        if ($file_size > MAX_AVATAR_SIZE) {
            sendResponse(false, 'Avatar file size too large. Maximum size is ' . number_format(MAX_AVATAR_SIZE / (1024 * 1024), 0) . 'MB.');
        }

        // Validate file type using constants
        $allowed_types = [
            ALLOWED_IMAGE_JPEG,
            ALLOWED_IMAGE_PNG,
            ALLOWED_IMAGE_GIF,
            ALLOWED_IMAGE_WEBP
        ];
        if (!in_array($file_type, $allowed_types)) {
            sendResponse(false, 'Invalid avatar file type. Only JPG, PNG, GIF, and WebP are allowed.');
        }

        // Generate unique filename
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $avatar_filename = 'user_' . $user_id . '_' . uniqid('', true) . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $avatar_filename;

        // Move uploaded file
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            sendResponse(false, 'Failed to upload avatar image');
        }

        // Delete old avatar if it exists and is not the default
        if ($existing_user['avatar'] && 
            $existing_user['avatar'] !== DEFAULT_USER_AVATAR && 
            file_exists(USER_AVATAR_DIR . $existing_user['avatar'])) {
            unlink(USER_AVATAR_DIR . $existing_user['avatar']);
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
    if ($avatar_filename !== $existing_user['avatar']) {
        $update_fields[] = 'avatar = ?';
        $update_values[] = $avatar_filename;
    }

    $update_values[] = $user_id; // For WHERE clause

    $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute($update_values)) {
        sendResponse(true, 'User updated successfully', [
            'user_id' => $user_id,
            'avatar' => $avatar_filename,
            'avatar_url' => $avatar_filename ? USER_AVATAR_URL . $avatar_filename : USER_AVATAR_URL . DEFAULT_USER_AVATAR
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
?>