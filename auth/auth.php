<?php
session_start();
require_once __DIR__ . '/database.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: /salya/index.php");
    exit();
}
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    exit();
}
