<?php
session_start();
require_once __DIR__ . '/../src/helpers/flash.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reset Password · The Cookie Lovestoblog</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="w-full max-w-md mx-auto mt-10 mb-20 bg-white border border-gray-200 rounded-xl shadow-md" role="main" aria-labelledby="reset-title">
  <div class="px-6 py-5 border-b border-gray-200 bg-white rounded-t-xl">
        <h1 id="reset-title" class="text-xl font-bold tracking-tight">Reset your password</h1>
        <p class="text-sm text-gray-500">Enter your email and a new password</p>
      </div>
      <div class="p-6">
        <?php if ($msg = getFlash('success')): ?>
          <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-700 px-3 py-2 text-sm" role="status" aria-live="polite">
            <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>
        <?php if ($msg = getFlash('error')): ?>
          <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm" role="alert" aria-live="assertive">
            <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="../src/controllers/reset_password.php" novalidate class="space-y-4">
          <div>
            <label for="email" class="block text-sm text-gray-600 mb-1">Email</label>
            <input id="email" type="email" name="email" autocomplete="email" required placeholder="you@example.com" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>
          <div>
            <label for="new_password" class="block text-sm text-gray-600 mb-1">New Password</label>
            <input id="new_password" type="password" name="new_password" autocomplete="new-password" required placeholder="••••••••" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>
          <div>
            <label for="confirm_password" class="block text-sm text-gray-600 mb-1">Confirm New Password</label>
            <input id="confirm_password" type="password" name="confirm_password" autocomplete="new-password" required placeholder="••••••••" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>
          <div class="flex items-center justify-between gap-3 pt-1">
            <button class="inline-flex items-center justify-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold shadow hover:bg-[#e5573e] active:translate-y-px" type="submit">Reset password</button>
            <a class="text-[#ff6347] hover:underline text-sm" href="/blog/public/login.php">Back to login</a>
          </div>
        </form>
      </div>
      <div class="px-6 py-4 border-t border-gray-200 text-center text-sm text-gray-600">
  <a class="text-[#ff6347] hover:underline" href="/blog/public/view_recipes.php">Back to recipes</a>
      </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
</html>
