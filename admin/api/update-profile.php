<?php
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../util/utilities.php';

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (
    empty($input['admin_id']) ||
    empty($input['first_name']) ||
    empty($input['last_name']) ||
    empty($input['email'])
) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$adminId = (int)$input['admin_id'];
$firstName = trim($input['first_name']);
$lastName = trim($input['last_name']);
$email = trim($input['email']);
$phone = isset($input['phone']) ? trim($input['phone']) : null;
$role = isset($input['role']) ? trim($input['role']) : null;
$address = isset($input['address']) ? trim($input['address']) : null;

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "UPDATE admins SET 
            first_name = :first_name,
            last_name = :last_name,
            email = :email,
            phone = :phone,
            role = :role,
            address = :address
        WHERE id = :admin_id"
    );
    $stmt->execute([
        ':first_name' => $firstName,
        ':last_name'  => $lastName,
        ':email'      => $email,
        ':phone'      => $phone,
        ':role'       => $role,
        ':address'   => $address,
        ':admin_id'   => $adminId
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
} catch (PDOException $e) {
    error_log("Admin profile update error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update profile'
    ]);
}
exit;