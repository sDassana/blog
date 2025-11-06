<?php
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in to comment.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$recipe_id = $_POST['recipe_id'] ?? null;
$text = trim($_POST['comment_text'] ?? '');

if (!$recipe_id || $text === '') {
    echo json_encode(['error' => 'Missing recipe or empty comment.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO recipe_comments (recipe_id, user_id, comment_text)
                           VALUES (:rid, :uid, :txt)");
    $stmt->execute(['rid' => $recipe_id, 'uid' => $user_id, 'txt' => $text]);
    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
