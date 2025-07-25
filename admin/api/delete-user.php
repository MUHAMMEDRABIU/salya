<?php
require __DIR__ . '/initialize.php';
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
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['user_id']) || empty($input['user_id'])) {
        sendResponse(false, 'User ID is required');
    }

    $user_id = (int)$input['user_id'];

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, avatar, role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        sendResponse(false, 'User not found');
    }


    // Start transaction
    $pdo->beginTransaction();

    try {
        // Delete user's orders (or you might want to keep them for records)
        // $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = ?");
        // $stmt->execute([$user_id]);

        // Instead of deleting orders, you might want to anonymize them
        $stmt = $pdo->prepare("UPDATE orders SET user_id = NULL WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // // Delete user's cart items
        // $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        // $stmt->execute([$user_id]);

        // // Delete user's addresses
        // $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE user_id = ?");
        // $stmt->execute([$user_id]);

        // // Delete user's reviews
        // $stmt = $pdo->prepare("DELETE FROM product_reviews WHERE user_id = ?");
        // $stmt->execute([$user_id]);

        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        // Commit transaction
        $pdo->commit();

        // Delete user's avatar file if it exists and is not the default
        if ($user['avatar'] && $user['avatar'] !== 'default.png') {
            $avatar_path = __DIR__ . '/../../assets/uploads/' . $user['avatar'];
            if (file_exists($avatar_path)) {
                unlink($avatar_path);
            }
        }

        sendResponse(true, 'User deleted successfully', ['user_id' => $user_id]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollback();
        throw $e;
    }
} catch (PDOException $e) {
    error_log("Database error in delete-user.php: " . $e->getMessage());
    sendResponse(false, 'Database error occurred');
} catch (Exception $e) {
    error_log("General error in delete-user.php: " . $e->getMessage());
    sendResponse(false, 'An unexpected error occurred');
}
