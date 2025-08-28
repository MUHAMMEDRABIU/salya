<?php
require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/monnify.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if JSON parsing was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data provided.']);
        exit();
    }

    $fullName = $data['fullName'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $password = $data['password'] ?? '';

    // Enhanced validation
    if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit();
    }

    // Validate password strength
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        exit();
    }

    // Validate phone number format (basic validation)
    if (!preg_match('/^[\+]?[0-9\-\(\)\s]{10,15}$/', $phone)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid phone number.']);
        exit();
    }

    // Validate full name (at least 2 characters, letters and spaces only)
    if (strlen(trim($fullName)) < 2 || !preg_match('/^[a-zA-Z\s]+$/', $fullName)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid full name (letters and spaces only).']);
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email is already registered. Please use a different email or login to your existing account.']);
        exit();
    }

    // Check if phone already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Phone number is already registered. Please use a different phone number.']);
        exit();
    }

    // Check if Monnify credentials are configured BEFORE starting registration
    if (!isset($_ENV['MONNIFY_API_KEY']) || !isset($_ENV['MONNIFY_SECRET_KEY']) || !isset($_ENV['MONNIFY_CONTRACT_CODE'])) {
        error_log("Registration Failed: Monnify credentials not configured");
        echo json_encode(['success' => false, 'message' => 'Payment service is not configured. Registration is temporarily unavailable.']);
        exit();
    }

    // Split fullName into first_name and last_name with improved logic
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

    // Validate name parts
    if (strlen($first_name) < 1) {
        echo json_encode(['success' => false, 'message' => 'First name is required.']);
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Start database transaction
    $pdo->beginTransaction();

    try {
        // Insert user into the database
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, `role`, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $result = $stmt->execute([$first_name, $last_name, $email, $phone, $hashedPassword, 'regular']);

        if (!$result) {
            throw new Exception('Failed to create user account. Please try again.');
        }

        $userId = $pdo->lastInsertId();

        if (!$userId) {
            throw new Exception('Failed to retrieve user ID after account creation.');
        }

        // Create user wallet - REQUIRED FOR REGISTRATION SUCCESS
        $walletResult = createUserWallet($userId, $email, $phone, $pdo);

        if (!$walletResult['success']) {
            $errorMessage = $walletResult['message'];
            $errorType = $walletResult['error_type'] ?? 'UNKNOWN';
            
            error_log("Registration Failed: Wallet creation failed for user $userId. Type: $errorType, Details: $errorMessage");
            
            // Handle different error types differently
            switch ($errorType) {
                case 'WALLET_EXISTS':
                    // If wallet exists, we can continue with registration
                    error_log("Registration Continuing: Using existing wallet for user $userId");
                    break;
                    
                case 'MISSING_PARAMETERS':
                    throw new Exception('Invalid user data provided for wallet creation. Please check your information and try again.');
                    
                case 'DATABASE_ERROR':
                    throw new Exception('Database error occurred while creating your wallet. Please try again later.');
                    
                default:
                    throw new Exception('Failed to create user wallet. Registration cannot proceed. Please try again later.');
            }
        }

        // Get Monnify credentials
        $apiKey = $_ENV['MONNIFY_API_KEY'];
        $secretKey = $_ENV['MONNIFY_SECRET_KEY'];
        $contractCode = $_ENV['MONNIFY_CONTRACT_CODE'];

        // Get Monnify token
        $token = getMonnifyToken($apiKey, $secretKey);

        if (!$token) {
            error_log("Registration Failed: Failed to get Monnify token for user $userId");
            throw new Exception('Failed to authenticate with payment service. Please try again later.');
        }

        // Create permanent virtual account - REQUIRED FOR REGISTRATION SUCCESS
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

        // If we reach here, everything was successful
        $pdo->commit();

        // Log successful registration
        error_log("Registration Success: User $userId registered successfully with email $email");

        // Success response
        $response = [
            'success' => true,
            'message' => 'Registration successful! Your account and payment wallet have been created.',
            'user_id' => $userId,
            'user_details' => [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone
            ],
            'wallet_created' => true,
            'virtual_account_created' => true,
            'virtual_account' => [
                'account_number' => $virtualAccountData['account_number'],
                'account_name' => $virtualAccountData['account_name'],
                'bank_name' => $virtualAccountData['bank_name']
            ]
        ];

        echo json_encode($response);
    } catch (Exception $e) {
        // Rollback transaction on any error
        $pdo->rollBack();

        // Log the specific error with more context
        error_log("Registration Transaction Failed for user email $email (Phone: $phone): " . $e->getMessage());

        // Return user-friendly error message
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => 'REGISTRATION_FAILED'
        ]);
        exit();
    }
} catch (PDOException $e) {
    // Rollback transaction if it was started
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log('Registration Database Error: ' . $e->getMessage() . ' | Code: ' . $e->getCode());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred. Please try again later.',
        'error_code' => 'DATABASE_ERROR'
    ]);
} catch (Exception $e) {
    error_log('Registration General Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An unexpected error occurred during registration. Please try again.',
        'error_code' => 'GENERAL_ERROR'
    ]);
}

