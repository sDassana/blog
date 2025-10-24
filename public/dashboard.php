<?php
session_start();
require_once __DIR__ . '/../src/helpers/flash.php';
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Load current user data for prefilling
$user = null;
try {
  $stmt = $pdo->prepare("SELECT * FROM `user` WHERE id = :id");
  $stmt->execute(['id' => $_SESSION['user_id']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
  $user = [];
}

$displayName = isset($user['username']) ? $user['username'] : ($_SESSION['username'] ?? 'User');
$email = isset($user['email']) ? $user['email'] : '';
$about = isset($user['about']) ? $user['about'] : '';
$avatar = isset($user['avatar']) && $user['avatar'] ? ('../public/' . $user['avatar']) : 'https://via.placeholder.com/128x128?text=Avatar';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard · The Cookie Lovestoblog</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="max-w-4xl mx-auto px-4 py-8 mb-16">
      <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
        <?php if ($msg = getFlash('success')): ?>
          <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-700 px-3 py-2 text-sm"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = getFlash('error')): ?>
          <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="flex items-center gap-4 mb-6">
          <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="w-20 h-20 rounded-full object-cover border" />
          <div>
            <h2 class="text-xl font-bold">Welcome, <?= htmlspecialchars($displayName) ?>!</h2>
            <p class="text-gray-600">Logged in as: <span class="font-semibold text-gray-800"><?= htmlspecialchars($_SESSION['role'] ?? 'member') ?></span></p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Quick Actions -->
          <section class="bg-white border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold mb-3">Quick Actions</h3>
            <div class="flex flex-wrap gap-3">
              <a class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold shadow hover:bg-[#e5573e]" href="add_recipe.php">Add Recipe</a>
              <a class="inline-flex items-center rounded-[15px] border border-[#ff6347]/40 text-[#ff6347] px-4 py-2 font-semibold hover:bg-[#ff6347]/10" href="view_recipes.php">Explore Recipes</a>
              <a class="inline-flex items-center rounded-lg bg-red-600 text-white px-4 py-2 font-semibold hover:bg-red-700" href="../src/controllers/logout.php">Logout</a>
            </div>
          </section>

          <!-- Profile Info -->
          <section class="border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold mb-3">Profile</h3>
            <form action="../src/controllers/update_profile.php" method="POST" enctype="multipart/form-data" class="space-y-3">
              <div>
                <label class="block text-sm text-gray-600 mb-1">Display Name</label>
                <input type="text" name="username" value="<?= htmlspecialchars($displayName) ?>" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
              <div>
                <label class="block text-sm text-gray-600 mb-1">About</label>
                <textarea name="about" rows="3" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" placeholder="Say something about yourself..."><?= htmlspecialchars($about) ?></textarea>
              </div>
              <div>
                <label class="block text-sm text-gray-600 mb-1">Profile Picture</label>
                <input type="file" name="avatar" accept="image/*" class="block w-full text-sm text-gray-700" />
                <p class="text-xs text-gray-500 mt-1">JPEG/PNG/WebP up to ~3MB.</p>
              </div>
              <div class="pt-2">
                <button type="submit" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold hover:bg-[#e5573e]">Save Profile</button>
              </div>
            </form>
          </section>

          <!-- Account Email -->
          <section class="border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold mb-3">Change Email</h3>
            <form action="../src/controllers/update_email.php" method="POST" class="space-y-3">
              <div>
                <label class="block text-sm text-gray-600 mb-1">New Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
              <div>
                <label class="block text-sm text-gray-600 mb-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
              <div class="pt-2">
                <button type="submit" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold hover:bg-[#e5573e]">Update Email</button>
              </div>
            </form>
          </section>

          <!-- Account Password -->
          <section class="border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold mb-3">Change Password</h3>
            <form action="../src/controllers/update_password.php" method="POST" class="space-y-3">
              <div>
                <label class="block text-sm text-gray-600 mb-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
              <div>
                <label class="block text-sm text-gray-600 mb-1">New Password</label>
                <input type="password" name="new_password" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
              <div>
                <label class="block text-sm text-gray-600 mb-1">Confirm New Password</label>
                <input type="password" name="confirm_password" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" />
              </div>
              <div class="pt-2">
                <button type="submit" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold hover:bg-[#e5573e]">Update Password</button>
              </div>
            </form>
          </section>
        </div>
      </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
</html>