<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: /salya/index.php");
    exit();
}
try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    exit();
}
