<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require __DIR__ . '/../config/database.php';

$admin_id = $_SESSION['admin_id'];

// Update last login timestamp for the user
$stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
$stmt->execute([$admin_id]);

// Destroy session
session_unset();
session_destroy();

// Redirect to homepage/login
header("Location: index.php");
exit();
