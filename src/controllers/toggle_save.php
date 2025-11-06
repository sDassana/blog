<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in to save recipes.']);
    exit;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
$recipe_id = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;

if ($recipe_id <= 0) {
    echo json_encode(['error' => 'Invalid recipe ID']);
    exit;
}

try {
    // Ensure table exists (best-effort) and has a unique constraint
    $pdo->exec("CREATE TABLE IF NOT EXISTS recipe_saves (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        recipe_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_user_recipe (user_id, recipe_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Toggle save status
    $check = $pdo->prepare('SELECT id FROM recipe_saves WHERE user_id = :uid AND recipe_id = :rid');
    $check->execute(['uid' => $user_id, 'rid' => $recipe_id]);

    if ($check->rowCount() > 0) {
        $del = $pdo->prepare('DELETE FROM recipe_saves WHERE user_id = :uid AND recipe_id = :rid');
        $del->execute(['uid' => $user_id, 'rid' => $recipe_id]);
        echo json_encode(['status' => 'unsaved']);
    } else {
        $ins = $pdo->prepare('INSERT INTO recipe_saves (user_id, recipe_id) VALUES (:uid, :rid)');
        $ins->execute(['uid' => $user_id, 'rid' => $recipe_id]);
        echo json_encode(['status' => 'saved']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
