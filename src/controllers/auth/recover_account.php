<?php
// Resets a password after confirming the submitted recovery words and new credential.
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../helpers/recovery_words.php';

$email = trim($_POST['email'] ?? '');
$words = $_POST['words'] ?? [];
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($email === '' || !is_array($words) || count($words) !== 5 || $new === '' || $confirm === '') {
    setFlash('error', 'Please fill all fields.');
    header('Location: /blog/public/recover_account.php');
    exit;
}

// Strong password policy: 8+ chars, upper, lower, digit, symbol
$strongPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';
if (!preg_match($strongPattern, $new)) {
    setFlash('error', 'New password must be at least 8 characters and include uppercase, lowercase, a number, and a symbol.');
    header('Location: /blog/public/recover_account.php');
    exit;
}

if ($new !== $confirm) {
    setFlash('error', 'Passwords do not match.');
    header('Location: /blog/public/recover_account.php');
    exit;
}

try {
    $normalized = normalize_recovery_words(array_values($words));

    // Find user by email
    $stmt = $pdo->prepare('SELECT id FROM user WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        throw new RuntimeException('No account found for that email.');
    }

    $uid = (int) $user['id'];

    // Fetch stored hashes
    $r = $pdo->prepare('SELECT word_hash1, word_hash2, word_hash3, word_hash4, word_hash5 FROM user_recovery WHERE user_id = :uid LIMIT 1');
    $r->execute(['uid' => $uid]);
    $rec = $r->fetch(PDO::FETCH_ASSOC);
    if (!$rec) {
        throw new RuntimeException('Recovery words not set for this account.');
    }

    $hashes = [
        $rec['word_hash1'] ?? '',
        $rec['word_hash2'] ?? '',
        $rec['word_hash3'] ?? '',
        $rec['word_hash4'] ?? '',
        $rec['word_hash5'] ?? '',
    ];

    // Verify in order
    for ($i = 0; $i < 5; $i++) {
        if (!is_string($hashes[$i]) || $hashes[$i] === '' || !password_verify($normalized[$i], $hashes[$i])) {
            throw new RuntimeException('Recovery words are incorrect.');
        }
    }

    // All good -> update password
    $pwdHash = password_hash($new, PASSWORD_DEFAULT);
    $u = $pdo->prepare('UPDATE user SET password = :pw WHERE id = :id');
    $u->execute(['pw' => $pwdHash, 'id' => $uid]);

    setFlash('success', 'Password reset successful. You can now login.');
    header('Location: /blog/public/login.php');
    exit;
} catch (Throwable $e) {
    @file_put_contents(
        __DIR__ . '/../../../logs/errors.log',
        '[' . date('Y-m-d H:i:s') . "] recover_account: " . $e->getMessage() . PHP_EOL,
        FILE_APPEND
    );
    setFlash('error', $e->getMessage());
    header('Location: /blog/public/recover_account.php');
    exit;
}
