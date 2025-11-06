<?php
require_once __DIR__ . '/../config/config.php';

// You can allow recovery without being logged in; this page is for locked-out users.
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recover Account · The Cookie Lovestoblog</title>
    <link rel="stylesheet" href="/blog/public/css/app.css" />
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="w-full max-w-xl mx-auto mt-10 mb-20 bg-white border border-gray-200 rounded-xl shadow-md" role="main" aria-labelledby="recover-title">
      <div class="px-6 py-5 border-b border-gray-200 bg-white rounded-t-xl">
        <h1 id="recover-title" class="text-xl font-bold tracking-tight">Recover your account</h1>
        <p class="text-sm text-gray-500">Enter your email, your five recovery words in order, and a new password.</p>
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

        <form method="POST" action="../src/controllers/auth/recover_account.php" novalidate class="space-y-4">
          <div>
            <label for="email" class="block text-sm text-gray-600 mb-1">Email</label>
            <input id="email" type="email" name="email" autocomplete="email" required placeholder="you@example.com" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Recovery words (in order)</label>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
              <input name="words[]" required placeholder="word 1" class="rounded-[12px] border border-gray-300 px-3 py-2" />
              <input name="words[]" required placeholder="word 2" class="rounded-[12px] border border-gray-300 px-3 py-2" />
              <input name="words[]" required placeholder="word 3" class="rounded-[12px] border border-gray-300 px-3 py-2" />
              <input name="words[]" required placeholder="word 4" class="rounded-[12px] border border-gray-300 px-3 py-2" />
              <input name="words[]" required placeholder="word 5" class="rounded-[12px] border border-gray-300 px-3 py-2" />
            </div>
            <p class="text-xs text-gray-500 mt-1">Letters only, 2–32 characters each.</p>
          </div>
          <div>
            <label for="new_password" class="block text-sm text-gray-600 mb-1">New Password</label>
            <div>
              <input id="new_password" type="password" name="new_password" autocomplete="new-password" required placeholder="••••••••"
                     pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                     title="At least 8 chars with upper, lower, number, and symbol"
                     class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
            </div>
            <div class="mt-1 relative">
              <p class="text-xs text-gray-500">Use at least 8 characters including upper & lower case, a number, and a symbol.</p>
              <button type="button" id="toggle-passwords" aria-label="Show passwords" class="absolute top-0 right-0 mr-px h-7 w-7 flex items-center justify-center text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </button>
            </div>
          </div>
          <div>
            <label for="confirm_password" class="block text-sm text-gray-600 mb-1">Confirm New Password</label>
            <div>
              <input id="confirm_password" type="password" name="confirm_password" autocomplete="new-password" required placeholder="••••••••"
                     pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                     title="Must match and meet strength rules"
                     class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
            </div>
            <div class="mt-1 relative">
              <p id="match-msg" class="text-xs hidden">Passwords match.</p>
            </div>
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
  <script>
    (function() {
      // Single toggle button that shows/hides both password fields
      const toggleBoth = document.getElementById('toggle-passwords');
      const pwInput = document.getElementById('new_password');
      const cpwInput = document.getElementById('confirm_password');
      if (toggleBoth && pwInput && cpwInput) {
        toggleBoth.addEventListener('click', () => {
          const show = pwInput.type === 'password';
          pwInput.type = show ? 'text' : 'password';
          cpwInput.type = show ? 'text' : 'password';
          toggleBoth.setAttribute('aria-label', show ? 'Hide passwords' : 'Show passwords');
        });
      }

      // Match feedback
      const pw = document.getElementById('new_password');
      const cpw = document.getElementById('confirm_password');
      const msg = document.getElementById('match-msg');
      function updateMatch() {
        if (!pw || !cpw || !msg) return;
        const bothFilled = pw.value.length > 0 && cpw.value.length > 0;
        const strong = pw.checkValidity();
        const match = pw.value === cpw.value;
        msg.classList.remove('hidden');
        msg.textContent = match ? (strong ? 'Passwords match.' : 'Passwords match but are not strong enough.') : 'Passwords do not match.';
        msg.className = 'mt-1 text-xs ' + (match && strong ? 'text-green-600' : 'text-red-600');
        if (!bothFilled) msg.classList.add('hidden');
      }
      if (pw && cpw) {
        pw.addEventListener('input', updateMatch);
        cpw.addEventListener('input', updateMatch);
      }
    })();
  </script>
</html>
          if (!bothFilled) nmsg.classList.add('hidden');
        }
        if (npw && ncf) { npw.addEventListener('input', updateMatch); ncf.addEventListener('input', updateMatch); }
      })();
    </script>
    </html>
