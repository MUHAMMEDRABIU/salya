<?php
require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/monnify.php';

// Load environment variables if .env file exists
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value, '"\'');
        }
    }
}

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $fullName = $data['fullName'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $password = $data['password'] ?? '';

    // Validation
    if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit();
    }

    // Validate password length
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
        exit();
    }

    // Split fullName into first_name and last_name
    $nameParts = preg_split('/\s+/', trim($fullName));
    if (count($nameParts) === 1) {
        $first_name = $nameParts[0];
        $last_name = '';
    } elseif (count($nameParts) === 2) {
        $first_name = $nameParts[0];
        $last_name = $nameParts[1];
    } else {
        $first_name = $nameParts[0];
        $last_name = implode(' ', array_slice($nameParts, 1));
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Start database transaction
    $pdo->beginTransaction();

    try {
        // Insert user into the database
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$first_name, $last_name, $email, $phone, $hashedPassword]);
        $userId = $pdo->lastInsertId();

        // Initialize response
        $response = [
            'success' => true,
            'message' => 'Registration successful.',
            'user_id' => $userId,
            'virtual_account_created' => false
        ];

        // Try to create virtual account
        $virtualAccountData = null;
        if (isset($_ENV['MONNIFY_API_KEY']) && isset($_ENV['MONNIFY_SECRET_KEY']) && isset($_ENV['MONNIFY_CONTRACT_CODE'])) {
            try {
                error_log("Registration: Attempting to create virtual account for user $userId");

                $apiKey = $_ENV['MONNIFY_API_KEY'];
                $secretKey = $_ENV['MONNIFY_SECRET_KEY'];
                $contractCode = $_ENV['MONNIFY_CONTRACT_CODE'];

                // Get Monnify token
                $token = getMonnifyToken($apiKey, $secretKey);

                if ($token) {
                    error_log("Registration: Got Monnify token for user $userId");

                    // Create permanent virtual account
                    $virtualAccountData = createPermanentVirtualAccount(
                        $token,
                        $contractCode,
                        $userId,
                        $email,
                        $fullName,
                        $pdo
                    );

                    if ($virtualAccountData) {
                        $response['virtual_account_created'] = true;
                        $response['virtual_account'] = [
                            'account_number' => $virtualAccountData['account_number'],
                            'account_name' => $virtualAccountData['account_name'],
                            'bank_name' => $virtualAccountData['bank_name']
                        ];
                        error_log("Registration Success: Virtual account created for user $userId - Account: " . $virtualAccountData['account_number']);
                    } else {
                        error_log("Registration Warning: Virtual account creation failed for user $userId");
                        $response['virtual_account_error'] = 'Virtual account creation failed, but registration completed successfully.';
                    }
                } else {
                    error_log("Registration Warning: Failed to get Monnify token for user $userId");
                    $response['virtual_account_error'] = 'Failed to authenticate with payment service.';
                }
            } catch (Exception $e) {
                error_log("Registration Monnify Error for user $userId: " . $e->getMessage());
                $response['virtual_account_error'] = 'Payment service temporarily unavailable.';
            }
        } else {
            error_log("Registration: Monnify credentials not configured");
            $response['virtual_account_error'] = 'Payment service not configured.';
        }

        // Commit the transaction (user is created regardless of virtual account status)
        $pdo->commit();

        // Log successful registration
        error_log("Registration Complete: User $userId registered successfully. Virtual account: " .
            ($response['virtual_account_created'] ? 'Created' : 'Failed'));

        echo json_encode($response);
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }
} catch (PDOException $e) {
    error_log('Registration Database Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred while processing your request.']);
} catch (Exception $e) {
    error_log('Registration General Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred during registration.']);
}