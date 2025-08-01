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

    // Check if Monnify credentials are configured BEFORE starting registration
    if (!isset($_ENV['MONNIFY_API_KEY']) || !isset($_ENV['MONNIFY_SECRET_KEY']) || !isset($_ENV['MONNIFY_CONTRACT_CODE'])) {
        error_log("Registration Failed: Monnify credentials not configured");
        echo json_encode(['success' => false, 'message' => 'Payment service is not configured. Registration is temporarily unavailable.']);
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

        error_log("Registration: User $userId created successfully");

        // Create user wallet - REQUIRED FOR REGISTRATION SUCCESS
        $walletCreated = createUserWallet($userId, $email, $phone, $pdo);

        if (!$walletCreated) {
            error_log("Registration Failed: Wallet creation failed for user $userId");
            throw new Exception('Failed to create user wallet. Registration cannot proceed.');
        }

        error_log("Registration: Wallet created successfully for user $userId");

        // Create virtual account - REQUIRED FOR REGISTRATION SUCCESS
        $apiKey = $_ENV['MONNIFY_API_KEY'];
        $secretKey = $_ENV['MONNIFY_SECRET_KEY'];
        $contractCode = $_ENV['MONNIFY_CONTRACT_CODE'];

        error_log("Registration: Attempting to create virtual account for user $userId");

        // Get Monnify token
        $token = getMonnifyToken($apiKey, $secretKey);

        if (!$token) {
            error_log("Registration Failed: Failed to get Monnify token for user $userId");
            throw new Exception('Failed to authenticate with payment service. Please try again later.');
        }

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

        if (!$virtualAccountData) {
            error_log("Registration Failed: Virtual account creation failed for user $userId");
            throw new Exception('Failed to create payment account. Please try again later.');
        }

        error_log("Registration Success: Virtual account created for user $userId - Account: " . $virtualAccountData['account_number']);

        // If we reach here, everything was successful
        $pdo->commit();

        // Success response
        $response = [
            'success' => true,
            'message' => 'Registration successful! Your account and payment wallet have been created.',
            'user_id' => $userId,
            'wallet_created' => true,
            'virtual_account_created' => true,
            'virtual_account' => [
                'account_number' => $virtualAccountData['account_number'],
                'account_name' => $virtualAccountData['account_name'],
                'bank_name' => $virtualAccountData['bank_name']
            ]
        ];

        error_log("Registration Complete: User $userId registered successfully with wallet and virtual account");

        echo json_encode($response);
    } catch (Exception $e) {
        // Rollback transaction on any error
        $pdo->rollBack();

        // Log the specific error
        error_log("Registration Transaction Failed for user email $email: " . $e->getMessage());

        // Return user-friendly error message
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit();
    }
} catch (PDOException $e) {
    error_log('Registration Database Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred. Please try again later.']);
} catch (Exception $e) {
    error_log('Registration General Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred during registration. Please try again.']);
}

/**
 * Create user wallet with initial balance of 0
 * 
 * @param int $userId User ID
 * @param string $email User email
 * @param string $phone User phone number
 * @param PDO $pdo Database connection
 * @return bool True if wallet created successfully, false otherwise
 */
function createUserWallet($userId, $email, $phone, $pdo)
{
    try {
        // Check if user already has a wallet
        $stmt = $pdo->prepare("SELECT id FROM wallets WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $existingWallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingWallet) {
            error_log("Wallet: User $userId already has a wallet with ID " . $existingWallet['id']);
            return false; // Wallet already exists, consider it failed
        }

        // Create new wallet with initial balance of 0
        $stmt = $pdo->prepare("
            INSERT INTO wallets (user_id, balance, email, phone, created_at, updated_at) 
            VALUES (?, 0.00, ?, ?, NOW(), NOW())
        ");

        $result = $stmt->execute([$userId, $email, $phone]);

        if ($result) {
            return true;
        } else {
            error_log("Wallet Creation Failed: Unable to create wallet for user $userId");
            return false;
        }
    } catch (PDOException $e) {
        error_log("Wallet Creation Error: " . $e->getMessage() . " for user $userId");
        return false;
    }
}
