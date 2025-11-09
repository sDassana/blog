<?php
require_once __DIR__ . '/../config/config.php';
if (!isset($_SESSION['user_id'])) {
  header("Location: /blog/public/login.php");
  exit;
}

// Determine back destination based on source context
$from = $_GET['from'] ?? '';
$backUrl = '/blog/public/view_recipes.php#latest';
if ($from === 'dashboard') {
  $backUrl = '/blog/public/dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
$pageTitle = 'Add Recipe - The Cookie Lovestoblog';
$extraHead = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.js"></script>';
include __DIR__ . '/partials/header.php';
?>
  </head>
  <body class="flex flex-col min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="max-w-4xl mx-auto px-4 py-8 mb-16">
      <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
        <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center text-[#ff6347] hover:underline mb-3">Back</a>
        <h2 class="text-xl font-bold mb-4">Share a New Recipe</h2>
  <div id="step-warning" class="hidden mb-4 w-full rounded-[15px] border border-[#ff6347]/30 bg-[#fff0e9] px-6 py-5 text-sm text-gray-700 shadow-md relative" role="alert" aria-live="assertive">
          <button type="button" aria-label="Dismiss step warning" class="absolute top-2 right-2 h-6 w-6 flex items-center justify-center rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100" data-step-warning-dismiss>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/></svg>
          </button>
          <div class="pr-4">
            <p class="text-[0.65rem] font-semibold uppercase tracking-[0.25em] text-[#ff6347]">Action Required</p>
            <p class="mt-1 text-sm leading-relaxed text-gray-700">Please add at least one step description before publishing your recipe.</p>
          </div>
        </div>
        <form id="recipeForm" action="../src/controllers/add_recipe.php" method="POST" enctype="multipart/form-data" class="space-y-5">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Recipe Title</label>
            <input type="text" name="title" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>
          <div class="mb-4">
  <label for="description" class="block font-semibold mb-1">Short Description</label>
  <textarea id="description" name="description" placeholder="Write a short summary... Use Markdown for formatting"></textarea>
</div>


          <div>
            <label class="block text-sm text-gray-600 mb-1">Category</label>
            <select name="category" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]">
              <option value="Appetizer / Starter">Appetizer / Starter</option>
              <option value="Main Course">Main Course</option>
              <option value="Side Dish">Side Dish</option>
              <option value="Dessert">Dessert</option>
              <option value="Snack">Snack</option>
              <option value="Soup / Salad">Soup / Salad</option>
              <option value="Bread / Pastry">Bread / Pastry</option>
              <option value="Drink / Beverage">Drink / Beverage</option>
              <option value="Sauce / Dip / Spread">Sauce / Dip / Spread</option>
            </select>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Tags (comma-separated)</label>
            <input type="text" name="tags" placeholder="e.g. cookies, chocolate, quick" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Main Image</label>
            <div class="modern-file flex items-center">
              <input id="image_main" type="file" name="image_main" accept="image/*" class="hidden" />
              <label for="image_main" class="inline-flex items-center rounded-[15px] bg-black text-white px-4 py-2 font-semibold shadow hover:bg-black/90 cursor-pointer">
                
                Upload image
              </label>
              <span class="file-name text-sm text-gray-600 ml-3">No file chosen</span>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-2">Ingredients</h3>
            <div id="ingredients" class="space-y-2">
              <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="ingredient_name[]" placeholder="Ingredient" required class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
                <input type="text" name="ingredient_qty[]" placeholder="Quantity (optional)" class="w-full sm:w-40 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
            </div>
            <button type="button" onclick="addIngredient()" class="mt-2 inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 text-sm hover:bg-[#e5573e]">+ Add Ingredient</button>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-2">Steps</h3>
            <div id="steps" class="space-y-3">
              <div>
                <textarea name="step_description[]" placeholder="Describe this step (Markdown supported)"></textarea>
                <div class="modern-file mt-2 flex items-center">
                  <input id="step_image_0" type="file" name="step_image[]" accept="image/*" class="hidden" />
                  <label for="step_image_0" class="inline-flex items-center rounded-[15px] bg-black text-white px-4 py-2 font-semibold shadow hover:bg-black/90 cursor-pointer">
                    Upload step image
                  </label>
                  <span class="file-name text-sm text-gray-600 ml-3">No file chosen</span>
                </div>
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

    <script type="module">
      import { initModernFileInput } from './js/file-input.js';

      // Initialize SimpleMDE for description
      const descriptionEditor = new EasyMDE({
        element: document.getElementById('description'),
        placeholder: 'Write a short summary... Use Markdown for formatting',
        spellChecker: false,
        toolbar: ['bold', 'italic', 'heading', '|', 'quote', 'unordered-list', 'ordered-list', '|', 'link', 'image', '|', 'preview', 'side-by-side', 'fullscreen', '|', 'guide'],
        status: false
      });

      // Store SimpleMDE instances for step textareas
      const stepEditors = new Map();
      const stepWarning = document.getElementById('step-warning');

      function showStepWarning() {
        if (!stepWarning) return;
        stepWarning.classList.remove('hidden');
        stepWarning.classList.add('flex', 'flex-col', 'gap-3');
        const dismissBtn = stepWarning.querySelector('[data-step-warning-dismiss]');
        if (dismissBtn) {
          dismissBtn.focus();
        }
        stepWarning.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      function hideStepWarning() {
        if (!stepWarning || stepWarning.classList.contains('hidden')) return;
        stepWarning.classList.add('hidden');
        stepWarning.classList.remove('flex', 'flex-col', 'gap-3');
      }

      const stepWarningDismiss = stepWarning ? stepWarning.querySelector('[data-step-warning-dismiss]') : null;
      if (stepWarningDismiss) {
        stepWarningDismiss.addEventListener('click', hideStepWarning);
      }

      function attachStepEditorListeners(editor) {
        if (!editor) return;
        editor.codemirror.on('change', () => {
          if (editor.value().trim().length > 0) {
            hideStepWarning();
          }
        });
      }

      // Initialize SimpleMDE for initial step
      const initialStepTextarea = document.querySelector('#steps textarea[name="step_description[]"]');
      if (initialStepTextarea) {
        initialStepTextarea.addEventListener('input', hideStepWarning);
        const editor = new EasyMDE({
          element: initialStepTextarea,
          placeholder: 'Describe this step (Markdown supported)',
          spellChecker: false,
          toolbar: ['bold', 'italic', '|', 'unordered-list', 'ordered-list', '|', 'link', 'image', '|', 'preview', 'guide'],
          status: false
        });
        stepEditors.set(initialStepTextarea, editor);
        attachStepEditorListeners(editor);
      }

      function addIngredient() {
        const div = document.createElement('div');
        div.className = 'flex flex-col sm:flex-row gap-2';
        // Do not mark dynamically added ingredient name as required; the first row enforces at least one ingredient
        div.innerHTML = '<input type="text" name="ingredient_name[]" placeholder="Ingredient" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />' +
                        '<input type="text" name="ingredient_qty[]" placeholder="Quantity (optional)" class="w-full sm:w-40 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />';
        document.getElementById('ingredients').appendChild(div);
      }

      function addStep() {
        const div = document.createElement('div');
        const stepInputId = 'step_image_' + Date.now();
        const textareaId = 'step_textarea_' + Date.now();
        div.innerHTML = '<textarea id="' + textareaId + '" name="step_description[]" placeholder="Describe this step (Markdown supported)"></textarea>' +
                        '<div class="modern-file mt-2 flex items-center">' +
                          '<input id="' + stepInputId + '" type="file" name="step_image[]" accept="image/*" class="hidden" />' +
                          '<label for="' + stepInputId + '" class="inline-flex items-center rounded-[15px] bg-black text-white px-4 py-2 font-semibold shadow hover:bg-black/90 cursor-pointer">' +
                            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 mr-2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7.5h3l1.5-2.25h9L18 7.5h3A1.5 1.5 0 0122.5 9v9A1.5 1.5 0 0121 19.5H3A1.5 1.5 0 011.5 18V9A1.5 1.5 0 013 7.5zm9 9a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"/></svg>' +
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
        attachStepEditorListeners(editor);
        textarea.addEventListener('input', hideStepWarning);

        // Initialize modern file input for the newly added step
        initModernFileInput(div);
      }

      // Expose add functions to global for onclick handlers
      window.addIngredient = addIngredient;
      window.addStep = addStep;

      // Initialize modern file inputs on load
      initModernFileInput(document);

      // Ensure SimpleMDE syncs values before form submission (bind to the correct form)
      const form = document.getElementById('recipeForm');
      if (form) {
        console.log('[recipe] submit handler attached');
        form.addEventListener('submit', function(e) {
          console.log('[recipe] submit fired');
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
            showStepWarning();
            return false;
          }
          hideStepWarning();
        });
      }
    </script>
    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
</html>
