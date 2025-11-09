<?php
// User dashboard showing quick actions, notifications, saved recipes, and profile settings.
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
    <?php $pageTitle = 'Dashboard · The Cookie Lovestoblog'; include __DIR__ . '/partials/header.php'; ?>
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
              <svg class="w-5 h-5 text-[#ff6347]" width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <span>Add Recipes</span>
            </a>
            <button type="button" id="qa-my-recipes" class="text-left flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg class="w-5 h-5 text-[#ff6347]" width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M4 7.9966C3.83599 7.99236 3.7169 7.98287 3.60982 7.96157C2.81644 7.80376 2.19624 7.18356 2.03843 6.39018C2 6.19698 2 5.96466 2 5.5C2 5.03534 2 4.80302 2.03843 4.60982C2.19624 3.81644 2.81644 3.19624 3.60982 3.03843C3.80302 3 4.03534 3 4.5 3H19.5C19.9647 3 20.197 3 20.3902 3.03843C21.1836 3.19624 21.8038 3.81644 21.9616 4.60982C22 4.80302 22 5.03534 22 5.5C22 5.96466 22 6.19698 21.9616 6.39018C21.8038 7.18356 21.1836 7.80376 20.3902 7.96157C20.2831 7.98287 20.164 7.99236 20 7.9966M10 13H14M4 8H20V16.2C20 17.8802 20 18.7202 19.673 19.362C19.3854 19.9265 18.9265 20.3854 18.362 20.673C17.7202 21 16.8802 21 15.2 21H8.8C7.11984 21 6.27976 21 5.63803 20.673C5.07354 20.3854 4.6146 19.9265 4.32698 19.362C4 18.7202 4 17.8802 4 16.2V8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <span>My Recipes</span>
            </button>
            
            <button type="button" id="qa-saved" class="text-left flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg class="w-5 h-5 text-[#ff6347]" width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M9 10.5L11 12.5L15.5 8M19 21V7.8C19 6.11984 19 5.27976 18.673 4.63803C18.3854 4.07354 17.9265 3.6146 17.362 3.32698C16.7202 3 15.8802 3 14.2 3H9.8C8.11984 3 7.27976 3 6.63803 3.32698C6.07354 3.6146 5.6146 4.07354 5.32698 4.63803C5 5.27976 5 6.11984 5 7.8V21L12 17L19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <span>Saved Recipes</span>
            </button>
            <button type="button" id="qa-settings" class="text-left flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg class="w-5 h-5 text-[#ff6347]" width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M18.7273 14.7273C18.6063 15.0015 18.5702 15.3056 18.6236 15.6005C18.6771 15.8954 18.8177 16.1676 19.0273 16.3818L19.0818 16.4364C19.2509 16.6052 19.385 16.8057 19.4765 17.0265C19.568 17.2472 19.6151 17.4838 19.6151 17.7227C19.6151 17.9617 19.568 18.1983 19.4765 18.419C19.385 18.6397 19.2509 18.8402 19.0818 19.0091C18.913 19.1781 18.7124 19.3122 18.4917 19.4037C18.271 19.4952 18.0344 19.5423 17.7955 19.5423C17.5565 19.5423 17.3199 19.4952 17.0992 19.4037C16.8785 19.3122 16.678 19.1781 16.5091 19.0091L16.4545 18.9545C16.2403 18.745 15.9682 18.6044 15.6733 18.5509C15.3784 18.4974 15.0742 18.5335 14.8 18.6545C14.5311 18.7698 14.3018 18.9611 14.1403 19.205C13.9788 19.4489 13.8921 19.7347 13.8909 20.0273V20.1818C13.8909 20.664 13.6994 21.1265 13.3584 21.4675C13.0174 21.8084 12.5549 22 12.0727 22C11.5905 22 11.1281 21.8084 10.7871 21.4675C10.4461 21.1265 10.2545 20.664 10.2545 20.1818V20.1C10.2475 19.7991 10.1501 19.5073 9.97501 19.2625C9.79991 19.0176 9.55521 18.8312 9.27273 18.7273C8.99853 18.6063 8.69437 18.5702 8.39947 18.6236C8.10456 18.6771 7.83244 18.8177 7.61818 19.0273L7.56364 19.0818C7.39478 19.2509 7.19425 19.385 6.97353 19.4765C6.7528 19.568 6.51621 19.6151 6.27727 19.6151C6.03834 19.6151 5.80174 19.568 5.58102 19.4765C5.36029 19.385 5.15977 19.2509 4.99091 19.0818C4.82186 18.913 4.68775 18.7124 4.59626 18.4917C4.50476 18.271 4.45766 18.0344 4.45766 17.7955C4.45766 17.5565 4.50476 17.3199 4.59626 17.0992C4.68775 16.8785 4.82186 16.678 4.99091 16.5091L5.04545 16.4545C5.25503 16.2403 5.39562 15.9682 5.4491 15.6733C5.50257 15.3784 5.46647 15.0742 5.34545 14.8C5.23022 14.5311 5.03887 14.3018 4.79497 14.1403C4.55107 13.9788 4.26526 13.8921 3.97273 13.8909H3.81818C3.33597 13.8909 2.87351 13.6994 2.53253 13.3584C2.19156 13.0174 2 12.5549 2 12.0727C2 11.5905 2.19156 11.1281 2.53253 10.7871C2.87351 10.4461 3.33597 10.2545 3.81818 10.2545H3.9C4.2009 10.2475 4.49273 10.1501 4.73754 9.97501C4.98236 9.79991 5.16883 9.55521 5.27273 9.27273C5.39374 8.99853 5.42984 8.69437 5.37637 8.39947C5.3229 8.10456 5.18231 7.83244 4.97273 7.61818L4.91818 7.56364C4.74913 7.39478 4.61503 7.19425 4.52353 6.97353C4.43203 6.7528 4.38493 6.51621 4.38493 6.27727C4.38493 6.03834 4.43203 5.80174 4.52353 5.58102C4.61503 5.36029 4.74913 5.15977 4.91818 4.99091C5.08704 4.82186 5.28757 4.68775 5.50829 4.59626C5.72901 4.50476 5.96561 4.45766 6.20455 4.45766C6.44348 4.45766 6.68008 4.50476 6.9008 4.59626C7.12152 4.68775 7.32205 4.82186 7.49091 4.99091L7.54545 5.04545C7.75971 5.25503 8.03183 5.39562 8.32674 5.4491C8.62164 5.50257 8.9258 5.46647 9.2 5.34545H9.27273C9.54161 5.23022 9.77093 5.03887 9.93245 4.79497C10.094 4.55107 10.1807 4.26526 10.1818 3.97273V3.81818C10.1818 3.33597 10.3734 2.87351 10.7144 2.53253C11.0553 2.19156 11.5178 2 12 2C12.4822 2 12.9447 2.19156 13.2856 2.53253C13.6266 2.87351 13.8182 3.33597 13.8182 3.81818V3.9C13.8193 4.19253 13.906 4.47834 14.0676 4.72224C14.2291 4.96614 14.4584 5.15749 14.7273 5.27273C15.0015 5.39374 15.3056 5.42984 15.6005 5.37637C15.8954 5.3229 16.1676 5.18231 16.3818 4.97273L16.4364 4.91818C16.6052 4.74913 16.8057 4.61503 17.0265 4.52353C17.2472 4.43203 17.4838 4.38493 17.7227 4.38493C17.9617 4.38493 18.1983 4.43203 18.419 4.52353C18.6397 4.61503 18.8402 4.74913 19.0091 4.91818C19.1781 5.08704 19.3122 5.28757 19.4037 5.50829C19.4952 5.72901 19.5423 5.96561 19.5423 6.20455C19.5423 6.44348 19.4952 6.68008 19.4037 6.9008C19.3122 7.12152 19.1781 7.32205 19.0091 7.49091L18.9545 7.54545C18.745 7.75971 18.6044 8.03183 18.5509 8.32674C18.4974 8.62164 18.5335 8.9258 18.6545 9.2V9.27273C18.7698 9.54161 18.9611 9.77093 19.205 9.93245C19.4489 10.094 19.7347 10.1807 20.0273 10.1818H20.1818C20.664 10.1818 21.1265 10.3734 21.4675 10.7144C21.8084 11.0553 22 11.5178 22 12C22 12.4822 21.8084 12.9447 21.4675 13.2856C21.1265 13.6266 20.664 13.8182 20.1818 13.8182H20.1C19.8075 13.8193 19.5217 13.906 19.2778 14.0676C19.0339 14.2291 18.8425 14.4584 18.7273 14.7273Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <span>General Settings</span>
            </button>
            <a href="about.php" class="flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg class="w-5 h-5 text-[#ff6347]" width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M12 16V12M12 8H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <span>About Us</span>
            </a>
            <a href="../src/controllers/auth/logout.php" class="flex items-center gap-2 px-3 py-2 rounded-[15px] hover:bg-[#ff6347]/10">
              <svg class="w-5 h-5 text-[#ff6347]" width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M16 17L21 12M21 12L16 7M21 12H9M12 17C12 17.93 12 18.395 11.8978 18.7765C11.6204 19.8117 10.8117 20.6204 9.77646 20.8978C9.39496 21 8.92997 21 8 21H7.5C6.10218 21 5.40326 21 4.85195 20.7716C4.11687 20.4672 3.53284 19.8831 3.22836 19.1481C3 18.5967 3 17.8978 3 16.5V7.5C3 6.10217 3 5.40326 3.22836 4.85195C3.53284 4.11687 4.11687 3.53284 4.85195 3.22836C5.40326 3 6.10218 3 7.5 3H8C8.92997 3 9.39496 3 9.77646 3.10222C10.8117 3.37962 11.6204 4.18827 11.8978 5.22354C12 5.60504 12 6.07003 12 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
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
              $eventKeys = [];
              $addEvent = function($timestamp, $message) use (&$events, &$eventKeys) {
                $key = $timestamp . '|' . $message;
                if (isset($eventKeys[$key])) {
                  return;
                }
                $eventKeys[$key] = true;
                $events[] = ['created_at' => $timestamp, 'message' => $message];
              };
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
                  $addEvent($row['created_at'], $msg);
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
                  $addEvent($row['created_at'], $msg);
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
                  $addEvent($row['created_at'], $msg);
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
                  $addEvent($row['created_at'], $msg);
                }
              } catch (Exception $e) {
                // swallow
              }

              // Merge session last_action as most recent pseudo-event if present
              if (!empty($_SESSION['last_action'])) {
                $addEvent(date('Y-m-d H:i:s'), $_SESSION['last_action']);
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
          <div id="view-saved" class="hidden h-full">
            <div class="flex items-center gap-3 mb-3 flex-none">
              <button type="button" id="btn-back-from-saved" class="text-sm text-[#ff6347] hover:underline">Back</button>
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
      const viewSaved = document.getElementById('view-saved');
      const viewNotifications = document.getElementById('view-notifications');
      const viewSettingsMenu = document.getElementById('view-settings-menu');
      const viewSettingsForm = document.getElementById('view-settings-form');
      const settingsFormTitle = document.getElementById('settings-form-title');

  const btnSettings = document.getElementById('qa-settings');
  const btnBackToNotifications = document.getElementById('btn-back-to-notifications');
  const btnBackToSettings = document.getElementById('btn-back-to-settings');
  const btnBackFromSaved = document.getElementById('btn-back-from-saved');

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
        if (viewSaved) {
          viewSaved.classList.add('hidden');
          viewSaved.classList.remove('flex', 'flex-col');
        }
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
        if (viewSaved) {
          viewSaved.classList.remove('hidden');
          viewSaved.classList.add('flex','flex-col');
        }
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
  if (btnBackFromSaved) btnBackFromSaved.addEventListener('click', showNotifications);

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
          const toggleBoth = document.getElementById('toggle-cp-new');
          const npwInput = document.getElementById('cp-new');
          const ncfInput = document.getElementById('cp-confirm');
          if (toggleBoth && npwInput && ncfInput) {
            toggleBoth.addEventListener('click', () => {
              const newType = npwInput.type === 'password' ? 'text' : 'password';
              npwInput.type = newType;
              ncfInput.type = newType;
            });
          }

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