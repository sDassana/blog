<?php
  session_start();
  $regErrors = $_SESSION['register_errors'] ?? [];
  $old = $_SESSION['register_old'] ?? ['username' => '', 'email' => ''];
  unset($_SESSION['register_errors'], $_SESSION['register_old']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Create account · The Cookie Lovestoblog</title>
  <link rel="stylesheet" href="/blog/public/css/app.css" />
  
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="w-full max-w-md mx-auto mt-10 mb-20 bg-white border border-gray-200 rounded-xl shadow-md" role="main" aria-labelledby="register-title">
  <div class="px-6 py-5 border-b border-gray-200 bg-white rounded-t-xl">
        <h1 id="register-title" class="text-xl font-bold tracking-tight">Create your account</h1>
        <p class="text-sm text-gray-500">Join to start sharing your recipes</p>
      </div>
      <div class="p-6">
        <?php if (!empty($regErrors)): ?>
          <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm" role="alert" aria-live="polite">
            <strong class="font-semibold">Please fix the following:</strong>
            <ul class="list-disc pl-5 mt-1">
              <?php foreach ($regErrors as $e): ?>
                <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <form action="../src/controllers/register.php" method="POST" novalidate class="space-y-4">
          <div>
            <label for="username" class="block text-sm text-gray-600 mb-1">Username</label>
            <input id="username" type="text" name="username" autocomplete="username" required placeholder="yourname" value="<?php echo htmlspecialchars($old['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>
          <div>
            <label for="email" class="block text-sm text-gray-600 mb-1">Email</label>
            <input id="email" type="email" name="email" autocomplete="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>
          <div>
            <label for="password" class="block text-sm text-gray-600 mb-1">Password</label>
            <input id="password" type="password" name="password" autocomplete="new-password" required placeholder="••••••••" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>
          <div>
            <label for="confirm_password" class="block text-sm text-gray-600 mb-1">Confirm Password</label>
            <input id="confirm_password" type="password" name="confirm_password" autocomplete="new-password" required placeholder="••••••••" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>
          <div class="flex items-center justify-between gap-3 pt-1">
            <button class="inline-flex items-center justify-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold shadow hover:bg-[#e5573e] active:translate-y-px" type="submit">Create account</button>
            <a class="text-[#ff6347] hover:underline text-sm" href="/blog/public/login.php">Already have an account?</a>
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