<?php
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: /blog/public/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT r.id, r.title, r.category, r.image_main, u.username, s.created_at
    FROM recipe_saves s
    JOIN recipe r ON s.recipe_id = r.id
    JOIN user u ON r.user_id = u.id
    WHERE s.user_id = :uid
    ORDER BY s.created_at DESC
");
$stmt->execute(['uid' => $user_id]);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $pageTitle = 'My Saved Recipes · The Cookie Lovestoblog'; include __DIR__ . '/partials/header.php'; ?>
</head>
<body class="font-body bg-amber-50 text-gray-800">
  <?php include __DIR__ . '/partials/topbar.php'; ?>

  <main class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">💾 My Saved Recipes</h1>
    <?php if (empty($recipes)): ?>
      <p class="text-gray-600">You haven’t saved any recipes yet.</p>
    <?php else: ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($recipes as $r): ?>
          <a href="recipe.php?id=<?= (int)$r['id'] ?>" class="block bg-white rounded-xl shadow hover:shadow-lg transition p-4">
            <?php if (!empty($r['image_main'])): ?>
              <?php $img = htmlspecialchars('../public/' . $r['image_main'], ENT_QUOTES, 'UTF-8'); ?>
              <img src="<?= $img ?>" alt="Recipe image" class="rounded-lg mb-3 w-full h-48 object-cover" loading="lazy">
            <?php else: ?>
              <div class="rounded-lg mb-3 w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">No image</div>
            <?php endif; ?>
            <h2 class="text-lg font-semibold line-clamp-2"><?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="mt-1 text-xs text-gray-500"><em><?= htmlspecialchars($r['category'], ENT_QUOTES, 'UTF-8') ?></em> · by <?= htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8') ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
