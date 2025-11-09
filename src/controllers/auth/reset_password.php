<?php
// Handles the simple email-based reset flow triggered from forgot_password.php.
require_once __DIR__ . '/../../../config/config.php';

$email = $_POST['email'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($new_password !== $confirm) {
    setFlash('error', 'Passwords do not match.');
    header('Location: /blog/public/forgot_password.php');
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM user WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user) {
    setFlash('error', 'No account found with that email.');
    header('Location: /blog/public/forgot_password.php');
    exit;
}

// Hash and update
$hash = password_hash($new_password, PASSWORD_DEFAULT);
$update = $pdo->prepare("UPDATE user SET password = :pw WHERE email = :email");
$update->execute(['pw' => $hash, 'email' => $email]);

setFlash('success', 'Password reset successful! You can now login.');
header('Location: /blog/public/login.php');
exit;
?>
