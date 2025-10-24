<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/helpers/flash.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>All Recipes · The Cookie Lovestoblog</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-white text-gray-800">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <main class="max-w-6xl mx-auto px-4 py-6 mb-16">
            <?php if ($msg = getFlash('success')): ?>
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-700 px-3 py-2 text-sm">
                    <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <?php if ($msg = getFlash('error')): ?>
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm">
                    <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <h1 class="text-2xl font-bold mb-4">Latest Recipes</h1>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php
                try {
                    $search = trim($_GET['search'] ?? '');
                    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                    $perPage = 15;
                    $offset = ($page - 1) * $perPage;

                    if ($search !== '') {
                        // Count total (use unique placeholders for repeated terms)
                        $countStmt = $pdo->prepare("SELECT COUNT(*)
                                                    FROM recipe r
                                                    JOIN user u ON r.user_id = u.id
                                                    WHERE r.title LIKE :termTitle OR r.tags LIKE :termTags");
                        $likeTerm = "%$search%";
                        $countStmt->execute(['termTitle' => $likeTerm, 'termTags' => $likeTerm]);
                        $total = (int)$countStmt->fetchColumn();

                        // Fetch page (use unique placeholders for repeated terms)
                        $stmt = $pdo->prepare("SELECT r.id, r.title, r.category, r.image_main, u.username, r.created_at
                                                FROM recipe r
                                                JOIN user u ON r.user_id = u.id
                                                WHERE r.title LIKE :termTitle OR r.tags LIKE :termTags
                                                ORDER BY r.created_at DESC
                                                LIMIT :limit OFFSET :offset");
                        $stmt->bindValue(':termTitle', $likeTerm, PDO::PARAM_STR);
                        $stmt->bindValue(':termTags', $likeTerm, PDO::PARAM_STR);
                        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                    } else {
                        // Count total
                        $total = (int)$pdo->query("SELECT COUNT(*) FROM recipe r JOIN user u ON r.user_id = u.id")->fetchColumn();

                        // Fetch page
                        $stmt = $pdo->prepare("SELECT r.id, r.title, r.category, r.image_main, u.username, r.created_at
                                                FROM recipe r
                                                JOIN user u ON r.user_id = u.id
                                                ORDER BY r.created_at DESC
                                                LIMIT :limit OFFSET :offset");
                        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                    }

                    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $totalPages = max(1, (int)ceil($total / $perPage));
                    if ($page > $totalPages) { $page = $totalPages; }

                    if (count($recipes) === 0) {
                        echo '<p class="text-gray-600">No recipes yet. Be the first to share one!</p>';
                    } else {
                        foreach ($recipes as $r) {
                            $img = $r['image_main'] ? '../public/' . htmlspecialchars($r['image_main'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/640x360?text=No+Image';
                            $title = htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8');
                            $user = htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8');
                            $cat = htmlspecialchars($r['category'], ENT_QUOTES, 'UTF-8');
                            $id = (int)$r['id'];
                            $qs = http_build_query(array_filter([
                                'id' => $id,
                                'page' => $page,
                                'search' => $search !== '' ? $search : null,
                            ]));
                            echo "
                                <a href='recipe.php?{$qs}' class='group block bg-white border border-gray-200 rounded-xl shadow hover:shadow-md transition overflow-hidden'>
                                    <div class='aspect-video bg-gray-100 overflow-hidden'>
                                        <img src='{$img}' alt='Recipe Image' class='w-full h-full object-cover group-hover:scale-[1.02] transition' />
                                    </div>
                                    <div class='p-4'>
                                        <h3 class='font-semibold text-lg mb-1'>{$title}</h3>
                                        <p class='text-sm text-gray-600'>By {$user}</p>
                                        <p class='text-xs text-[#ff6347] mt-1'><em>{$cat}</em></p>
                                    </div>
                                </a>
                            ";
                        }
                    }
                } catch (Exception $e) {
                    echo "<p class='text-red-700'>Error loading recipes: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
                }
                ?>
            </div>

            <?php if (!empty($total) && $total > 0): ?>
                <?php
                    $totalPages = max(1, (int)ceil($total / $perPage));
                    $page = isset($page) ? $page : 1;
                    $searchQS = $search !== '' ? '&search=' . urlencode($search) : '';
                ?>
                <nav class="mt-8 flex items-center justify-center gap-2" aria-label="Pagination">
                    <?php if ($page > 1): ?>
                        <a class="px-3 py-1.5 rounded border border-gray-300 bg-white hover:bg-gray-50" href="?page=<?= $page - 1 ?><?= $searchQS ?>">Previous</a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 rounded border border-gray-200 text-gray-400 bg-gray-50 cursor-not-allowed">Previous</span>
                    <?php endif; ?>

                    <?php
                    // Show up to 5 page links centered on current page
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    for ($p = $start; $p <= $end; $p++):
                    ?>
                        <?php if ($p === $page): ?>
                            <span class="px-3 py-1.5 rounded bg-[#ff6347] text-white font-semibold"><?= $p ?></span>
                        <?php else: ?>
                            <a class="px-3 py-1.5 rounded border border-gray-300 bg-white hover:bg-gray-50" href="?page=<?= $p ?><?= $searchQS ?>"><?= $p ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a class="px-3 py-1.5 rounded border border-gray-300 bg-white hover:bg-gray-50" href="?page=<?= $page + 1 ?><?= $searchQS ?>">Next</a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 rounded border border-gray-200 text-gray-400 bg-gray-50 cursor-not-allowed">Next</span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </main>
        <?php include __DIR__ . '/partials/footer.php'; ?>
    </body>
</html>