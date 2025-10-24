<?php
session_start();
require_once __DIR__ . '/../config/config.php';

$recipe_id = $_GET['id'] ?? null;
if (!$recipe_id) { echo "Missing recipe ID."; exit; }

// Fetch recipe data
$stmt = $pdo->prepare("SELECT * FROM recipe WHERE id=:id");
$stmt->execute(['id'=>$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) { echo "Recipe not found."; exit; }

// Ownership check
if ($_SESSION['user_id'] != $recipe['user_id']) {
    echo "Unauthorized.";
    exit;
}

// Fetch ingredients and steps
$ings = $pdo->prepare("SELECT * FROM recipe_ingredients WHERE recipe_id=:id");
$ings->execute(['id'=>$recipe_id]);
$ingredients = $ings->fetchAll(PDO::FETCH_ASSOC);

$stepsStmt = $pdo->prepare("SELECT * FROM recipe_steps WHERE recipe_id=:id ORDER BY step_number");
$stepsStmt->execute(['id'=>$recipe_id]);
$steps = $stepsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Recipe · The Cookie Lovestoblog</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="max-w-4xl mx-auto px-4 py-8 mb-16">
      <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
        <h2 class="text-xl font-bold mb-4">Edit Recipe</h2>
        <form action="../src/controllers/update_recipe.php" method="POST" enctype="multipart/form-data" class="space-y-5">
          <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">

          <div>
            <label class="block text-sm text-gray-600 mb-1">Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($recipe['title']) ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Category</label>
            <select name="category" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
              <?php
              $cats = ['Main Dish','Dessert','Drink'];
              foreach ($cats as $cat) {
                  $sel = ($recipe['category'] === $cat) ? 'selected' : '';
                  echo "<option value='$cat' $sel>$cat</option>";
              }
              ?>
            </select>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Tags</label>
            <input type="text" name="tags" value="<?= htmlspecialchars($recipe['tags']) ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Replace main image (optional)</label>
            <input type="hidden" name="existing_image_main" value="<?= htmlspecialchars($recipe['image_main']) ?>">
            <input type="file" name="image_main" accept="image/*" class="block w-full text-sm text-gray-700">
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-2">Ingredients</h3>
            <div id="ingredients" class="space-y-2">
              <?php foreach ($ingredients as $ing): ?>
                <div class="flex flex-col sm:flex-row gap-2">
                  <input type="text" name="ingredient_name[]" value="<?= htmlspecialchars($ing['ingredient_name']) ?>" required class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
                  <input type="text" name="ingredient_qty[]" value="<?= htmlspecialchars($ing['quantity']) ?>" required class="w-full sm:w-40 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" onclick="addIngredient()" class="mt-2 inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 text-sm hover:bg-[#e5573e]">+ Add Ingredient</button>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-2">Steps</h3>
            <div id="steps" class="space-y-3">
              <?php foreach ($steps as $s): ?>
                <div>
                  <textarea name="step_description[]" rows="3" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]"><?= htmlspecialchars($s['step_description']) ?></textarea>
                  <input type="hidden" name="step_existing_image[]" value="<?= htmlspecialchars($s['step_image'] ?? '') ?>">
                  <input type="file" name="step_image[]" accept="image/*" class="mt-2 block w-full text-sm text-gray-700">
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" onclick="addStep()" class="mt-2 inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 text-sm hover:bg-[#e5573e]">+ Add Step</button>
          </div>

          <div class="pt-2">
            <button type="submit" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold shadow hover:bg-[#e5573e]">Update Recipe</button>
          </div>
        </form>
      </div>
    </main>

    <script>
      function addIngredient() {
        const div = document.createElement('div');
        div.className = 'flex flex-col sm:flex-row gap-2';
  div.innerHTML = '<input type="text" name="ingredient_name[]" placeholder="Ingredient" required class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />' +
      '<input type="text" name="ingredient_qty[]" placeholder="Quantity" required class="w-full sm:w-40 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />';
        document.getElementById('ingredients').appendChild(div);
      }

      function addStep() {
        const div = document.createElement('div');
  div.innerHTML = '<textarea name="step_description[]" rows="3" placeholder="Step description" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]"></textarea>' +
                        '<input type="file" name="step_image[]" accept="image/*" class="mt-2 block w-full text-sm text-gray-700" />';
        document.getElementById('steps').appendChild(div);
      }
    </script>
    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
</html>