/**
 * Create user wallet with initial balance of 0
 * 
 * @param int $userId User ID
 * @param string $email User email
 * @param string $phone User phone number
 * @param PDO $pdo Database connection
 * @return array Result array with success status, message, and data
 */
function createUserWallet($userId, $email, $phone, $pdo)
{
    try {
        // Check if user already has a wallet
        $stmt = $pdo->prepare("SELECT id, balance, created_at FROM wallets WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $existingWallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingWallet) {
            $walletCreatedDate = date('M j, Y g:i A', strtotime($existingWallet['created_at']));
            $message = "Wallet already exists for this user. " .
                      "Wallet ID: {$existingWallet['id']}, " .
                      "Current Balance: ₦" . number_format($existingWallet['balance'], 2) . ", " .
                      "Created: {$walletCreatedDate}";
            
            error_log("Wallet Creation Skipped: $message (User ID: $userId)");
            
            return [
                'success' => false,
                'message' => $message,
                'error_type' => 'WALLET_EXISTS',
                'existing_wallet' => [
                    'id' => $existingWallet['id'],
                    'balance' => $existingWallet['balance'],
                    'created_at' => $existingWallet['created_at']
                ]
            ];
        }

        // Validate required parameters
        if (empty($userId) || empty($email) || empty($phone)) {
            $message = "Missing required parameters for wallet creation. " .
                      "User ID: " . ($userId ?: 'missing') . ", " .
                      "Email: " . ($email ?: 'missing') . ", " .
                      "Phone: " . ($phone ?: 'missing');
            
            error_log("Wallet Creation Failed: $message");
            
            return [
                'success' => false,
                'message' => $message,
                'error_type' => 'MISSING_PARAMETERS'
            ];
        }

        // Create new wallet with initial balance of 0
        $stmt = $pdo->prepare("
            INSERT INTO wallets (user_id, balance, currency, is_active, created_at, updated_at) 
            VALUES (?, 0.00, 'NGN', 1, NOW(), NOW())
        ");

        $result = $stmt->execute([$userId]);

        if ($result) {
            $walletId = $pdo->lastInsertId();
            $message = "Wallet created successfully";
            
            return [
                'success' => true,
                'message' => $message,
                'wallet_data' => [
                    'id' => $walletId,
                    'user_id' => $userId,
                    'balance' => 0.00,
                    'currency' => 'NGN'
                ]
            ];
        } else {
            $errorInfo = $stmt->errorInfo();
            $message = "Failed to execute wallet creation query for user $userId. " .
                      "SQL Error: " . ($errorInfo[2] ?? 'Unknown error') . " " .
                      "(SQLSTATE: " . ($errorInfo[0] ?? 'Unknown') . ")";
            
            error_log("Wallet Creation Failed: $message");
            
            return [
                'success' => false,
                'message' => $message,
                'error_type' => 'QUERY_EXECUTION_FAILED',
                'sql_error' => $errorInfo
            ];
        }
    } catch (PDOException $e) {
        $message = "Database error during wallet creation for user $userId. " .
                  "Error: {$e->getMessage()}, " .
                  "Error Code: {$e->getCode()}, " .
                  "File: {$e->getFile()}:{$e->getLine()}";
        
        error_log("Wallet Creation Database Error: $message");
        
        return [
            'success' => false,
            'message' => $message,
            'error_type' => 'DATABASE_ERROR',
            'exception' => [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ];
    } catch (Exception $e) {
        $message = "Unexpected error during wallet creation for user $userId. " .
                  "Error: {$e->getMessage()}, " .
                  "File: {$e->getFile()}:{$e->getLine()}";
        
        error_log("Wallet Creation Unexpected Error: $message");
        
        return [
            'success' => false,
            'message' => $message,
            'error_type' => 'UNEXPECTED_ERROR',
            'exception' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ];
    }
}