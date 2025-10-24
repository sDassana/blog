<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /blog/public/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form data
$title = trim($_POST['title'] ?? '');
$category = $_POST['category'] ?? '';
$tags = trim($_POST['tags'] ?? '');
$ingredient_names = $_POST['ingredient_name'] ?? [];
$ingredient_qty = $_POST['ingredient_qty'] ?? [];
$step_descriptions = $_POST['step_description'] ?? [];
$step_images = $_FILES['step_image'] ?? null;

$errors = [];

if ($title === '' || $category === '' || empty($ingredient_names) || empty($step_descriptions)) {
    $errors[] = "All required fields must be filled.";
}

// Handle main image upload
$mainImagePath = null;
if (!empty($_FILES['image_main']['name'])) {
    $uploadDir = __DIR__ . '/../../public/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileTmp = $_FILES['image_main']['tmp_name'];
    $fileName = uniqid('recipe_') . '.' . pathinfo($_FILES['image_main']['name'], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $fileName;
    $fileType = mime_content_type($fileTmp);

    if (in_array($fileType, ['image/jpeg', 'image/png', 'image/webp'])) {
        move_uploaded_file($fileTmp, $filePath);
        $mainImagePath = 'uploads/' . $fileName;
    } else {
        $errors[] = "Invalid main image format. Only JPG, PNG, or WEBP allowed.";
    }
}

if (!empty($errors)) {
    foreach ($errors as $e) echo "<p style='color:red;'>$e</p>";
    exit;
}

try {
    $pdo->beginTransaction();

    // Insert recipe
    $stmt = $pdo->prepare("INSERT INTO recipe (user_id, title, category, tags, image_main) VALUES (:user_id, :title, :category, :tags, :image_main)");
    $stmt->execute([
        'user_id' => $user_id,
        'title' => $title,
        'category' => $category,
        'tags' => $tags,
        'image_main' => $mainImagePath
    ]);
    $recipe_id = $pdo->lastInsertId();

    // Insert ingredients
    $ingStmt = $pdo->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_name, quantity) VALUES (:recipe_id, :ingredient_name, :quantity)");
    foreach ($ingredient_names as $i => $name) {
        $ingStmt->execute([
            'recipe_id' => $recipe_id,
            'ingredient_name' => trim($name),
            'quantity' => trim($ingredient_qty[$i] ?? '')
        ]);
    }

    // Insert steps
    $stepStmt = $pdo->prepare("INSERT INTO recipe_steps (recipe_id, step_number, step_description, step_image) VALUES (:recipe_id, :step_number, :step_description, :step_image)");
    foreach ($step_descriptions as $i => $desc) {
        $stepImagePath = null;
        if (!empty($step_images['name'][$i])) {
            $fileTmp = $step_images['tmp_name'][$i];
            $fileName = uniqid('step_') . '.' . pathinfo($step_images['name'][$i], PATHINFO_EXTENSION);
            $filePath = $uploadDir . $fileName;
            $fileType = mime_content_type($fileTmp);

            if (in_array($fileType, ['image/jpeg', 'image/png', 'image/webp'])) {
                move_uploaded_file($fileTmp, $filePath);
                $stepImagePath = 'uploads/' . $fileName;
            }
        }

        $stepStmt->execute([
            'recipe_id' => $recipe_id,
            'step_number' => $i + 1,
            'step_description' => trim($desc),
            'step_image' => $stepImagePath
        ]);
    }

    $pdo->commit();
    echo "Recipe uploaded successfully! <a href='../../public/view_recipes.php'>View All Recipes</a>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error saving recipe: " . htmlspecialchars($e->getMessage());
}
?>
