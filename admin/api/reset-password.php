<?php
require __DIR__ . '/initialize.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['user_id'])) {
        throw new Exception("User ID is required.");
    }

    $userId = (int) $input['user_id'];
    $tempPassword = 'bin2hex(random_bytes(4))'; // e.g., 8-char temp password
    $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $userId]);

    // Get user email
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }

    // Send new password
    $to = $user['email'];
    $subject = "Your Password Has Been Reset";
    $message = "Your new temporary password is: $tempPassword\n\nPlease log in and change it.";
    $headers = "From: no-reply@yourdomain.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8";

    if (!mail($to, $subject, $message, $headers)) {
        throw new Exception("Password updated but failed to send email.");
    }

    echo json_encode(['success' => true, 'message' => 'Password reset and email sent.']);
} catch (Exception $e) {
    error_log("[RESET ERROR] " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
