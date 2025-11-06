<?php
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /blog/public/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT r.id, r.title, r.category, r.image_main, u.username
    FROM saved_recipes s
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
  <meta charset="UTF-8">
  <title>My Saved Recipes · The Cookie Loves to Blog</title>
  <link rel="stylesheet" href="/blog/public/css/app.css" />
  
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
          <a href="view_recipe.php?id=<?= $r['id'] ?>" class="block bg-white rounded-xl shadow hover:shadow-lg transition p-4">
            <?php if ($r['image_main']): ?>
              <img src="<?= htmlspecialchars('../public/' . $r['image_main']) ?>" class="rounded-lg mb-3 w-full h-48 object-cover">
            <?php endif; ?>
            <h2 class="text-lg font-semibold"><?= htmlspecialchars($r['title']) ?></h2>
            <p class="text-sm text-gray-600"><?= htmlspecialchars($r['category']) ?> by <?= htmlspecialchars($r['username']) ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
