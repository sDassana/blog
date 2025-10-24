<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /blog/public/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add New Recipe · The Cookie Lovestoblog</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="max-w-4xl mx-auto px-4 py-8 mb-16">
      <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
        <h2 class="text-xl font-bold mb-4">Share a New Recipe</h2>
        <form action="../src/controllers/add_recipe.php" method="POST" enctype="multipart/form-data" class="space-y-5">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Recipe Title</label>
            <input type="text" name="title" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Category</label>
            <select name="category" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
              <option value="Main Dish">Main Dish</option>
              <option value="Dessert">Dessert</option>
              <option value="Drink">Drink</option>
            </select>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Tags (comma-separated)</label>
            <input type="text" name="tags" placeholder="e.g. cookies, chocolate, quick" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Main Image</label>
            <input type="file" name="image_main" accept="image/*" class="block w-full text-sm text-gray-700" />
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-2">Ingredients</h3>
            <div id="ingredients" class="space-y-2">
              <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="ingredient_name[]" placeholder="Ingredient" required class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
                <input type="text" name="ingredient_qty[]" placeholder="Quantity" required class="w-full sm:w-40 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
            </div>
            <button type="button" onclick="addIngredient()" class="mt-2 inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 text-sm hover:bg-[#e5573e]">+ Add Ingredient</button>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-2">Steps</h3>
            <div id="steps" class="space-y-3">
              <div>
                <textarea name="step_description[]" rows="3" placeholder="Describe this step" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]"></textarea>
                <input type="file" name="step_image[]" accept="image/*" class="mt-2 block w-full text-sm text-gray-700" />
              </div>
            </div>
            <button type="button" onclick="addStep()" class="mt-2 inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 text-sm hover:bg-[#e5573e]">+ Add Step</button>
          </div>

          <div class="pt-2">
            <button type="submit" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold shadow hover:bg-[#e5573e]">Publish Recipe</button>
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
  div.innerHTML = '<textarea name="step_description[]" rows="3" placeholder="Describe this step" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]"></textarea>' +
                        '<input type="file" name="step_image[]" accept="image/*" class="mt-2 block w-full text-sm text-gray-700" />';
        document.getElementById('steps').appendChild(div);
      }
    </script>
    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
</html>
