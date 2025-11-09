<?php
// Updates profile details (display name only).
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    setFlash('error', 'You must be logged in.');
    header('Location: /blog/public/login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$username = trim($_POST['username'] ?? '');
if ($username === '') {
    setFlash('error', 'Display name is required.');
    header('Location: /blog/public/dashboard.php');
    exit;
}

$updates = ['username' => $username];
$performed = ['Changed Display Name'];

try {
    $sql = 'UPDATE `user` SET username = :username WHERE id = :id';
    $updates['id'] = $userId;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updates);

    // keep session display name in sync
    $_SESSION['username'] = $username;

    $performed = array_values(array_unique($performed));
    setFlash('success', 'Profile updated: ' . implode(', ', $performed));
} catch (Exception $e) {
    setFlash('error', 'Failed to update profile: ' . htmlspecialchars($e->getMessage()));
}

header('Location: /blog/public/dashboard.php');
exit;
