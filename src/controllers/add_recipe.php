<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /blog/public/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure uploads directory path is available everywhere
$uploadDir = __DIR__ . '/../../public/uploads/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// Handle form data
$title = trim($_POST['title'] ?? '');
$category = $_POST['category'] ?? '';
$tags = trim($_POST['tags'] ?? '');
$description = trim($_POST['description'] ?? '');
$ingredient_names = $_POST['ingredient_name'] ?? [];
$ingredient_qty = $_POST['ingredient_qty'] ?? [];
$step_descriptions = $_POST['step_description'] ?? [];
$step_images = $_FILES['step_image'] ?? null;

$errors = [];

if ($title === '' || $category === '' || empty($ingredient_names) || empty($step_descriptions)) {
    $errors[] = "All required fields must be filled.";
}

// Handle main image upload (safe filename + whitelist by MIME)
$mainImagePath = null;
if (!empty($_FILES['image_main']['name'])) {
    $fileTmp = $_FILES['image_main']['tmp_name'];
    $fileType = @mime_content_type($fileTmp);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if ($fileType && isset($allowed[$fileType])) {
        $ext = $allowed[$fileType];
        $fileName = uniqid('recipe_', true) . '.' . $ext; // do not trust client filename
        $filePath = $uploadDir . $fileName;
        if (@move_uploaded_file($fileTmp, $filePath)) {
            $mainImagePath = 'uploads/' . $fileName;
        }
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

    // Ensure category column can store new categories (migrate from ENUM to VARCHAR if needed)
    try {
        $pdo->exec("ALTER TABLE recipe MODIFY category VARCHAR(100) NOT NULL");
    } catch (Exception $e) {
        // ignore if not needed or lacks permissions
    }

    // Ensure description column exists (best-effort)
    try {
        $pdo->exec("ALTER TABLE recipe ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER title");
    } catch (Exception $e) {
        // ignore if not supported or already exists
    }

    // Insert recipe
    $stmt = $pdo->prepare("INSERT INTO recipe (user_id, title, description, category, tags, image_main) VALUES (:user_id, :title, :description, :category, :tags, :image_main)");
    $stmt->execute([
        'user_id' => $user_id,
        'title' => $title,
        'description' => $description,
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
        if (is_array($step_images) && isset($step_images['name'][$i]) && !empty($step_images['name'][$i]) && isset($step_images['tmp_name'][$i])) {
            $fileTmp = $step_images['tmp_name'][$i];
            $fileType = @mime_content_type($fileTmp);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if ($fileType && isset($allowed[$fileType])) {
                $ext = $allowed[$fileType];
                $fileName = uniqid('step_', true) . '.' . $ext;
                $filePath = $uploadDir . $fileName;
                if (@move_uploaded_file($fileTmp, $filePath)) {
                    $stepImagePath = 'uploads/' . $fileName;
                }
            }
        }

        $stepStmt->execute([
            'recipe_id' => $recipe_id,
            'step_number' => $i + 1,
            'step_description' => trim($desc),
            'step_image' => $stepImagePath
        ]);
    }

    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    header("Location: /blog/public/view_recipes.php");
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error saving recipe: " . htmlspecialchars($e->getMessage());
}
?>
