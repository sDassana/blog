<?php
// Registration page combining account fields with recovery word generation.
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
    <?php $pageTitle = 'Create account · The Cookie Lovestoblog'; include __DIR__ . '/partials/header.php'; ?>
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <div class="w-full px-4 sm:px-6">
      <main class="w-full max-w-4xl mx-auto mt-10 mb-16 bg-white border border-gray-200 rounded-xl shadow-md" role="main" aria-labelledby="register-title">
        <div class="px-6 py-6 border-b border-gray-200 bg-white rounded-t-xl">
          <h1 id="register-title" class="text-2xl font-semibold tracking-tight">Create your account</h1>
          <p class="text-sm text-gray-500 mt-1">Join to start sharing your recipes</p>
        </div>
        <div class="px-6 py-8">
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
          <form action="../src/controllers/auth/register.php" method="POST" novalidate class="space-y-6">
          <div class="grid grid-cols-1 gap-y-10 md:grid-cols-2 md:gap-x-10">
            <!-- Left panel: account fields -->
            <div class="space-y-6">
              <div>
                <label for="username" class="form-label">Username</label>
                <input id="username" type="text" name="username" autocomplete="username" required placeholder="yourname" value="<?php echo htmlspecialchars($old['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control" />
              </div>
              <div>
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" autocomplete="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control" />
              </div>
              <div>
                <label for="password" class="form-label">Password</label>
                <div>
                  <input id="password" type="password" name="password" autocomplete="new-password" required placeholder="••••••••"
                         pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                         title="At least 8 chars with upper, lower, number, and symbol"
                         class="form-control" />
                </div>
                <div class="mt-1 relative">
                  <p id="pw-match-msg" class="form-hint hidden">Passwords match.</p>
                  <button type="button" id="toggle-passwords" aria-label="Show passwords" class="absolute top-0 right-0 mr-px h-7 w-7 flex items-center justify-center text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </div>
                <p class="form-hint">Use at least 8 characters including upper & lower case, a number, and a symbol.</p>
              </div>
              <div>
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div>
                  <input id="confirm_password" type="password" name="confirm_password" autocomplete="new-password" required placeholder="••••••••"
                         pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                         title="Must match and meet strength rules"
                         class="form-control" />
                </div>
              </div>
            </div>

            <!-- Right panel: recovery words -->
            <div class="pt-2 space-y-6">
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-semibold">Recovery words (copy and store safely)</label>
                <div class="flex items-center gap-3">
                  <button type="button" id="copy-words-btn" class="text-sm rounded-[12px] border border-gray-300 px-3 py-1 hover:bg-gray-50">Copy</button>
                  <a href="register.php?regen=1" class="text-[#ff6347] text-sm hover:underline">Regenerate</a>
                </div>
              </div>
              <p class="form-hint">These five words act like recovery keys. Save them securely. The order matters (1 → 5). You’ll need them to reset your password if you forget it.</p>

              <!-- Strict 3 then 2 layout using 6-column grid to keep equal widths and center bottom row -->
              <div class="grid grid-cols-6 gap-3">
                <?php foreach ($candidate_row1 as $w): ?>
                  <div class="relative col-span-2 flex justify-center">
                    <input
                      name="words[]"
                      value="<?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?>"
                      required
                      pattern="[A-Za-z]{2,32}"
                      title="Letters only, 2–32 characters"
                      class="form-control max-w-[200px] text-center"
                    />
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="grid grid-cols-6 gap-3 mt-3">
                <!-- Left spacer to center the two inputs -->
                <div class="col-span-1"></div>
                <?php foreach ($candidate_row2 as $w): ?>
                  <div class="relative col-span-2 flex justify-center">
                    <input
                      name="words[]"
                      value="<?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?>"
                      required
                      pattern="[A-Za-z]{2,32}"
                      title="Letters only, 2–32 characters"
                      class="form-control max-w-[200px] text-center"
                    />
                  </div>
                <?php endforeach; ?>
                <!-- Right spacer to center the two inputs -->
                <div class="col-span-1"></div>
              </div>

              <div class="flex items-start gap-3">
                <p class="form-hint flex-1">Tip: Write them down in order or store them in a secure password manager.</p>
                <span id="copy-words-status" class="hidden text-xs text-green-600">Copied</span>
              </div>

              <div class="mt-6">
                <div class="flex justify-center">
                  <button class="btn-primary" type="submit">Create account</button>
                </div>
                <div class="mt-3 flex w-full items-center justify-between text-sm">
                  <a class="text-[#ff6347] hover:underline" href="/blog/public/login.php">Already have an account?</a>
                  <a class="text-gray-600 hover:underline" href="/blog/public/view_recipes.php">Back to recipes</a>
                </div>
              </div>
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
        if (!bothFilled) {
          msg.className = 'form-hint hidden';
          msg.textContent = '';
          return;
        }
  msg.classList.remove('hidden');
  msg.textContent = match ? (strong ? 'Passwords match.' : 'Passwords match but are not strong enough.') : 'Passwords do not match.';
  msg.className = 'form-hint ' + (match && strong ? 'text-green-600' : (match ? 'text-[#ff6347]' : 'text-red-600'));
        // Eye toggle stays visible at all times; no show/hide here
      }
      if (pw && cpw) {
        pw.addEventListener('input', updateMatch);
        cpw.addEventListener('input', updateMatch);
      }
    })();
  </script>
</html>