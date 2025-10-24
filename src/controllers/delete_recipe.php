<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../helpers/flash.php';


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in.']);
    exit;
}

$recipe_id = $_POST['recipe_id'] ?? null;

if (!$recipe_id) {
    echo json_encode(['error' => 'Missing recipe ID.']);
    exit;
}

// Verify ownership
$stmt = $pdo->prepare("SELECT user_id FROM recipe WHERE id=:id");
$stmt->execute(['id' => $recipe_id]);
$owner = $stmt->fetchColumn();

if (!$owner) {
    echo json_encode(['error' => 'Recipe not found.']);
    exit;
}

if ($owner != $_SESSION['user_id']) {
    setFlash('error', 'You cannot delete this recipe.');
    header('Location: /blog/public/view_recipes.php');
    exit;
}

// Delete recipe (thanks to foreign keys, ingredients, steps, comments, likes are auto-deleted)
$pdo->prepare("DELETE FROM recipe WHERE id=:id")->execute(['id' => $recipe_id]);
setFlash('success', 'Recipe deleted successfully!');
header('Location: /blog/public/view_recipes.php');
exit;
