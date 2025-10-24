<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$comment_id = $_POST['comment_id'] ?? null;
if (!$comment_id) {
    echo json_encode(['error' => 'Missing comment ID']);
    exit;
}

$stmt = $pdo->prepare("SELECT user_id FROM recipe_comments WHERE id = :id");
$stmt->execute(['id' => $comment_id]);
$owner = $stmt->fetchColumn();

if ($owner != $_SESSION['user_id']) {
    echo json_encode(['error' => 'You can only delete your own comments']);
    exit;
}

$pdo->prepare("DELETE FROM recipe_comments WHERE id = :id")->execute(['id' => $comment_id]);
echo json_encode(['status' => 'deleted']);
