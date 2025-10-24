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
$newEmail = trim($_POST['email'] ?? '');
$currentPassword = $_POST['current_password'] ?? '';

if ($newEmail === '' || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Please enter a valid email address.');
    header('Location: /blog/public/dashboard.php');
    exit;
}

try {
    // Load current user
    $stmt = $pdo->prepare('SELECT email, password FROM `user` WHERE id = :id');
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        throw new Exception('User not found');
    }

    // Check password
    if (!password_verify($currentPassword, $row['password'])) {
        setFlash('error', 'Current password is incorrect.');
        header('Location: /blog/public/dashboard.php');
        exit;
    }

    // Check if email is taken by another user
    $check = $pdo->prepare('SELECT id FROM `user` WHERE email = :email AND id <> :id');
    $check->execute(['email' => $newEmail, 'id' => $userId]);
    if ($check->fetch()) {
        setFlash('error', 'This email is already in use.');
        header('Location: /blog/public/dashboard.php');
        exit;
    }

    // Update
    $upd = $pdo->prepare('UPDATE `user` SET email = :email WHERE id = :id');
    $upd->execute(['email' => $newEmail, 'id' => $userId]);

    setFlash('success', 'Email updated successfully.');
} catch (Exception $e) {
    setFlash('error', 'Failed to update email: ' . htmlspecialchars($e->getMessage()));
}

header('Location: /blog/public/dashboard.php');
exit;
