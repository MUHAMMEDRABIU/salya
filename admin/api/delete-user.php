<?php
require __DIR__ . '/../initialize.php';
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

// Check if admin is authenticated
if (!isset($_SESSION['admin_id'])) {
    sendResponse(false, 'Admin authentication required');
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method');
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['user_id']) || empty($input['user_id'])) {
        sendResponse(false, 'User ID is required');
    }

    $user_id = (int)$input['user_id'];

    // Validate user ID
    if ($user_id <= 0) {
        sendResponse(false, 'Invalid user ID');
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, avatar, role, first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        sendResponse(false, 'User not found');
    }

    // Optional: Prevent deletion of certain user roles
    if ($user['role'] === 'admin' || $user['role'] === 'super_admin') {
        sendResponse(false, 'Cannot delete admin users through this endpoint');
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Log the deletion action
        $admin_id = $_SESSION['admin_id'];
        $user_name = $user['first_name'] . ' ' . $user['last_name'];
        error_log("Admin {$admin_id} is deleting user {$user_id} ({$user_name})");

        // Instead of deleting orders, anonymize them for record keeping
        $stmt = $pdo->prepare("UPDATE orders SET user_id = NULL WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete user's cart items
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete user's addresses
        $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete user's reviews
        $stmt = $pdo->prepare("DELETE FROM product_reviews WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete user's wishlist items
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete user's notifications
        $stmt = $pdo->prepare("DELETE FROM user_notifications WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete user's wallet transactions
        $stmt = $pdo->prepare("DELETE FROM wallet_transactions WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $result = $stmt->execute([$user_id]);

        if (!$result || $stmt->rowCount() === 0) {
            throw new Exception('Failed to delete user from database');
        }

        // Commit transaction
        $pdo->commit();

        // Delete user's avatar file if it exists and is not the default
        if ($user['avatar'] && $user['avatar'] !== DEFAULT_USER_AVATAR) {
            $avatar_path = USER_AVATAR_DIR . $user['avatar'];
            if (file_exists($avatar_path)) {
                if (!unlink($avatar_path)) {
                    error_log("Failed to delete avatar file: {$avatar_path}");
                }
            }
        }

        sendResponse(true, 'User deleted successfully', [
            'user_id' => $user_id,
            'user_name' => $user_name,
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollback();
        error_log("Transaction rollback in delete-user.php: " . $e->getMessage());
        throw $e;
    }
} catch (PDOException $e) {
    error_log("Database error in delete-user.php: " . $e->getMessage());
    sendResponse(false, 'Database error occurred');
} catch (Exception $e) {
    error_log("General error in delete-user.php: " . $e->getMessage());
    sendResponse(false, 'An unexpected error occurred: ' . $e->getMessage());
}
