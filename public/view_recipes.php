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
    <link rel="stylesheet" href="/blog/public/css/app.css" />
        
    </head>
    <body class="min-h-screen bg-white text-gray-800 scroll-smooth">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <main class="max-w-6xl mx-auto px-4 py-6 mb-16">
            <?php
                // Determine if we're on the landing view (no search, first page)
                $landingSearch = trim($_GET['search'] ?? '');
                $landingPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            ?>
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

                        <?php if ($landingSearch === '' && $landingPage === 1): ?>
                                <?php
                                    // Random quote hero (simple curated list)
                                    $quotes = [
                                        'Cooking is love made visible.',
                                        'Good food is the foundation of genuine happiness.',
                                        'Baked with love, served with joy.',
                                        'Life is short. Eat dessert first.',
                                        'The secret ingredient is always love.',
                                        'Happiness is homemade.'
                                    ];
                                    $len = count($quotes);
                                    // Pick two distinct quotes: one for hero, one for the side tile
                                    try {
                                        $i1 = random_int(0, $len - 1);
                                    } catch (Exception $e) {
                                        $i1 = array_rand($quotes);
                                    }
                                    do {
                                        try {
                                            $i2 = random_int(0, $len - 1);
                                        } catch (Exception $e) {
                                            $i2 = array_rand($quotes);
                                        }
                                    } while ($i2 === $i1 && $len > 1);
                                    $heroQuote = $quotes[$i1];
                                    $sideQuote = $quotes[$i2];
                                ?>
                                <section class="mb-8">
                                    <div class="w-full bg-transparent text-gray-900 overflow-hidden min-h-[60vh] flex flex-col" style="background-image: radial-gradient(ellipse at center, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 35%, rgba(255,255,255,0.75) 55%, rgba(255,255,255,0.5) 70%, rgba(255,255,255,0.25) 85%, rgba(255,255,255,0) 100%), url('/blog/public/assets/hero-cookies.svg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                                        <!-- Centered quote content -->
                                        <div class="flex-1 flex items-center justify-center">
                                            <div class="px-6 text-center max-w-4xl mx-auto">
                                                <p class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-extrabold leading-tight" style="font-family: 'Comic Sans MS','Comic Sans','Comic Neue',cursive; font-size: clamp(3rem, 8vw, 9rem); line-height: 1.05; padding-bottom: 20px; padding-top: 20px;">
                                                    “<?= htmlspecialchars($heroQuote, ENT_QUOTES, 'UTF-8') ?>”
                                                </p>
                                            </div>
                                        </div>
                                        <!-- Bottom actions -->
                                        <div class="px-4 pb-6 md:pb-8">
                                            <div class="mx-auto max-w-3xl flex flex-col items-center justify-center gap-3">
                                                <div class="flex items-center justify-center gap-3 flex-wrap">
                                                    <a href="/blog/public/add_recipe.php?from=listing" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-5 py-2.5 font-semibold hover:bg-[#e5573e] w-full sm:w-auto justify-center">Share your own recipes</a>
                                                    <a href="/blog/public/view_recipes.php#latest" class="inline-flex items-center rounded-[15px] border border-[#ff6347]/30 text-[#ff6347] hover:bg-[#ff6347]/10 px-5 py-2.5 font-semibold w-full sm:w-auto justify-center">Explore recipes</a>
                                                </div>
                                                <div>
                                                    <a href="/blog/public/about.php" class="inline-flex items-center rounded-[15px] bg-black text-white px-4 py-2 text-sm font-semibold hover:bg-neutral-800 w-full sm:w-auto justify-center">About Us</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <?php
                                    // Featured: Most popular by likes (fallback to most recent if no likes)
                                    $featured = null;
                                    try {
                                        $q = $pdo->query(
                                            "SELECT r.id, r.title, r.category, r.image_main, u.username, COUNT(rl.id) AS likes
                                             FROM recipe r
                                             JOIN user u ON u.id = r.user_id
                                             LEFT JOIN recipe_likes rl ON rl.recipe_id = r.id
                                             GROUP BY r.id, r.title, r.category, r.image_main, u.username
                                             ORDER BY likes DESC, r.created_at DESC
                                             LIMIT 1"
                                        );
                                        $featured = $q->fetch(PDO::FETCH_ASSOC) ?: null;
                                        if (!$featured) {
                                            $q2 = $pdo->query(
                                                "SELECT r.id, r.title, r.category, r.image_main, u.username, 0 AS likes
                                                 FROM recipe r JOIN user u ON u.id = r.user_id
                                                 ORDER BY r.created_at DESC
                                                 LIMIT 1"
                                            );
                                            $featured = $q2->fetch(PDO::FETCH_ASSOC) ?: null;
                                        }
                                    } catch (Exception $e) { $featured = null; }
                                ?>
                                                <?php if ($featured): ?>
                                                    <?php
                                                        $fid = (int)$featured['id'];
                                                        $fimg = $featured['image_main'] ? '../public/' . htmlspecialchars($featured['image_main'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/800x450?text=No+Image';
                                                        $ftitle = htmlspecialchars($featured['title'], ENT_QUOTES, 'UTF-8');
                                                        $fuser = htmlspecialchars($featured['username'], ENT_QUOTES, 'UTF-8');
                                                        $fcat = htmlspecialchars($featured['category'], ENT_QUOTES, 'UTF-8');
                                                        $flikes = (int)$featured['likes'];
                                                        // Fetch first step as description preview
                                                        $fdesc = '';
                                                        try {
                                                            $s = $pdo->prepare("SELECT step_description FROM recipe_steps WHERE recipe_id = :rid ORDER BY step_number ASC LIMIT 1");
                                                            $s->execute(['rid' => $fid]);
                                                            $row = $s->fetch(PDO::FETCH_ASSOC);
                                                            if ($row && !empty($row['step_description'])) {
                                                                $fdesc = trim($row['step_description']);
                                                                if (mb_strlen($fdesc) > 220) { $fdesc = mb_substr($fdesc, 0, 220) . '…'; }
                                                            }
                                                        } catch (Exception $e) { /* ignore */ }
                                                    ?>
                                                    <section class="mb-8">
                                                        <h2 class="text-2xl font-semibold mb-3">Top Recipe</h2>
                                                        <div class="grid gap-4 md:grid-cols-2 items-stretch">
                                                            <div class="justify-self-start w-full md:max-w-xl">
                                                                <div class="bg-[#FAF7F2] border border-gray-200 rounded-[15px] shadow hover:shadow-md transition overflow-hidden p-4">
                                                                    <a href="recipe.php?id=<?= $fid ?>" class="block">
                                                                        <div class="aspect-video bg-gray-100 overflow-hidden rounded-[15px]">
                                                                            <img src="<?= $fimg ?>" alt="Top Recipe Image" class="w-full h-full object-cover" />
                                                                        </div>
                                                                        <div class="mt-4">
                                                                            <h3 class="text-xl md:text-2xl font-bold leading-snug"><?= $ftitle ?></h3>
                                                                            <p class="text-sm text-gray-600 mt-1">By <?= $fuser ?></p>
                                                                            <p class="text-xs text-[#ff6347] mt-1"><em><?= $fcat ?></em></p>
                                                                            <?php if ($fdesc !== ''): ?>
                                                                                <p class="text-sm text-gray-700 mt-3"><?= nl2br(htmlspecialchars($fdesc, ENT_QUOTES, 'UTF-8')) ?></p>
                                                                            <?php else: ?>
                                                                                <p class="text-sm text-gray-500 mt-3">No description available.</p>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="mt-4 flex items-center gap-2 text-sm text-gray-700">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ff6347" stroke="#ff6347" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21h7.125a3.375 3.375 0 003.32-2.71l1.194-6.375A2.25 2.25 0 0016.92 9H13.5V6.75A2.25 2.25 0 0011.25 4.5h-.9c-.621 0-1.17.42-1.311 1.023L7.5 9m0 12V9m0 12H5.25A2.25 2.25 0 013 18.75V12.75A2.25 2.25 0 015.25 10.5H7.5" /></svg>
                                                                            <span class="font-medium"><?= $flikes ?></span>
                                                                            <span>likes</span>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="rounded-[15px]  bg-white p-4 md:p-6 flex items-center justify-center">
                                                                <p class="text-3xl md:text-4xl lg:text-5xl text-gray-800 text-center leading-relaxed font-extrabold" style="font-family: 'Comic Sans MS','Comic Sans','Comic Neue',cursive;font-size: clamp(3rem, 5vw, 9rem); line-height: 1.05;">“<?= htmlspecialchars($sideQuote, ENT_QUOTES, 'UTF-8') ?>”</p>
                                                            </div>
                                                        </div>
                                                    </section>
                                                <?php endif; ?>
                        <?php endif; ?>

                        <h1 id="latest" class="text-2xl font-bold mb-4">Latest Recipes</h1>

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
                        $stmt = $pdo->prepare("SELECT r.id, r.title, r.description, r.category, r.image_main, u.username, r.created_at
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
                        $stmt = $pdo->prepare("SELECT r.id, r.title, r.description, r.category, r.image_main, u.username, r.created_at
                                                FROM recipe r
                                                JOIN user u ON r.user_id = u.id
                                                ORDER BY r.created_at DESC
                                                LIMIT :limit OFFSET :offset");
                        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                    }

                    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Determine like/save state for current user for recipes on this page
                    $likedMap = [];
                    $savedMap = [];
                    if (isset($_SESSION['user_id']) && !empty($recipes)) {
                        $ids = array_column($recipes, 'id');
                        $placeholders = implode(',', array_fill(0, count($ids), '?'));
                        // Liked
                        $likeQ = $pdo->prepare("SELECT recipe_id FROM recipe_likes WHERE user_id = ? AND recipe_id IN ($placeholders)");
                        $likeQ->execute(array_merge([$_SESSION['user_id']], $ids));
                        foreach ($likeQ->fetchAll(PDO::FETCH_COLUMN) as $rid) { $likedMap[(int)$rid] = true; }
                        // Saved (if table exists)
                        try {
                            $saveQ = $pdo->prepare("SELECT recipe_id FROM recipe_saves WHERE user_id = ? AND recipe_id IN ($placeholders)");
                            $saveQ->execute(array_merge([$_SESSION['user_id']], $ids));
                            foreach ($saveQ->fetchAll(PDO::FETCH_COLUMN) as $rid) { $savedMap[(int)$rid] = true; }
                        } catch (Exception $e) {
                            // if table absent, silently ignore
                        }
                    }
                    $totalPages = max(1, (int)ceil($total / $perPage));
                    if ($page > $totalPages) { $page = $totalPages; }

                    if (count($recipes) === 0) {
                        echo '<p class="text-gray-600">No recipes yet. Be the first to share one!</p>';
                    } else {
                                                foreach ($recipes as $r) {
                                                        $img = $r['image_main'] ? '../public/' . htmlspecialchars($r['image_main'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/640x360?text=No+Image';
                                                        $title = htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8');
                                                        $desc = isset($r['description']) ? trim($r['description']) : '';
                                                        if ($desc !== '') {
                                                            $desc = htmlspecialchars(mb_strimwidth($desc, 0, 120, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8');
                                                        }
                                                        $user = htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8');
                                                        $cat = htmlspecialchars($r['category'], ENT_QUOTES, 'UTF-8');
                                                        $id = (int)$r['id'];
                                                        $qs = http_build_query(array_filter([
                                                                'id' => $id,
                                                                'page' => $page,
                                                                'search' => $search !== '' ? $search : null,
                                                        ]));
                                                        $isLiked = isset($likedMap[$id]);
                                                        $isSaved = isset($savedMap[$id]);
                                                        echo "
                                                                                            <div class='group bg-white border border-gray-200 rounded-[15px] shadow hover:shadow-md transition overflow-hidden'>
                                                                <a href='recipe.php?{$qs}' class='block'>
                                                                    <div class='aspect-video bg-gray-100 overflow-hidden'>
                                                                        <img src='{$img}' alt='Recipe Image' class='w-full h-full object-cover group-hover:scale-[1.02] transition' />
                                                                    </div>
                                                                    <div class='p-4'>
                                                                        <h3 class='font-semibold text-lg mb-1'>{$title}</h3>
                                                                        <p class='text-sm text-gray-600'>By {$user}</p>
                                                                        <p class='text-xs text-[#ff6347] mt-1'><em>{$cat}</em></p>
                                                                                                        " . ($desc !== '' ? "<p class='text-sm text-gray-700 mt-2'>{$desc}</p>" : "") . "
                                                                    </div>
                                                                </a>
                                                                ".(isset($_SESSION['user_id'])
                                                                    ? "<div class='px-4 pb-4 flex items-center gap-3'>
                                                                                <button data-recipe='{$id}' data-action='like' class='inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm " . ($isLiked ? "border-[#ff6347] text-[#ff6347] bg-[#ff6347]/10" : "border-gray-300 text-gray-700 hover:bg-gray-50") . "' title='Like'>
                                                                                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='" . ($isLiked ? "#ff6347" : "none") . "' stroke='" . ($isLiked ? "#ff6347" : "currentColor") . "' class='w-4 h-4'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M7.5 21h7.125a3.375 3.375 0 003.32-2.71l1.194-6.375A2.25 2.25 0 0016.92 9H13.5V6.75A2.25 2.25 0 0011.25 4.5h-.9c-.621 0-1.17.42-1.311 1.023L7.5 9m0 12V9m0 12H5.25A2.25 2.25 0 013 18.75V12.75A2.25 2.25 0 015.25 10.5H7.5' /></svg>
                                                                                    <span>" . ($isLiked ? "Liked" : "Like") . "</span>
                                                                                </button>
                                                                                <button data-recipe='{$id}' data-action='save' class='inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm " . ($isSaved ? "border-[#ff6347] text-[#ff6347] bg-[#ff6347]/10" : "border-gray-300 text-gray-700 hover:bg-gray-50") . "' title='Save'>
                                                                                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='" . ($isSaved ? "#ff6347" : "none") . "' stroke='" . ($isSaved ? "#ff6347" : "currentColor") . "' class='w-4 h-4'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M5.25 3.75A2.25 2.25 0 017.5 1.5h9a2.25 2.25 0 012.25 2.25v17.19a.75.75 0 01-1.132.65L12 17.25l-5.618 4.34a.75.75 0 01-1.132-.65V3.75z' /></svg>
                                                                                    <span>" . ($isSaved ? "Saved" : "Save") . "</span>
                                                                                </button>
                                                                            </div>"
                                                                    : "").
                                                            "</div>";
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

                <?php if (isset($_SESSION['user_id'])): ?>
                <script>
                    // Delegate clicks for like/save buttons
                    document.addEventListener('click', async (e) => {
                        const btn = e.target.closest('button[data-action]');
                        if (!btn) return;
                        e.preventDefault();
                        const rid = btn.getAttribute('data-recipe');
                        const action = btn.getAttribute('data-action');
                        const isLike = action === 'like';
                        const endpoint = isLike ? '../src/controllers/toggle_like.php' : '../src/controllers/toggle_save.php';

                        const fd = new FormData();
                        fd.append('recipe_id', rid);
                        try {
                            const res = await fetch(endpoint, { method: 'POST', body: fd });
                            const data = await res.json();
                            if (data.error) { alert(data.error); return; }
                            // Toggle styles/text
                            const active = (isLike && data.status === 'liked') || (!isLike && data.status === 'saved');
                            const inactive = (isLike && data.status === 'unliked') || (!isLike && data.status === 'unsaved');
                            if (active || inactive) {
                                const svg = btn.querySelector('svg');
                                const span = btn.querySelector('span');
                                if (active) {
                                    btn.className = 'inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm border-[#ff6347] text-[#ff6347] bg-[#ff6347]/10';
                                    svg.setAttribute('fill', '#ff6347');
                                    svg.setAttribute('stroke', '#ff6347');
                                    span.textContent = isLike ? 'Liked' : 'Saved';
                                } else {
                                    btn.className = 'inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm border-gray-300 text-gray-700 hover:bg-gray-50';
                                    svg.setAttribute('fill', 'none');
                                    svg.setAttribute('stroke', 'currentColor');
                                    span.textContent = isLike ? 'Like' : 'Save';
                                }
                            }
                        } catch (err) {
                            alert('Action failed.');
                        }
                    });
                </script>
                <?php endif; ?>
                <script>
                    // Custom slower smooth scroll for Explore link to #latest
                    (function() {
                        function smoothScrollTo(targetY, duration) {
                            const startY = window.pageYOffset;
                            const diff = targetY - startY;
                            const start = performance.now();
                            const ease = t => t<.5 ? 2*t*t : -1+(4-2*t)*t; // easeInOutQuad
                            function step(now) {
                                const elapsed = now - start;
                                const p = Math.min(1, elapsed / duration);
                                const y = startY + diff * ease(p);
                                window.scrollTo(0, y);
                                if (p < 1) requestAnimationFrame(step);
                            }
                            requestAnimationFrame(step);
                        }

                        document.querySelectorAll('a[href$="#latest"]').forEach(a => {
                            a.addEventListener('click', (e) => {
                                const el = document.getElementById('latest');
                                if (!el) return;
                                e.preventDefault();
                                const rect = el.getBoundingClientRect();
                                const offset = window.pageYOffset + rect.top - 10; // slight padding
                                smoothScrollTo(offset, 800); // ~0.8s
                            });
                        });
                    })();
                </script>
        </body>
</html>