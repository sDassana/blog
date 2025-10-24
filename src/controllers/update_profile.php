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
$username = trim($_POST['username'] ?? '');
$about = trim($_POST['about'] ?? '');

if ($username === '') {
    setFlash('error', 'Display name is required.');
    header('Location: /blog/public/dashboard.php');
    exit;
}

// Helper to check if a column exists on `user`
function userHasColumn(PDO $pdo, string $column): bool {
    try {
        $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
        $q = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = \"user\" AND COLUMN_NAME = :col');
        $q->execute(['db' => $dbName, 'col' => $column]);
        return (int)$q->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

$updates = ['username' => $username];
$fields = ['username = :username'];

// About: optional if column exists
$aboutIncluded = false;
if ($about !== '' || userHasColumn($pdo, 'about')) {
    if (userHasColumn($pdo, 'about')) {
        $aboutIncluded = true;
        $updates['about'] = $about;
        $fields[] = 'about = :about';
    }
}

// Handle avatar upload
$avatarPath = null;
$avatarIncluded = false;
if (!empty($_FILES['avatar']['name'])) {
    $tmp = $_FILES['avatar']['tmp_name'];
    if (is_uploaded_file($tmp)) {
        $type = mime_content_type($tmp);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (isset($allowed[$type])) {
            $ext = $allowed[$type];
            $dir = __DIR__ . '/../../public/uploads/avatars/';
            if (!is_dir($dir)) { @mkdir($dir, 0777, true); }

            // Delete previous avatar if stored
            if (userHasColumn($pdo, 'avatar')) {
                $prev = $pdo->prepare('SELECT avatar FROM `user` WHERE id = :id');
                $prev->execute(['id' => $userId]);
                $prevPath = $prev->fetchColumn();
                if ($prevPath) {
                    $fullPrev = __DIR__ . '/../../public/' . ltrim($prevPath, '/');
                    if (is_file($fullPrev)) { @unlink($fullPrev); }
                }
            }

            $fileName = 'u' . $userId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($tmp, $dir . $fileName)) {
                $avatarPath = 'uploads/avatars/' . $fileName;
            }
        }
    }
}

if ($avatarPath && userHasColumn($pdo, 'avatar')) {
    $avatarIncluded = true;
    $updates['avatar'] = $avatarPath;
    $fields[] = 'avatar = :avatar';
}

if (empty($fields)) {
    setFlash('error', 'Nothing to update.');
    header('Location: /blog/public/dashboard.php');
    exit;
}

try {
    $sql = 'UPDATE `user` SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $updates['id'] = $userId;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updates);

    // keep session display name in sync
    $_SESSION['username'] = $username;

    $notes = [];
    if (!$aboutIncluded) { $notes[] = 'about'; }
    if (!($avatarIncluded || empty($_FILES['avatar']['name']))) { $notes[] = 'avatar'; }

    if (!empty($notes)) {
        setFlash('success', 'Profile updated. Note: columns missing for ' . implode(', ', $notes) . '.');
    } else {
        setFlash('success', 'Profile updated successfully.');
    }
} catch (Exception $e) {
    setFlash('error', 'Failed to update profile: ' . htmlspecialchars($e->getMessage()));
}

header('Location: /blog/public/dashboard.php');
exit;
