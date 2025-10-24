<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in to like recipes.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$recipe_id = $_POST['recipe_id'] ?? null;

if (!$recipe_id) {
    echo json_encode(['error' => 'Missing recipe ID.']);
    exit;
}

try {
    // Check if already liked
    $check = $pdo->prepare("SELECT id FROM recipe_likes WHERE user_id = :uid AND recipe_id = :rid");
    $check->execute(['uid' => $user_id, 'rid' => $recipe_id]);

    if ($check->rowCount() > 0) {
        // Unlike
        $pdo->prepare("DELETE FROM recipe_likes WHERE user_id = :uid AND recipe_id = :rid")
            ->execute(['uid' => $user_id, 'rid' => $recipe_id]);
        echo json_encode(['status' => 'unliked']);
    } else {
        // Like
        $pdo->prepare("INSERT INTO recipe_likes (user_id, recipe_id) VALUES (:uid, :rid)")
            ->execute(['uid' => $user_id, 'rid' => $recipe_id]);
        echo json_encode(['status' => 'liked']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
