<?php
  require_once __DIR__ . '/../config/config.php';
  require_once __DIR__ . '/../src/helpers/recovery_words.php';
  $regErrors = $_SESSION['register_errors'] ?? [];
  $old = $_SESSION['register_old'] ?? ['username' => '', 'email' => ''];
  $oldWords = $_SESSION['register_words_old'] ?? [];
  $regen = isset($_GET['regen']);
  unset($_SESSION['register_errors'], $_SESSION['register_old'], $_SESSION['register_words_old']);

  // Default candidate set of five words; force new set if regen requested
  if ($regen) {
    $candidate = pick_random_words(5);
  } else {
    $candidate = !empty($oldWords) && count($oldWords) === 5 ? $oldWords : pick_random_words(5);
  }

  // Split into 3 + 2 for clearer order presentation
  $candidate_row1 = array_slice($candidate, 0, 3);
  $candidate_row2 = array_slice($candidate, 3, 2);
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
  <body class="min-h-screen bg-white text-gray-800 flex flex-col">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <div class="flex-1 w-full flex items-center justify-center">
  <main class="w-full max-w-4xl mx-auto my-0 bg-white border border-gray-200 rounded-xl shadow-md" role="main" aria-labelledby="register-title">
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
  <form action="../src/controllers/auth/register.php" method="POST" novalidate class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left panel: account fields -->
            <div class="space-y-4">
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
                <div>
                  <input id="password" type="password" name="password" autocomplete="new-password" required placeholder="••••••••"
                         pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                         title="At least 8 chars with upper, lower, number, and symbol"
                         class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
                </div>
                <p class="mt-1 text-xs text-gray-500">Use at least 8 characters including upper & lower case, a number, and a symbol.</p>
              </div>
              <div>
                <label for="confirm_password" class="block text-sm text-gray-600 mb-1">Confirm Password</label>
                <div>
                  <input id="confirm_password" type="password" name="confirm_password" autocomplete="new-password" required placeholder="••••••••"
                         pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                         title="Must match and meet strength rules"
                         class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
                </div>
                <div class="mt-1 relative">
                  <p id="pw-match-msg" class="text-xs hidden">Passwords match.</p>
                  <button type="button" id="toggle-passwords" aria-label="Show passwords" class="absolute top-0 right-0 mr-px h-7 w-7 flex items-center justify-center text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </div>
              </div>
              <div class="mt-2">
                <a class="text-[#ff6347] hover:underline text-sm" href="/blog/public/login.php">Already have an account?</a>
              </div>
              <div class="mt-1">
                <a class="text-gray-600 hover:underline text-sm" href="/blog/public/view_recipes.php">Back to recipes</a>
              </div>
            </div>

            <!-- Right panel: recovery words -->
            <div class="pt-2">
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-semibold">Recovery words (copy and store safely)</label>
                <div class="flex items-center gap-3">
                  <button type="button" id="copy-words-btn" class="text-sm rounded-[12px] border border-gray-300 px-3 py-1 hover:bg-gray-50">Copy</button>
                  <a href="register.php?regen=1" class="text-[#ff6347] text-sm hover:underline">Regenerate</a>
                </div>
              </div>
              <p class="text-xs text-gray-600 mb-3">These five words act like recovery keys. Save them securely. The order matters (1 → 5). You’ll need them to reset your password if you forget it.</p>

              <!-- Strict 3 then 2 layout using 6-column grid to keep equal widths and center bottom row -->
              <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <?php foreach ($candidate_row1 as $w): ?>
                  <div class="relative md:col-span-2">
                    <input
                      name="words[]"
                      value="<?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?>"
                      required
                      pattern="[A-Za-z]{2,32}"
                      title="Letters only, 2–32 characters"
                      class="w-full rounded-[12px] border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]"
                    />
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mt-3">
                <!-- Left spacer to center the two inputs on md -->
                <div class="hidden md:block md:col-span-1"></div>
                <?php foreach ($candidate_row2 as $w): ?>
                  <div class="relative md:col-span-2">
                    <input
                      name="words[]"
                      value="<?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?>"
                      required
                      pattern="[A-Za-z]{2,32}"
                      title="Letters only, 2–32 characters"
                      class="w-full rounded-[12px] border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]"
                    />
                  </div>
                <?php endforeach; ?>
                <!-- Right spacer to center the two inputs on md -->
                <div class="hidden md:block md:col-span-1"></div>
              </div>

              <div class="mt-2 text-xs flex items-center gap-3">
                <p class="text-gray-500">Tip: Write them down in order or store them in a secure password manager.</p>
                <span id="copy-words-status" class="hidden text-green-600">Copied</span>
              </div>
            </div>
          </div>
          <div class="pt-4">
            <div class="text-center">
              <button class="inline-flex items-center justify-center rounded-[15px] bg-[#ff6347] text-white px-5 py-2.5 font-semibold shadow hover:bg-[#e5573e] active:translate-y-px" type="submit">Create account</button>
            </div>
          </div>
        </form>
      </div>
    </main>
    </div>
    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
  <script>
    (function() {
      const btn = document.getElementById('copy-words-btn');
      const status = document.getElementById('copy-words-status');
      if (!btn) return;
      btn.addEventListener('click', async () => {
        const inputs = Array.from(document.querySelectorAll('input[name="words[]"]'));
        const words = inputs.map((el) => (el.value || '').trim()).filter(Boolean);
        if (words.length !== 5) return;
        const text = words.join(' ');
        try {
          if (navigator.clipboard && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(text);
          } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
          }
          if (status) {
            status.classList.remove('hidden');
            setTimeout(() => status.classList.add('hidden'), 1600);
          }
        } catch (e) {
          // ignore
        }
      });
      // Single toggle button that shows/hides both password fields
      const toggleBoth = document.getElementById('toggle-passwords');
      const pwInput = document.getElementById('password');
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
      const pw = document.getElementById('password');
      const cpw = document.getElementById('confirm_password');
      const msg = document.getElementById('pw-match-msg');
      function updateMatch() {
        if (!pw || !cpw || !msg) return;
        const bothFilled = pw.value.length > 0 && cpw.value.length > 0;
        const strong = pw.checkValidity();
        const match = pw.value === cpw.value;
        msg.classList.remove('hidden');
        msg.textContent = match ? (strong ? 'Passwords match.' : 'Passwords match but are not strong enough.') : 'Passwords do not match.';
        msg.className = 'mt-1 text-xs ' + (match && strong ? 'text-green-600' : 'text-red-600');
        if (!bothFilled) msg.classList.add('hidden');
        // Eye toggle stays visible at all times; no show/hide here
      }
      if (pw && cpw) {
        pw.addEventListener('input', updateMatch);
        cpw.addEventListener('input', updateMatch);
      }
    })();
  </script>
</html>