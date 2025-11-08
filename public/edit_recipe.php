<?php
require_once __DIR__ . '/../config/config.php';

$recipe_id = $_GET['id'] ?? null;
if (!$recipe_id) { echo "Missing recipe ID."; exit; }

// Fetch recipe data
$stmt = $pdo->prepare("SELECT * FROM recipe WHERE id=:id");
$stmt->execute(['id'=>$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) { echo "Recipe not found."; exit; }

// Ownership or admin check
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
if ($_SESSION['user_id'] != $recipe['user_id'] && !$isAdmin) {
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
    <?php 
    $pageTitle = 'Edit Recipe · The Cookie Lovestoblog'; 
    $extraHead = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.js"></script>';
    include __DIR__ . '/partials/header.php'; 
    ?>
  </head>
  <body class="flex flex-col min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="max-w-4xl mx-auto px-4 py-8 mb-16">
      <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
        <h2 class="text-xl font-bold mb-4">Edit Recipe</h2>
  <form id="editRecipeForm" action="../src/controllers/update_recipe.php" method="POST" enctype="multipart/form-data" class="space-y-5">
          <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">

          <div>
            <label class="block text-sm text-gray-600 mb-1">Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($recipe['title']) ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Category</label>
            <select name="category" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
              <?php
              $cats = [
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
              foreach ($cats as $cat) {
                  $sel = ($recipe['category'] === $cat) ? 'selected' : '';
                  $val = htmlspecialchars($cat, ENT_QUOTES, 'UTF-8');
                  echo "<option value=\"$val\" $sel>$val</option>";
              }
              ?>
            </select>
          </div>

          <div class="mb-2">
            <label for="description" class="block text-sm text-gray-600 mb-1">Short Description</label>
            <textarea id="description" name="description" placeholder="Short description (Markdown supported)"><?= htmlspecialchars($recipe['description'] ?? '') ?></textarea>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Tags</label>
            <input type="text" name="tags" value="<?= htmlspecialchars($recipe['tags']) ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Replace main image (optional)</label>
            <input type="hidden" name="existing_image_main" value="<?= htmlspecialchars($recipe['image_main']) ?>">
            <div class="modern-file flex items-center">
              <input id="image_main" type="file" name="image_main" accept="image/*" class="hidden">
               <label for="image_main" class="inline-flex items-center rounded-[15px] bg-black text-white px-4 py-2 font-semibold shadow hover:bg-black/90 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 mr-2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7.5h3l1.5-2.25h9L18 7.5h3A1.5 1.5 0 0122.5 9v9A1.5 1.5 0 0121 19.5H3A1.5 1.5 0 011.5 18V9A1.5 1.5 0 013 7.5zm9 9a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"/></svg>
                Upload image
              </label>
              <span class="file-name text-sm text-gray-600 ml-3">No file chosen</span>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-2">Ingredients</h3>
            <div id="ingredients" class="space-y-2">
              <?php foreach ($ingredients as $ing): ?>
                <div class="flex flex-col sm:flex-row gap-2">
                  <input type="text" name="ingredient_name[]" value="<?= htmlspecialchars($ing['ingredient_name']) ?>" required class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
                  <input type="text" name="ingredient_qty[]" value="<?= htmlspecialchars($ing['quantity']) ?>" placeholder="Quantity (optional)" class="w-full sm:w-40 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" onclick="addIngredient()" class="mt-2 inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 text-sm hover:bg-[#e5573e]">+ Add Ingredient</button>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-2">Steps</h3>
            <div id="steps" class="space-y-3">
              <?php foreach ($steps as $i => $s): ?>
                <div>
                  <textarea name="step_description[]"><?= htmlspecialchars($s['step_description']) ?></textarea>
                  <input type="hidden" name="step_existing_image[]" value="<?= htmlspecialchars($s['step_image'] ?? '') ?>">
                  <div class="modern-file mt-2 flex items-center">
                    <input id="step_image_<?= $i ?>" type="file" name="step_image[]" accept="image/*" class="hidden">
                     <label for="step_image_<?= $i ?>" class="inline-flex items-center rounded-[15px] bg-black text-white px-4 py-2 font-semibold shadow hover:bg-black/90 cursor-pointer">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 mr-2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7.5h3l1.5-2.25h9L18 7.5h3A1.5 1.5 0 0122.5 9v9A1.5 1.5 0 0121 19.5H3A1.5 1.5 0 011.5 18V9A1.5 1.5 0 013 7.5zm9 9a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"/></svg>
                        Upload step image
                    </label>
                    <span class="file-name text-sm text-gray-600 ml-3">No file chosen</span>
                  </div>
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

    <script type="module">
      import { initModernFileInput } from './js/file-input.js';

      // Initialize SimpleMDE for description
      const descriptionEditor = new EasyMDE({
        element: document.getElementById('description'),
        placeholder: 'Short description (Markdown supported)',
        spellChecker: false,
        toolbar: ['bold', 'italic', 'heading', '|', 'quote', 'unordered-list', 'ordered-list', '|', 'link', 'image', '|', 'preview', 'side-by-side', 'fullscreen', '|', 'guide'],
        status: false
      });

      // Store SimpleMDE instances for step textareas
      const stepEditors = new Map();

      // Initialize SimpleMDE for existing steps
      document.querySelectorAll('#steps textarea[name="step_description[]"]').forEach((textarea) => {
        const editor = new EasyMDE({
          element: textarea,
          placeholder: 'Describe this step (Markdown supported)',
          spellChecker: false,
          toolbar: ['bold', 'italic', '|', 'unordered-list', 'ordered-list', '|', 'link', 'image', '|', 'preview', 'guide'],
          status: false
        });
        stepEditors.set(textarea, editor);
      });

      function addIngredient() {
        const div = document.createElement('div');
        div.className = 'flex flex-col sm:flex-row gap-2';
        // Do not require dynamically added rows; existing rows already ensure at least one ingredient
        div.innerHTML = '<input type="text" name="ingredient_name[]" placeholder="Ingredient" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />' +
                        '<input type="text" name="ingredient_qty[]" placeholder="Quantity (optional)" class="w-full sm:w-40 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />';
        document.getElementById('ingredients').appendChild(div);
      }

      function addStep() {
        const div = document.createElement('div');
  const stepInputId = 'step_image_' + Date.now();
  const textareaId = 'step_textarea_' + Date.now();
  div.innerHTML = '<textarea id="' + textareaId + '" name="step_description[]" placeholder="Step description (Markdown supported)"></textarea>' +
                  '<div class="modern-file mt-2 flex items-center">' +
                    '<input id="' + stepInputId + '" type="file" name="step_image[]" accept="image/*" class="hidden" />' +
                    '<label for="' + stepInputId + '" class="inline-flex items-center rounded-[15px] bg-black text-white px-4 py-2 font-semibold shadow hover:bg-black/90 cursor-pointer">' +
                      '<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" class=\"w-4 h-4 mr-2\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"1.5\" d=\"M3 7.5h3l1.5-2.25h9L18 7.5h3A1.5 1.5 0 0122.5 9v9A1.5 1.5 0 0121 19.5H3A1.5 1.5 0 011.5 18V9A1.5 1.5 0 013 7.5zm9 9a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z\"/></svg>' +
                      'Upload step image' +
                    '</label>' +
                    '<span class="file-name text-sm text-gray-600 ml-3">No file chosen</span>' +
                  '</div>';
        document.getElementById('steps').appendChild(div);

        // Initialize SimpleMDE for the newly added textarea
        const textarea = document.getElementById(textareaId);
        const editor = new EasyMDE({
          element: textarea,
          placeholder: 'Describe this step (Markdown supported)',
          spellChecker: false,
          toolbar: ['bold', 'italic', '|', 'unordered-list', 'ordered-list', '|', 'link', 'image', '|', 'preview', 'guide'],
          status: false
        });
        stepEditors.set(textarea, editor);

        // Initialize modern file input for the newly added step
        initModernFileInput(div);
      }

      window.addIngredient = addIngredient;
      window.addStep = addStep;

      // Initialize modern file inputs on load
      initModernFileInput(document);

      // Ensure SimpleMDE syncs values before form submission (bind to the correct form)
      const form = document.getElementById('editRecipeForm');
      if (form) {
        console.log('[edit-recipe] submit handler attached');
        form.addEventListener('submit', function(e) {
          console.log('[edit-recipe] submit fired');
          // Sync description editor
          if (descriptionEditor) {
            descriptionEditor.codemirror.save();
          }
          // Sync all step editors
          stepEditors.forEach((editor, textarea) => {
            editor.codemirror.save();
          });
          
          // Validate that at least one step has content
          let hasStepContent = false;
          document.querySelectorAll('textarea[name="step_description[]"]').forEach(ta => {
            if (ta.value.trim().length > 0) {
              hasStepContent = true;
            }
          });
          
          if (!hasStepContent) {
            e.preventDefault();
            alert('Please add at least one step description.');
            return false;
          }
        });
      }
    </script>
    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
</html>
