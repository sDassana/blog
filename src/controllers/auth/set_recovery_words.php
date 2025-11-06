<?php
// src/controllers/auth/set_recovery_words.php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../helpers/recovery_words.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /blog/public/login.php');
    exit;
}

$uid = (int) $_SESSION['user_id'];
$words = $_POST['words'] ?? [];

try {
    if (!is_array($words) || count($words) !== 5) {
        throw new InvalidArgumentException('Please provide exactly five words.');
    }
    $words = array_values($words);
    $normalized = normalize_recovery_words($words);
    $hashes = hash_recovery_words($normalized); // length 5

    // Upsert into user_recovery
    $pdo->beginTransaction();
    $exists = $pdo->prepare('SELECT id FROM user_recovery WHERE user_id = :uid LIMIT 1');
    $exists->execute(['uid' => $uid]);
    $row = $exists->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $stmt = $pdo->prepare('UPDATE user_recovery SET word_hash1 = :h1, word_hash2 = :h2, word_hash3 = :h3, word_hash4 = :h4, word_hash5 = :h5, created_at = NOW() WHERE user_id = :uid');
        $stmt->execute([
            'h1' => $hashes[0],
            'h2' => $hashes[1],
            'h3' => $hashes[2],
            'h4' => $hashes[3],
            'h5' => $hashes[4],
            'uid' => $uid,
        ]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO user_recovery (user_id, word_hash1, word_hash2, word_hash3, word_hash4, word_hash5, created_at) VALUES (:uid, :h1, :h2, :h3, :h4, :h5, NOW())');
        $stmt->execute([
            'uid' => $uid,
            'h1' => $hashes[0],
            'h2' => $hashes[1],
            'h3' => $hashes[2],
            'h4' => $hashes[3],
            'h5' => $hashes[4],
        ]);
    }
    $pdo->commit();

    // One-time preview for the user to copy
    $_SESSION['recovery_preview'] = $normalized;
    setFlash('success', 'Recovery words saved. Copy them now and store safely.');
    header('Location: /blog/public/recovery_words.php');
    exit;
} catch (Throwable $e) {
    if ($pdo && $pdo->inTransaction()) { $pdo->rollBack(); }
    @file_put_contents(
        __DIR__ . '/../../../logs/errors.log',
        '[' . date('Y-m-d H:i:s') . "] set_recovery_words: " . $e->getMessage() . PHP_EOL,
        FILE_APPEND
    );
    setFlash('error', 'Could not save recovery words. Please check input and try again.');
    header('Location: /blog/public/recovery_words.php');
    exit;
}
