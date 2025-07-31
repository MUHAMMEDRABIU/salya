<?php

/**
 * Get Monnify authentication token
 * 
 * @param string $apiKey Monnify API Key
 * @param string $secretKey Monnify Secret Key
 * @return string|null Access token or null on failure
 */
function getMonnifyToken($apiKey, $secretKey)
{
    if (empty($apiKey) || empty($secretKey)) {
        error_log("Monnify Token Error: API Key or Secret Key is empty");
        return null;
    }

    $credentials = base64_encode("$apiKey:$secretKey");
    $url = "https://sandbox.monnify.com/api/v1/auth/login";

    // Prepare POST data (empty JSON object for login endpoint)
    $postData = json_encode(new stdClass());

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true); // Explicitly set POST method
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // Send empty JSON body
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $credentials",
        "Content-Type: application/json",
        "Accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For sandbox, set to true in production
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // For sandbox, set to 2 in production
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ShopEase-PHP/1.0');

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlInfo = curl_getinfo($ch);
    curl_close($ch);

    // Enhanced cURL error logging
    if ($curlError) {
        error_log("Monnify Token cURL Error: " . $curlError . " - URL: " . $url . " - HTTP Code: " . $httpCode);
        return null;
    }

    // Log HTTP errors with more context
    if ($httpCode !== 200) {
        error_log("Monnify Token HTTP Error: HTTP $httpCode - URL: $url - Request: POST with Basic Auth - Response: " . $response);

        // Additional debug info for 405 errors
        if ($httpCode === 405) {
            error_log("Monnify 405 Debug: Method not allowed. Check if endpoint requires POST. cURL Info: " . json_encode($curlInfo));
        }

        return null;
    }

    // Decode response
    $data = json_decode($response, true);

    // Log JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Monnify Token JSON Error: " . json_last_error_msg() . " - Raw Response: " . $response);
        return null;
    }

    // Check if request was successful
    if (!isset($data['requestSuccessful']) || $data['requestSuccessful'] !== true) {
        $errorMessage = $data['responseMessage'] ?? 'Unknown error';
        $errorCode = $data['responseCode'] ?? 'Unknown code';
        error_log("Monnify Token API Error: [$errorCode] $errorMessage - Full response: " . json_encode($data));
        return null;
    }

    // Check if token exists
    if (!isset($data['responseBody']['accessToken'])) {
        error_log("Monnify Token Missing: Access token not found in response - " . json_encode($data));
        return null;
    }

    $token = $data['responseBody']['accessToken'];
    $expiresIn = $data['responseBody']['expiresIn'] ?? 'unknown';
    error_log("Monnify Token Success: Token obtained successfully, expires in: $expiresIn seconds");

    return $token;
}

/**
 * Get user's virtual account from database
 * 
 * @param int $userId User ID
 * @param PDO $pdo Database connection
 * @return array|null Virtual account data or null if not found
 */
