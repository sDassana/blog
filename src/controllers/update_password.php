<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/helpers/flash.php';

if (!isset($_SESSION['user_id'])) {
    setFlash('error', 'You must be logged in.');
    header('Location: /blog/public/login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (strlen($newPassword) < 8) {
    setFlash('error', 'New password must be at least 8 characters.');
    header('Location: /blog/public/dashboard.php');
    exit;
}

if ($newPassword !== $confirmPassword) {
    setFlash('error', 'New password and confirmation do not match.');
    header('Location: /blog/public/dashboard.php');
    exit;
}

try {
    // Load current hash
    $stmt = $pdo->prepare('SELECT password FROM `user` WHERE id = :id');
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        throw new Exception('User not found');
    }

    if (!password_verify($currentPassword, $row['password'])) {
        setFlash('error', 'Current password is incorrect.');
        header('Location: /blog/public/dashboard.php');
        exit;
    }

    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE `user` SET password = :pw WHERE id = :id');
    $upd->execute(['pw' => $hash, 'id' => $userId]);

    setFlash('success', 'Password updated successfully.');
} catch (Exception $e) {
    setFlash('error', 'Failed to update password: ' . htmlspecialchars($e->getMessage()));
}

header('Location: /blog/public/dashboard.php');
exit;
