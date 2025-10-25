<?php
session_start();
require_once __DIR__ . '/../src/helpers/flash.php';
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Load current user data
$user = null;
try {
  $stmt = $pdo->prepare("SELECT id, username, email FROM `user` WHERE id = :id");
  $stmt->execute(['id' => $_SESSION['user_id']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
  $user = [];
}

$displayName = $user['username'] ?? ($_SESSION['username'] ?? 'User');
$email = $user['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard · The Cookie Lovestoblog</title>
  <link rel="stylesheet" href="/blog/public/css/app.css" />
    
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="max-w-5xl mx-auto px-4 py-8 mb-16">
      <?php if ($msg = getFlash('success')): ?>
        <div class="mb-4 rounded-[15px] border border-green-200 bg-green-50 text-green-700 px-3 py-2 text-sm"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>
      <?php if ($msg = getFlash('error')): ?>
        <div class="mb-4 rounded-[15px] border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <h1 class="text-2xl font-bold text-center mb-6">Welcome, <?= htmlspecialchars($displayName) ?></h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Quick Actions (vertical menu) -->
  <section id="qa-panel" class="md:col-span-1 bg-white border border-gray-200 rounded-[15px] p-4 h-max">
          <h2 class="text-lg font-semibold mb-3">Quick Actions</h2>
          <nav class="flex flex-col">
            <a href="add_recipe.php?from=dashboard" class="flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-[#ff6347]"><path d="M12 4.5a.75.75 0 01.75.75V11h5.75a.75.75 0 010 1.5H12.75v5.75a.75.75 0 01-1.5 0V12.5H5.5a.75.75 0 010-1.5h5.75V5.25A.75.75 0 0112 4.5z"/></svg>
              <span>Add Recipes</span>
            </a>
            <button type="button" id="qa-my-recipes" class="text-left flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-[#ff6347]"><path d="M3.75 5.25a.75.75 0 01.75-.75h15a.75.75 0 01.75.75v12a.75.75 0 01-.75.75h-15a.75.75 0 01-.75-.75v-12zM5 6.5v9h14v-9H5z"/></svg>
              <span>My Recipes</span>
            </button>
            <button type="button" id="qa-saved" class="text-left flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-[#ff6347]"><path d="M17.593 3.322a2.25 2.25 0 012.657 3.53l-7.5 6.25a2.25 2.25 0 01-2.9 0l-7.5-6.25A2.25 2.25 0 014.407 3.322L12 9.17l5.593-4.848z"/></svg>
              <span>Saved Recipes</span>
            </button>
            <button type="button" id="qa-settings" class="text-left flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-[#ff6347]"><path fill-rule="evenodd" d="M11.25 2.25a.75.75 0 01.75.75v1.012a7.5 7.5 0 014.918 2.07l.716-.716a.75.75 0 011.061 1.06l-.716.717A7.5 7.5 0 0119.988 12h1.012a.75.75 0 010 1.5h-1.012a7.5 7.5 0 01-2.07 4.918l.716.716a.75.75 0 11-1.06 1.061l-.717-.716A7.5 7.5 0 0112.75 19.988V21a.75.75 0 01-1.5 0v-1.012a7.5 7.5 0 01-4.918-2.07l-.716.716a.75.75 0 11-1.061-1.06l.716-.717A7.5 7.5 0 014.012 13.5H3a.75.75 0 010-1.5h1.012a7.5 7.5 0 012.07-4.918l-.716-.716a.75.75 0 111.06-1.061l.717.716A7.5 7.5 0 0111.25 4.012V3a.75.75 0 01.75-.75zm.75 6a4.5 4.5 0 100 9 4.5 4.5 0 000-9z" clip-rule="evenodd"/></svg>
              <span>General Settings</span>
            </button>
            <a href="about.php" class="flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5 text-[#ff6347]"><path d="M11.484 3.25a8.25 8.25 0 108.266 8.745.75.75 0 10-1.494-.14 6.75 6.75 0 11-6.772-7.145.75.75 0 10-.25-1.46z"/><path d="M12 8.25a.75.75 0 01.75.75v.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 3a.75.75 0 01.75.75v3a.75.75 0 01-1.5 0v-3A.75.75 0 0112 11.25z"/></svg>
              <span>About Us</span>
            </a>
            <a href="../src/controllers/auth/logout.php" class="flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-[#ff6347]"><path fill-rule="evenodd" d="M3.75 4.5A2.25 2.25 0 016 2.25h5.25a.75.75 0 010 1.5H6a.75.75 0 00-.75.75v15a.75.75 0 00.75.75h5.25a.75.75 0 010 1.5H6A2.25 2.25 0 013.75 20.25v-15zm14.47 6.53a.75.75 0 000-1.06l-3-3a.75.75 0 10-1.06 1.06l1.72 1.72H10.5a.75.75 0 000 1.5h5.38l-1.72 1.72a.75.75 0 001.06 1.06l3-3z" clip-rule="evenodd"/></svg>
              <span>Logout</span>
            </a>
          </nav>
        </section>

        <!-- Right: Notifications / Settings panel -->
        <section class="md:col-span-2 bg-white border border-gray-200 rounded-[15px] p-4 min-h-[320px] flex flex-col" id="panel">
          <!-- Notifications (default) -->
          <div id="view-notifications" class="h-full flex flex-col">
            <div class="flex items-center justify-between mb-3 flex-none">
              <h2 class="text-lg font-semibold">Notifications</h2>
            </div>
            <?php
              // Build a merged notifications feed (last 10), including:
              // - Likes on my recipes
              // - My likes (on any recipe)
              // - Comments on my recipes
              // - My comments (on any recipe)
              $events = [];
              $uid = $_SESSION['user_id'];
              try {
                // Likes on my recipes by anyone (including self)
                $stmt1 = $pdo->prepare(
                  "SELECT rl.created_at, u.username AS actor, r.title AS recipe, r.user_id AS owner_id
                   FROM recipe_likes rl
                   JOIN recipe r ON r.id = rl.recipe_id
                   JOIN user u ON u.id = rl.user_id
                   WHERE r.user_id = :uid
                   ORDER BY rl.created_at DESC
                   LIMIT 20"
                );
                $stmt1->execute(['uid' => $uid]);
                foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
                  $self = ($row['actor'] === ($user['username'] ?? '')) || ($row['owner_id'] == $uid);
                  $msg = ($row['owner_id'] == $uid && $row['actor'] === ($user['username'] ?? ''))
                    ? "You liked your recipe " . $row['recipe']
                    : $row['actor'] . " liked your recipe " . $row['recipe'];
                  $events[] = ['created_at' => $row['created_at'], 'message' => $msg];
                }

                // My likes on any recipe (if it's not mine, phrase accordingly)
                $stmt2 = $pdo->prepare(
                  "SELECT rl.created_at, r.title AS recipe, r.user_id AS owner_id
                   FROM recipe_likes rl
                   JOIN recipe r ON r.id = rl.recipe_id
                   WHERE rl.user_id = :uid
                   ORDER BY rl.created_at DESC
                   LIMIT 20"
                );
                $stmt2->execute(['uid' => $uid]);
                foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
                  $msg = ($row['owner_id'] == $uid)
                    ? "You liked your recipe " . $row['recipe']
                    : "You liked recipe " . $row['recipe'];
                  $events[] = ['created_at' => $row['created_at'], 'message' => $msg];
                }

                // Comments on my recipes
                $stmt3 = $pdo->prepare(
                  "SELECT c.created_at, u.username AS actor, r.title AS recipe, r.user_id AS owner_id
                   FROM recipe_comments c
                   JOIN recipe r ON r.id = c.recipe_id
                   JOIN user u ON u.id = c.user_id
                   WHERE r.user_id = :uid
                   ORDER BY c.created_at DESC
                   LIMIT 20"
                );
                $stmt3->execute(['uid' => $uid]);
                foreach ($stmt3->fetchAll(PDO::FETCH_ASSOC) as $row) {
                  $msg = ($row['actor'] === ($user['username'] ?? ''))
                    ? "You commented your recipe " . $row['recipe']
                    : $row['actor'] . " commented on your recipe " . $row['recipe'];
                  $events[] = ['created_at' => $row['created_at'], 'message' => $msg];
                }

                // My comments on any recipe
                $stmt4 = $pdo->prepare(
                  "SELECT c.created_at, r.title AS recipe, r.user_id AS owner_id
                   FROM recipe_comments c
                   JOIN recipe r ON r.id = c.recipe_id
                   WHERE c.user_id = :uid
                   ORDER BY c.created_at DESC
                   LIMIT 20"
                );
                $stmt4->execute(['uid' => $uid]);
                foreach ($stmt4->fetchAll(PDO::FETCH_ASSOC) as $row) {
                  $msg = ($row['owner_id'] == $uid)
                    ? "You commented your recipe " . $row['recipe']
                    : "You commented on recipe " . $row['recipe'];
                  $events[] = ['created_at' => $row['created_at'], 'message' => $msg];
                }
              } catch (Exception $e) {
                // swallow
              }

              // Merge session last_action as most recent pseudo-event if present
              if (!empty($_SESSION['last_action'])) {
                $events[] = ['created_at' => date('Y-m-d H:i:s'), 'message' => $_SESSION['last_action']];
              }

              // Sort desc by created_at and limit 10
              usort($events, function($a, $b) {
                return strtotime($b['created_at']) <=> strtotime($a['created_at']);
              });
              $events = array_slice($events, 0, 10);
            ?>
            <div id="notifications-scroll" class="flex-1 overflow-y-auto pr-1">
              <?php if (empty($events)): ?>
                <p class="text-gray-600">No new notifications yet. Your likes, comments, and account changes will appear here.</p>
              <?php else: ?>
                <ul class="space-y-2">
                  <?php foreach ($events as $ev): ?>
                    <li class="rounded-[15px] border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
                      <?= htmlspecialchars($ev['message']) ?>
                      <span class="text-gray-500">(<?= htmlspecialchars($ev['created_at']) ?>)</span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </div>

          <!-- Saved Recipes -->
          <div id="view-saved" class="hidden h-full flex flex-col">
            <div class="flex items-center justify-between mb-3 flex-none">
              <h2 class="text-lg font-semibold">Saved Recipes</h2>
            </div>
            <?php
              // Fetch current user's saved recipes
              $saved = [];
              try {
                $stmt = $pdo->prepare(
                  "SELECT rs.created_at AS saved_at, r.id, r.title, r.category
                   FROM recipe_saves rs
                   JOIN recipe r ON r.id = rs.recipe_id
                   WHERE rs.user_id = :uid
                   ORDER BY rs.created_at DESC
                   LIMIT 50"
                );
                $stmt->execute(['uid' => $_SESSION['user_id']]);
                $saved = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (Exception $e) { $saved = []; }
            ?>
            <div id="saved-scroll" class="flex-1 overflow-y-auto pr-1">
              <?php if (empty($saved)): ?>
                <p class="text-gray-600">You haven't saved any recipes yet.</p>
              <?php else: ?>
                <ul class="space-y-2">
                  <?php foreach ($saved as $s): ?>
                    <li class="rounded-[15px] border border-gray-200 bg-gray-50 px-3 py-2 text-sm flex items-center justify-between">
                      <div>
                        <a href="recipe.php?id=<?= (int)$s['id'] ?>" class="text-gray-800 hover:underline font-medium">
                          <?= htmlspecialchars($s['title']) ?>
                        </a>
                        <span class="ml-2 text-xs text-[#ff6347]"><em><?= htmlspecialchars($s['category']) ?></em></span>
                      </div>
                      <span class="text-gray-500 text-xs">Saved: <?= htmlspecialchars($s['saved_at']) ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </div>

          <!-- Settings menu -->
          <div id="view-settings-menu" class="hidden">
            <div class="flex items-center gap-3 mb-3">
              <button type="button" id="btn-back-to-notifications" class="text-sm text-[#ff6347] hover:underline">Back</button>
              <h2 class="text-lg font-semibold">General Settings</h2>
            </div>
            <ul class="space-y-2">
              <li><button type="button" data-target="form-name" class="w-full text-left px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">Change Display Name</button></li>
              <li><button type="button" data-target="form-email" class="w-full text-left px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">Change Email</button></li>
              <li><button type="button" data-target="form-password" class="w-full text-left px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">Change Password</button></li>
            </ul>
          </div>

          <!-- Settings forms (hidden by default) -->
          <div id="view-settings-form" class="hidden">
            <div class="flex items-center gap-3 mb-3">
              <button type="button" id="btn-back-to-settings" class="text-sm text-[#ff6347] hover:underline">Back</button>
              <h2 id="settings-form-title" class="text-lg font-semibold"></h2>
            </div>

            <!-- Change Display Name -->
            <form id="form-name" action="../src/controllers/update_profile.php" method="POST" class="space-y-4 hidden">
              <div>
                <label class="block text-sm text-gray-600 mb-1">Display Name</label>
                <input type="text" name="username" value="<?= htmlspecialchars($displayName) ?>" required class="w-full rounded-[15px] border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
              <div>
                <button type="submit" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold hover:bg-[#e5573e]">Save</button>
              </div>
            </form>

            <!-- Change Email -->
            <form id="form-email" action="../src/controllers/update_email.php" method="POST" class="space-y-4 hidden">
              <div>
                <label class="block text-sm text-gray-600 mb-1">New Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required class="w-full rounded-[15px] border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
              <div>
                <label class="block text-sm text-gray-600 mb-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full rounded-[15px] border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
              <div>
                <button type="submit" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold hover:bg-[#e5573e]">Update Email</button>
              </div>
            </form>

            <!-- Change Password -->
            <form id="form-password" action="../src/controllers/update_password.php" method="POST" class="space-y-4 hidden">
              <div>
                <label class="block text-sm text-gray-600 mb-1">Current Password</label>
                <div>
                  <input id="cp-current" type="password" name="current_password" required class="w-full rounded-[15px] border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
                </div>
                <div class="mt-1 relative">
                  <button type="button" id="toggle-cp-current" aria-label="Show password" class="absolute top-0 right-0 mr-px h-7 w-7 flex items-center justify-center text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </div>
              </div>
              <div>
                <label class="block text-sm text-gray-600 mb-1">New Password</label>
                <div>
                  <input id="cp-new" type="password" name="new_password" required
                         pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                         title="At least 8 chars with upper, lower, number, and symbol"
                         class="w-full rounded-[15px] border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
                </div>
                <div class="mt-1 relative">
                  <p class="text-xs text-gray-500">Use at least 8 characters including upper & lower case, a number, and a symbol.</p>
                  <button type="button" id="toggle-cp-new" aria-label="Show password" class="absolute top-0 right-0 mr-px h-7 w-7 flex items-center justify-center text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </div>
              </div>
              <div>
                <label class="block text-sm text-gray-600 mb-1">Confirm New Password</label>
                <div>
                  <input id="cp-confirm" type="password" name="confirm_password" required
                         pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                         title="Must match and meet strength rules"
                         class="w-full rounded-[15px] border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
                </div>
                <div class="mt-1 relative">
                  <p id="cp-match-msg" class="text-xs hidden">Passwords match.</p>
                  <button type="button" id="toggle-cp-confirm" aria-label="Show password" class="absolute top-0 right-0 mr-px h-7 w-7 flex items-center justify-center text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </div>
              </div>
              <div>
                <button type="submit" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold hover:bg-[#e5573e]">Update Password</button>
              </div>
            </form>
          </div>
        </section>
      </div>

      <!-- My Recipes (tiles) -->
      <section class="mt-8" id="my-recipes">
        <h2 class="text-lg font-semibold mb-3">My Recipes</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <?php
            try {
              $stmt = $pdo->prepare("SELECT id, title, category, image_main, created_at FROM recipe WHERE user_id = :uid ORDER BY created_at DESC LIMIT 12");
              $stmt->execute(['uid' => $_SESSION['user_id']]);
              $mine = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) { $mine = []; }

            if (empty($mine)) {
              echo '<p class="text-gray-600">You have not added any recipes yet.</p>';
            } else {
              foreach ($mine as $r) {
                $img = $r['image_main'] ? '../public/' . htmlspecialchars($r['image_main'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/640x360?text=No+Image';
                $title = htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8');
                $cat = htmlspecialchars($r['category'], ENT_QUOTES, 'UTF-8');
                $id = (int)$r['id'];
                echo "
                  <a href='recipe.php?id={$id}' class='group block bg-white border border-gray-200 rounded-[15px] shadow hover:shadow-md transition overflow-hidden'>
                    <div class='aspect-video bg-gray-100 overflow-hidden'>
                      <img src='{$img}' alt='Recipe Image' class='w-full h-full object-cover group-hover:scale-[1.02] transition' />
                    </div>
                    <div class='p-4'>
                      <h3 class='font-semibold text-lg mb-1'>{$title}</h3>
                      <p class='text-xs text-[#ff6347]'><em>{$cat}</em></p>
                    </div>
                  </a>
                ";
              }
            }
          ?>
        </div>
      </section>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script>
  const qaPanel = document.getElementById('qa-panel');
  const rightPanel = document.getElementById('panel');
  const notifScroll = document.getElementById('notifications-scroll');
      const viewSaved = document.getElementById('view-saved');
      const viewNotifications = document.getElementById('view-notifications');
      const viewSettingsMenu = document.getElementById('view-settings-menu');
      const viewSettingsForm = document.getElementById('view-settings-form');
      const settingsFormTitle = document.getElementById('settings-form-title');

      const btnSettings = document.getElementById('qa-settings');
      const btnBackToNotifications = document.getElementById('btn-back-to-notifications');
      const btnBackToSettings = document.getElementById('btn-back-to-settings');

      const forms = {
        'form-name': {
          el: document.getElementById('form-name'),
          title: 'Change Display Name'
        },
        'form-email': {
          el: document.getElementById('form-email'),
          title: 'Change Email'
        },
        'form-password': {
          el: document.getElementById('form-password'),
          title: 'Change Password'
        }
      };

      function applyNotificationsSizing() {
        if (!qaPanel || !rightPanel) return;
        // Compute a common height and apply to BOTH panels so they match exactly
        const leftH = qaPanel.getBoundingClientRect().height;
        // Ensure a sensible minimum height (matches the previous min-h)
        const minH = 320;
        const target = Math.max(Math.ceil(leftH), minH);
        qaPanel.style.height = target + 'px';
        rightPanel.style.height = target + 'px';
        // Scrolling is confined to notifications list (inner container)
      }

      function clearPanelSizing() {
        if (rightPanel) {
          rightPanel.style.height = '';
        }
        if (qaPanel) {
          qaPanel.style.height = '';
        }
      }

      function showNotifications() {
        viewNotifications.classList.remove('hidden');
        viewSettingsMenu.classList.add('hidden');
        viewSettingsForm.classList.add('hidden');
        Object.values(forms).forEach(f => f.el.classList.add('hidden'));
        // Ensure notifications box matches QA height and is scrollable
        applyNotificationsSizing();
      }

      function showSettingsMenu() {
        viewNotifications.classList.add('hidden');
        if (viewSaved) viewSaved.classList.add('hidden');
        viewSettingsMenu.classList.remove('hidden');
        viewSettingsForm.classList.add('hidden');
        Object.values(forms).forEach(f => f.el.classList.add('hidden'));
        // Let settings expand naturally
        clearPanelSizing();
      }

      function showSettingsForm(id) {
        viewNotifications.classList.add('hidden');
        if (viewSaved) viewSaved.classList.add('hidden');
        viewSettingsMenu.classList.add('hidden');
        viewSettingsForm.classList.remove('hidden');
        Object.entries(forms).forEach(([key, f]) => {
          if (key === id) {
            f.el.classList.remove('hidden');
            settingsFormTitle.textContent = f.title;
          } else {
            f.el.classList.add('hidden');
          }
        });
        // Let settings expand naturally
        clearPanelSizing();
      }

      function showSaved() {
        if (viewSaved) viewSaved.classList.remove('hidden');
        viewNotifications.classList.add('hidden');
        viewSettingsMenu.classList.add('hidden');
        viewSettingsForm.classList.add('hidden');
        Object.values(forms).forEach(f => f.el.classList.add('hidden'));
        // Match panels and keep inner list scrollable
        applyNotificationsSizing();
      }

      // Wiring
      if (btnSettings) btnSettings.addEventListener('click', showSettingsMenu);
      if (btnBackToNotifications) btnBackToNotifications.addEventListener('click', showNotifications);
      if (btnBackToSettings) btnBackToSettings.addEventListener('click', showSettingsMenu);

      document.querySelectorAll('#view-settings-menu [data-target]').forEach(btn => {
        btn.addEventListener('click', () => showSettingsForm(btn.dataset.target));
      });

      // When page loads, notifications are visible by default — size the box
      applyNotificationsSizing();

      // Keep sizing in sync on window resize when notifications are visible
      window.addEventListener('resize', () => {
        if (!viewNotifications.classList.contains('hidden') || (viewSaved && !viewSaved.classList.contains('hidden'))) {
          applyNotificationsSizing();
        }
      });

      // Placeholder handlers for items we will build later
      const info = (msg) => alert(msg);
      const btnMyRecipes = document.getElementById('qa-my-recipes');
      const btnSaved = document.getElementById('qa-saved');
      if (btnMyRecipes) btnMyRecipes.addEventListener('click', () => {
        document.getElementById('my-recipes')?.scrollIntoView({behavior: 'smooth'});
      });
      if (btnSaved) btnSaved.addEventListener('click', showSaved);

          // Password field toggles and match feedback for Change Password form
          function wireToggle(btnId, inputId) {
            const b = document.getElementById(btnId);
            const i = document.getElementById(inputId);
            if (!b || !i) return;
            b.addEventListener('click', () => {
              i.type = i.type === 'password' ? 'text' : 'password';
            });
          }
          wireToggle('toggle-cp-current', 'cp-current');
          wireToggle('toggle-cp-new', 'cp-new');
          wireToggle('toggle-cp-confirm', 'cp-confirm');

          const npw = document.getElementById('cp-new');
          const ncf = document.getElementById('cp-confirm');
          const nmsg = document.getElementById('cp-match-msg');
          function updateCpMatch() {
            if (!npw || !ncf || !nmsg) return;
            const bothFilled = npw.value.length > 0 && ncf.value.length > 0;
            const strong = npw.checkValidity();
            const match = npw.value === ncf.value;
            nmsg.classList.remove('hidden');
            nmsg.textContent = match ? (strong ? 'Passwords match.' : 'Passwords match but are not strong enough.') : 'Passwords do not match.';
            nmsg.className = 'mt-1 text-xs ' + (match && strong ? 'text-green-600' : 'text-red-600');
            if (!bothFilled) nmsg.classList.add('hidden');
          }
          if (npw && ncf) {
            npw.addEventListener('input', updateCpMatch);
            ncf.addEventListener('input', updateCpMatch);
          }
    </script>
  </body>
</html>