function getUserVirtualAccount($pdo, $userId)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM virtual_accounts WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($account) {
            error_log("Virtual Account Retrieved: User $userId has account " . $account['account_number']);
            return $account;
        } else {
            error_log("Virtual Account Not Found: User $userId has no virtual account");
            return null;
        }
    } catch (PDOException $e) {
        error_log("Virtual Account DB Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Create permanent virtual account for user and store in database
 * 
 * @param string $token Monnify access token
 * @param string $contractCode Monnify contract code
 * @param int $userId User ID from database
 * @param string $customerEmail Customer email address
 * @param string $customerName Customer full name
 * @param PDO $pdo Database connection
 * @return array|null Virtual account data or null on failure
 */
/**
 * Create permanent virtual account for user and store in database (FIXED VERSION)
 */
function createPermanentVirtualAccount($token, $contractCode, $userId, $customerEmail, $customerName, $pdo)
{
    // Check if user already has a virtual account
    $stmt = $pdo->prepare("SELECT * FROM virtual_accounts WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $existingAccount = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingAccount) {
        error_log("Virtual Account: User $userId already has account: " . $existingAccount['account_number']);
        return $existingAccount;
    }

    // Create unique account reference for permanent account
    $accountReference = "USER_" . $userId . "_" . time();

    $payload = [
        "accountReference" => $accountReference,
        "accountName" => $customerName,
        "currencyCode" => "NGN",
        "contractCode" => $contractCode,
        "customerEmail" => $customerEmail,
        "customerName" => $customerName,
        "getAllAvailableBanks" => false,
        "preferredBanks" => ["035", "058", "011", "033", "057"]
    ];

    $url = "https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts";

    error_log("Permanent Virtual Account: Creating for user $userId ($customerEmail)");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log("Permanent Virtual Account cURL Error: " . $curlError);
        return null;
    }

    if ($httpCode !== 200) {
        error_log("Permanent Virtual Account HTTP Error: HTTP $httpCode - Payload: " . json_encode($payload) . " - Response: " . $response);
        return null;
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Permanent Virtual Account JSON Error: " . json_last_error_msg() . " - Response: " . $response);
        return null;
    }

    if (!isset($data['requestSuccessful']) || $data['requestSuccessful'] !== true) {
        $errorMessage = $data['responseMessage'] ?? 'Unknown error';
        error_log("Permanent Virtual Account API Error: $errorMessage - Payload: " . json_encode($payload));
        return null;
    }

    // FIX: Check for accounts array instead of direct accountNumber
    if (!isset($data['responseBody']['accounts']) || empty($data['responseBody']['accounts'])) {
        error_log("Permanent Virtual Account Missing: No accounts array in response - " . json_encode($data));
        return null;
    }

    // Get the first account from the accounts array
    $account = $data['responseBody']['accounts'][0];

    if (!isset($account['accountNumber'])) {
        error_log("Permanent Virtual Account Missing: No account number in first account - " . json_encode($data));
        return null;
    }

    // Extract account details from the accounts array
    $accountNumber = $account['accountNumber'];
    $accountName = $account['accountName'];
    $bankName = $account['bankName'];

    error_log("Permanent Virtual Account Parsed: Account $accountNumber, Name: $accountName, Bank: $bankName");

    // Store in database
    try {
        $stmt = $pdo->prepare("
            INSERT INTO virtual_accounts (user_id, account_number, account_name, bank_name, customer_email, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $result = $stmt->execute([$userId, $accountNumber, $accountName, $bankName, $customerEmail]);

        if ($result) {
            error_log("Permanent Virtual Account Success: Stored account $accountNumber for user $userId in database");

            return [
                'user_id' => $userId,
                'account_number' => $accountNumber,
                'account_name' => $accountName,
                'bank_name' => $bankName,
                'customer_email' => $customerEmail,
                'created_at' => date('Y-m-d H:i:s')
            ];
        } else {
            error_log("Permanent Virtual Account DB Error: Failed to store account for user $userId");
            return null;
        }
    } catch (PDOException $e) {
        error_log("Permanent Virtual Account DB Exception: " . $e->getMessage());
        return null;
    }
}

/**
 * Check payment status for a transaction
 * 
 * @param string $token Monnify access token
 * @param string $transactionReference Transaction reference to check
 * @return array|null Payment status data or null on failure
 */
function checkPaymentStatus($token, $transactionReference)
{
    if (empty($token)) {
        error_log("Payment Status Error: Token is empty");
        return null;
    }

    if (empty($transactionReference)) {
        error_log("Payment Status Error: Transaction reference is empty");
        return null;
    }

    $url = "https://sandbox.monnify.com/api/v2/transactions/" . urlencode($transactionReference);

    error_log("Payment Status Check: Checking status for transaction $transactionReference");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log("Payment Status cURL Error: " . $curlError);
        return null;
    }

    if ($httpCode !== 200) {
        error_log("Payment Status HTTP Error: HTTP $httpCode - URL: $url - Response: " . $response);
        return null;
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Payment Status JSON Error: " . json_last_error_msg() . " - Response: " . $response);
        return null;
    }

    if (!isset($data['requestSuccessful']) || $data['requestSuccessful'] !== true) {
        $errorMessage = $data['responseMessage'] ?? 'Unknown error';
        error_log("Payment Status API Error: $errorMessage - Transaction: $transactionReference - Response: " . json_encode($data));
        return null;
    }

    $paymentStatus = $data['responseBody']['paymentStatus'] ?? 'UNKNOWN';
    error_log("Payment Status Success: Transaction $transactionReference has status $paymentStatus");

    return $data;
}

/**
 * Initialize a payment transaction
 * 
 * @param string $token Monnify access token
 * @param string $contractCode Monnify contract code
 * @param float $amount Transaction amount
 * @param string $customerEmail Customer email
 * @param string $customerName Customer name
 * @param string $orderRef Order reference
 * @param string $redirectUrl Success redirect URL
 * @return array|null Payment initialization response or null on failure
 */
function initializePayment($token, $contractCode, $amount, $customerEmail, $customerName, $orderRef, $redirectUrl = '')
{
    if (empty($token) || empty($contractCode) || empty($amount) || empty($customerEmail) || empty($orderRef)) {
        error_log("Payment Init Error: Missing required parameters");
        return null;
    }

    if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        error_log("Payment Init Error: Invalid email format: $customerEmail");
        return null;
    }

    $payload = [
        "amount" => floatval($amount),
        "customerName" => $customerName,
        "customerEmail" => $customerEmail,
        "paymentReference" => $orderRef,
        "paymentDescription" => "ShopEase Frozen Foods Order",
        "currencyCode" => "NGN",
        "contractCode" => $contractCode,
        "redirectUrl" => $redirectUrl,
        "paymentMethods" => ["CARD", "ACCOUNT_TRANSFER"]
    ];

    $url = "https://sandbox.monnify.com/api/v1/merchant/transactions/init-transaction";

    error_log("Payment Init Request: Initializing payment of NGN$amount for $customerEmail with reference $orderRef");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log("Payment Init cURL Error: " . $curlError);
        return null;
    }

    if ($httpCode !== 200) {
        error_log("Payment Init HTTP Error: HTTP $httpCode - Payload: " . json_encode($payload) . " - Response: " . $response);
        return null;
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Payment Init JSON Error: " . json_last_error_msg() . " - Response: " . $response);
        return null;
    }

    if (!isset($data['requestSuccessful']) || $data['requestSuccessful'] !== true) {
        $errorMessage = $data['responseMessage'] ?? 'Unknown error';
        error_log("Payment Init API Error: $errorMessage - Payload: " . json_encode($payload) . " - Response: " . json_encode($data));
        return null;
    }

    $checkoutUrl = $data['responseBody']['checkoutUrl'] ?? '';
    error_log("Payment Init Success: Checkout URL generated for $customerEmail - URL: $checkoutUrl");

    return $data;
}

/**
 * Verify webhook signature (for production use)
 * 
 * @param string $payload Raw POST data
 * @param string $signature Monnify signature header
 * @param string $secretKey Your secret key
 * @return bool True if signature is valid
 */
function verifyWebhookSignature($payload, $signature, $secretKey)
{
    $computedSignature = hash_hmac('sha512', $payload, $secretKey);
    $isValid = hash_equals($computedSignature, $signature);

    if (!$isValid) {
        error_log("Webhook Signature Verification Failed: Expected $computedSignature, got $signature");
    } else {
        error_log("Webhook Signature Verified Successfully");
    }

    return $isValid;
}

/**
 * Log Monnify webhook for debugging
 * 
 * @param array $webhookData Webhook payload
 */
function logMonnifyWebhook($webhookData)
{
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event_type' => $webhookData['eventType'] ?? 'unknown',
        'transaction_reference' => $webhookData['eventData']['transactionReference'] ?? 'unknown',
        'payment_status' => $webhookData['eventData']['paymentStatus'] ?? 'unknown',
        'amount_paid' => $webhookData['eventData']['amountPaid'] ?? 0,
        'full_data' => $webhookData
    ];

    error_log("Monnify Webhook Received: " . json_encode($logData));
}
