<?php
// Branded login page that displays flash messages and posts to the auth controller.
require_once __DIR__ . '/../config/config.php';
$errors = $_SESSION['login_errors'] ?? [];
$old_email = $_SESSION['old_email'] ?? '';
$success = getFlash('success');
unset($_SESSION['login_errors'], $_SESSION['old_email']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $pageTitle = 'Login · The Cookie Lovestoblog'; include __DIR__ . '/partials/header.php'; ?>
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="w-full max-w-md mx-auto mt-10 mb-20 bg-white border border-gray-200 rounded-xl shadow-md">
  <div class="px-6 py-5 border-b border-gray-200 bg-white rounded-t-xl">
        <h1 id="login-title" class="text-xl font-bold tracking-tight">Welcome back</h1>
        <p class="text-sm text-gray-500">Sign in to continue to your account</p>
      </div>

      <div class="p-6">
        <?php if (!empty($success)): ?>
          <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-700 px-3 py-2 text-sm" role="status" aria-live="polite">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
          <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm" role="alert" aria-live="polite">
            <strong class="font-semibold">We couldn’t sign you in:</strong>
            <ul class="list-disc pl-5 mt-1">
              <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

  <form action="../src/controllers/auth/login.php" method="POST" novalidate class="space-y-4">
          <div>
            <label for="email" class="block text-sm text-gray-600 mb-1">Email</label>
            <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($old_email, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" required placeholder="you@example.com" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>

          <div>
            <label for="password" class="block text-sm text-gray-600 mb-1">Password</label>
            <div>
              <input id="password" type="password" name="password" autocomplete="current-password" required placeholder="••••••••" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
            </div>
            <div class="mt-1 relative">
              <button type="button" id="toggle-password" aria-label="Show password" class="absolute top-0 right-0 mr-px h-7 w-7 flex items-center justify-center text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </button>
            </div>
          </div>

          <p class="text-center text-sm">
            <a href="forgot_password.php" class="text-[#ff6347] hover:underline">Forgot Password?</a>
          </p>

          <div class="flex items-center justify-between gap-3 pt-1">
            <button class="inline-flex items-center justify-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold shadow hover:bg-[#e5573e] active:translate-y-px" type="submit">Sign in</button>
            <a class="text-[#ff6347] hover:underline text-sm" href="/blog/public/register.php">Create account</a>
          </div>
        </form>
      </div>

      <div class="px-6 py-4 border-t border-gray-200 text-center text-sm text-gray-600">
  <a class="text-[#ff6347] hover:underline" href="/blog/public/view_recipes.php">Back to recipes</a>
      </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
  <script>
    (function() {
      // Password visibility toggle
      const toggleBtn = document.getElementById('toggle-password');
      const pwInput = document.getElementById('password');
      if (toggleBtn && pwInput) {
        toggleBtn.addEventListener('click', () => {
          const show = pwInput.type === 'password';
          pwInput.type = show ? 'text' : 'password';
          toggleBtn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
      }
    })();
  </script>
</html>
