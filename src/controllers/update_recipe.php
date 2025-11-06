<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Not logged in.";
    exit;
}

$recipe_id = $_POST['recipe_id'] ?? null;
if (!$recipe_id) { echo "Missing recipe ID."; exit; }

// Ownership check
$stmt = $pdo->prepare("SELECT user_id FROM recipe WHERE id=:id");
$stmt->execute(['id'=>$recipe_id]);
$owner = $stmt->fetchColumn();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
if ($owner != $_SESSION['user_id'] && !$isAdmin) {
    echo "Unauthorized.";
    exit;
}

// Ensure description column exists (best-effort, ignore errors on unsupported versions)
try {
    $pdo->exec("ALTER TABLE recipe ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER title");
} catch (Exception $e) {
    // ignore
}

// Update main details
$title = $_POST['title'];
$category = $_POST['category'];
// Whitelist allowed categories
$ALLOWED_CATEGORIES = [
    'Appetizer / Starter',
    'Main Course',
    'Side Dish',
    'Dessert',
    'Snack',
    'Soup / Salad',
    'Bread / Pastry',
    'Drink / Beverage',
    'Sauce / Dip / Spread'
];

if (!in_array($category, $ALLOWED_CATEGORIES, true)) {
    echo "Invalid category.";
    exit;
}
$tags = $_POST['tags'];
$description = trim($_POST['description'] ?? '');

// Best-effort: enforce ENUM constraint (safe if column already enum); ignore failures
try {
    $pdo->exec("ALTER TABLE recipe MODIFY category ENUM('Appetizer / Starter','Main Course','Side Dish','Dessert','Snack','Soup / Salad','Bread / Pastry','Drink / Beverage','Sauce / Dip / Spread') NOT NULL");
} catch (Exception $e) {
    // ignore if cannot alter
}

$mainImagePath = null;
if (!empty($_FILES['image_main']['name'])) {
    $uploadDir = __DIR__ . '/../../public/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $fileTmp = $_FILES['image_main']['tmp_name'];
    $fileType = @mime_content_type($fileTmp);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if ($fileType && isset($allowed[$fileType])) {
        $ext = $allowed[$fileType];
        $fileName = uniqid('recipe_', true) . '.' . $ext;
        if (@move_uploaded_file($fileTmp, $uploadDir . $fileName)) {
            $mainImagePath = 'uploads/' . $fileName;
        }
    }
}

$query = "UPDATE recipe SET title=:t, description=:d, category=:c, tags=:tag";
$params = ['t'=>$title, 'd'=>$description, 'c'=>$category, 'tag'=>$tags, 'id'=>$recipe_id];
if ($mainImagePath) {
    $query .= ", image_main=:img";
    $params['img'] = $mainImagePath;
}
$query .= " WHERE id=:id";
$pdo->prepare($query)->execute($params);

// Update ingredients
$pdo->prepare("DELETE FROM recipe_ingredients WHERE recipe_id=:id")->execute(['id'=>$recipe_id]);
$ingNames = $_POST['ingredient_name'];
$ingQty = $_POST['ingredient_qty'];
$ingStmt = $pdo->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_name, quantity) VALUES (:rid,:n,:q)");
foreach ($ingNames as $i=>$n) {
    $ingStmt->execute(['rid'=>$recipe_id, 'n'=>$n, 'q'=>$ingQty[$i]]);
}

// Update steps
$stepDesc = $_POST['step_description'];
$stepImages = $_FILES['step_image'];
$uploadDir = __DIR__ . '/../../public/uploads/';

// 1. Retrieve existing step images before deleting (based on form order assumption)
$existingImages = [];
$stmt = $pdo->prepare("SELECT step_image FROM recipe_steps WHERE recipe_id=:id ORDER BY step_number ASC");
$stmt->execute(['id'=>$recipe_id]);
$existingImages = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 2. Delete all old steps
$pdo->prepare("DELETE FROM recipe_steps WHERE recipe_id=:id")->execute(['id'=>$recipe_id]);

// 3. Re-insert steps, using existing image path if no new file is uploaded
$stepStmt = $pdo->prepare("INSERT INTO recipe_steps (recipe_id, step_number, step_description, step_image) VALUES (:rid,:num,:desc,:img)");
foreach ($stepDesc as $i=>$desc) {
    $imgPath = null;
    
    // Check if a new file was uploaded for this step index
    if (!empty($stepImages['name'][$i])) {
        $fileTmp = $stepImages['tmp_name'][$i];
        $fileType = @mime_content_type($fileTmp);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if ($fileType && isset($allowed[$fileType])) {
            $ext = $allowed[$fileType];
            $fileName = uniqid('step_', true) . '.' . $ext;
            // Note: You should ideally delete the OLD file from the filesystem here if it exists!
            if (@move_uploaded_file($fileTmp, $uploadDir . $fileName)) {
                $imgPath = 'uploads/' . $fileName;
            }
        }
    } else {
        // If NO new file, check for an existing image path at this index
        // This relies on the form submission order matching the database order.
        if (isset($existingImages[$i])) {
            $imgPath = $existingImages[$i];
        }
    }
    
    // Check if description is empty and skip insertion if so (optional cleanup)
    if (!empty(trim($desc)) || $imgPath) {
        $stepStmt->execute(['rid'=>$recipe_id,'num'=>$i+1,'desc'=>$desc,'img'=>$imgPath]);
    }
}

header("Location: /blog/public/recipe.php?id=$recipe_id");
exit;
?>
