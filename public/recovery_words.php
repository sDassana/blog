<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/helpers/flash.php';
require_once __DIR__ . '/../src/helpers/recovery_words.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: /blog/public/login.php');
  exit;
}

// Handle regenerate action locally (no DB writes)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['action'] ?? '') === 'regen') {
  $candidate = pick_random_words(5);
} else {
  $candidate = pick_random_words(5);
}

$preview = $_SESSION['recovery_preview'] ?? null;
unset($_SESSION['recovery_preview']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recovery Words · The Cookie</title>
    <link rel="stylesheet" href="/blog/public/css/app.css" />
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="max-w-3xl mx-auto px-4 py-8 mb-20">
      <h1 class="text-2xl font-bold mb-2">Recovery words</h1>
      <p class="text-gray-600 mb-6">Generate five words you can use to recover your account. Store them safely. You can generate a new set or choose your own words.</p>

      <?php if ($msg = getFlash('success')): ?>
        <div class="mb-4 rounded-[15px] border border-green-200 bg-green-50 text-green-700 px-3 py-2 text-sm" role="status"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>
      <?php if ($msg = getFlash('error')): ?>
        <div class="mb-4 rounded-[15px] border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm" role="alert"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>

      <?php if (is_array($preview) && count($preview) === 5): ?>
        <div class="mb-6 p-4 rounded-[15px] border border-amber-200 bg-amber-50">
          <div class="font-semibold mb-1">Saved words (copy and store safely):</div>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($preview as $w): ?>
              <span class="inline-block rounded-[12px] bg-white border border-gray-200 px-3 py-1 text-sm"><?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <section class="mb-10">
        <h2 class="text-lg font-semibold mb-3">Suggested set</h2>
        <div class="flex flex-wrap gap-2 mb-4">
          <?php foreach ($candidate as $w): ?>
            <span class="inline-block rounded-[12px] bg-white border border-gray-200 px-3 py-1 text-sm"><?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?></span>
          <?php endforeach; ?>
        </div>
        <div class="flex items-center gap-3">
          <form method="POST" action="recovery_words.php">
            <input type="hidden" name="action" value="regen" />
            <button type="submit" class="rounded-[15px] border border-gray-300 px-3 py-1.5 hover:bg-gray-50">Regenerate</button>
          </form>
          <form method="POST" action="../src/controllers/auth/set_recovery_words.php">
            <?php foreach ($candidate as $i => $w): ?>
              <input type="hidden" name="words[]" value="<?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?>" />
            <?php endforeach; ?>
            <button type="submit" class="rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 hover:bg-[#e5573e]">Use these</button>
          </form>
        </div>
      </section>

      <section>
        <h2 class="text-lg font-semibold mb-3">Choose your own</h2>
        <form method="POST" action="../src/controllers/auth/set_recovery_words.php" class="grid grid-cols-1 md:grid-cols-5 gap-3">
          <input name="words[]" required placeholder="word 1" class="rounded-[12px] border border-gray-300 px-3 py-2" />
          <input name="words[]" required placeholder="word 2" class="rounded-[12px] border border-gray-300 px-3 py-2" />
          <input name="words[]" required placeholder="word 3" class="rounded-[12px] border border-gray-300 px-3 py-2" />
          <input name="words[]" required placeholder="word 4" class="rounded-[12px] border border-gray-300 px-3 py-2" />
          <input name="words[]" required placeholder="word 5" class="rounded-[12px] border border-gray-300 px-3 py-2" />
          <div class="md:col-span-5">
            <button type="submit" class="rounded-[15px] bg-[#ff6347] text-white px-4 py-2 hover:bg-[#e5573e]">Save my words</button>
          </div>
        </form>
        <p class="text-xs text-gray-500 mt-2">Letters only, 2–32 characters. Words are hashed before saving; they are shown here only once for you to copy.</p>
      </section>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
  </html>
