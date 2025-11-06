<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/helpers/markdown.php';

$recipe_id = $_GET['id'] ?? null;

if (!$recipe_id || !is_numeric($recipe_id)) {
    echo "Invalid recipe ID.";
    exit;
}

// Fetch recipe details
try {
    $stmt = $pdo->prepare("SELECT r.*, u.username 
                           FROM recipe r 
                           JOIN user u ON r.user_id = u.id 
                           WHERE r.id = :id");
    $stmt->execute(['id' => $recipe_id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) {
        echo "Recipe not found.";
        exit;
    }

    // Fetch ingredients
    $ingStmt = $pdo->prepare("SELECT ingredient_name, quantity 
                              FROM recipe_ingredients 
                              WHERE recipe_id = :id");
    $ingStmt->execute(['id' => $recipe_id]);
    $ingredients = $ingStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch steps
    $stepStmt = $pdo->prepare("SELECT step_number, step_description, step_image 
                               FROM recipe_steps 
                               WHERE recipe_id = :id 
                               ORDER BY step_number ASC");
    $stepStmt->execute(['id' => $recipe_id]);
    $steps = $stepStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error loading recipe: " . htmlspecialchars($e->getMessage());
    exit;
}

// Build a fallback back-link URL to the listing, preserving page/search when present
$returnPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$returnSearch = isset($_GET['search']) ? trim($_GET['search']) : '';
$backUrl = '/blog/public/view_recipes.php?page=' . $returnPage;
if ($returnSearch !== '') {
    $backUrl .= '&search=' . urlencode($returnSearch);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($recipe['title']) ?> · The Cookie Lovestoblog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/blog/public/css/app.css" />
    
</head>

<body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="max-w-6xl mx-auto px-4 py-6 mb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <aside class="hidden lg:block lg:col-span-1 bg-white border border-gray-200 rounded-xl shadow p-5 h-max lg:sticky lg:top-20">
                <h2 class="text-lg font-semibold mb-3">Ingredients for <?= htmlspecialchars($recipe['title']) ?></h2>
                <table class="w-full text-sm">
                    <tbody>
                        <?php foreach ($ingredients as $ing): ?>
                            <tr class="border-b last:border-0">
                                <td class="py-2 pr-3 text-gray-800"><?= htmlspecialchars($ing['ingredient_name']) ?></td>
                                <td class="py-2 pl-3 text-gray-600"><?= htmlspecialchars($ing['quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </aside>
            <section class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl shadow p-6 mb-6">
                    <a id="backLink" href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center text-[#ff6347] hover:underline mb-3">Back to recipes</a>
                    <h1 class="text-2xl font-bold mb-1"><?= htmlspecialchars($recipe['title']) ?></h1>
                    <p class="text-sm text-gray-600 mb-4"><em>By <?= htmlspecialchars($recipe['username']) ?> · Category: <?= htmlspecialchars($recipe['category']) ?></em></p>
                    <?php
                    // Count likes
                    $likeCountStmt = $pdo->prepare("SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = :id");
                    $likeCountStmt->execute(['id' => $recipe_id]);
                    $likeCount = $likeCountStmt->fetchColumn();

                    $isLiked = false;
                    $isSaved = false;
                    if (isset($_SESSION['user_id'])) {
                        $checkLike = $pdo->prepare("SELECT id FROM recipe_likes WHERE recipe_id = :id AND user_id = :uid");
                        $checkLike->execute(['id' => $recipe_id, 'uid' => $_SESSION['user_id']]);
                        $isLiked = $checkLike->rowCount() > 0;

                        try {
                            $checkSave = $pdo->prepare("SELECT id FROM recipe_saves WHERE recipe_id = :id AND user_id = :uid");
                            $checkSave->execute(['id' => $recipe_id, 'uid' => $_SESSION['user_id']]);
                            $isSaved = $checkSave->rowCount() > 0;
                        } catch (Exception $e) {
                            $isSaved = false;
                        }
                    }
                    ?>
                    <div id="likeSection" class="my-3 flex items-center gap-3 text-gray-700">
                        <button id="likeButton" class="inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm <?= $isLiked ? 'border-[#ff6347] text-[#ff6347] bg-[#ff6347]/10' : 'border-gray-300 text-gray-700 hover:bg-gray-50' ?>" title="Like">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="<?= $isLiked ? '#ff6347' : 'none' ?>" stroke="<?= $isLiked ? '#ff6347' : 'currentColor' ?>" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21h7.125a3.375 3.375 0 003.32-2.71l1.194-6.375A2.25 2.25 0 0016.92 9H13.5V6.75A2.25 2.25 0 0011.25 4.5h-.9c-.621 0-1.17.42-1.311 1.023L7.5 9m0 12V9m0 12H5.25A2.25 2.25 0 013 18.75V12.75A2.25 2.25 0 015.25 10.5H7.5" /></svg>
                            <span><?= $isLiked ? 'Liked' : 'Like' ?></span>
                        </button>
                        <button id="saveButton" class="inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm <?= $isSaved ? 'border-[#ff6347] text-[#ff6347] bg-[#ff6347]/10' : 'border-gray-300 text-gray-700 hover:bg-gray-50' ?>" title="Save">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="<?= $isSaved ? '#ff6347' : 'none' ?>" stroke="<?= $isSaved ? '#ff6347' : 'currentColor' ?>" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.25 3.75A2.25 2.25 0 017.5 1.5h9a2.25 2.25 0 012.25 2.25v17.19a.75.75 0 01-1.132.65L12 17.25l-5.618 4.34a.75.75 0 01-1.132-.65V3.75z" /></svg>
                            <span><?= $isSaved ? 'Saved' : 'Save' ?></span>
                        </button>
                        <span id="likeCount" class="text-sm font-medium"><?= $likeCount ?></span>
                        <span class="text-sm">likes</span>
                    </div>
                    <?php if ($recipe['image_main']): ?>
                        <img src="<?= htmlspecialchars('../public/' . $recipe['image_main']) ?>" alt="Recipe Image" class="w-full max-w-full md:max-w-3xl h-auto rounded-lg mb-4">
                    <?php endif; ?>

                    <?php if (!empty($recipe['description'])): ?>
                        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4">
                            <div class="text-gray-800 text-base"><?php echo md_to_html($recipe['description']); ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile/Tablet ingredients block: shown below title/photo and description, hidden on desktop -->
                    <div class="block lg:hidden bg-white border border-gray-200 rounded-xl shadow p-5 mb-6">
                        <h2 class="text-lg font-semibold mb-3">Ingredients for <?= htmlspecialchars($recipe['title']) ?></h2>
                        <table class="w-full text-sm">
                            <tbody>
                                <?php foreach ($ingredients as $ing): ?>
                                    <tr class="border-b last:border-0">
                                        <td class="py-2 pr-3 text-gray-800"><?= htmlspecialchars($ing['ingredient_name']) ?></td>
                                        <td class="py-2 pl-3 text-gray-600"><?= htmlspecialchars($ing['quantity']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <h2 class="text-lg font-semibold mt-4 mb-2">Steps</h2>
                    <div class="space-y-6">
                        <?php foreach ($steps as $step): ?>
                            <div class="">
                                <h3 class="font-semibold">Step <?= $step['step_number'] ?></h3>
                                <div class="text-gray-700"><?php echo md_to_html($step['step_description']); ?></div>
                                <?php if ($step['step_image']): ?>
                                    <img src="<?= htmlspecialchars('../public/' . $step['step_image']) ?>" alt="Step image" class="mt-2 rounded-lg max-w-full h-auto">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $recipe['user_id']): ?>
                        <div class="mt-6 flex items-center gap-3">
                            <a href="edit_recipe.php?id=<?= $recipe_id ?>" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold shadow hover:bg-[#e5573e]">Edit</a>
                            <button id="deleteRecipe" class="inline-flex items-center rounded-[15px] bg-black text-white px-4 py-2 font-semibold hover:bg-black/90">Delete</button>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                // Fetch comments
                $cstmt = $pdo->prepare("SELECT c.id, c.comment_text, c.created_at, u.username, u.id AS uid
                                                            FROM recipe_comments c
                                                            JOIN user u ON c.user_id = u.id
                                                            WHERE c.recipe_id = :rid
                                                            ORDER BY c.created_at DESC");
                $cstmt->execute(['rid' => $recipe_id]);
                $comments = $cstmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div id="commentSection" class="bg-white border border-gray-200 rounded-xl shadow p-6">
                    <h2 class="text-lg font-semibold mb-3">Comments (<?= count($comments) ?>)</h2>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <textarea id="commentText" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#ff6347]" placeholder="Write a comment..."></textarea>
                        <button id="submitComment" class="mt-2 inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 font-semibold hover:bg-[#e5573e]">Post Comment</button>
                    <?php else: ?>
                        <p class="text-sm text-gray-600"> <a class="text-[#ff6347] hover:underline" href="login.php">Login</a> to comment.</p>
                    <?php endif; ?>

                    <div id="commentsList" class="mt-4 space-y-3">
                        <?php foreach ($comments as $c): ?>
                            <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg">
                                <div class="text-sm text-gray-700"><strong><?= htmlspecialchars($c['username']) ?></strong> <span class="text-gray-500">(<?= $c['created_at'] ?>)</span></div>
                                <p class="text-gray-800 mt-1"><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $c['uid']): ?>
                                    <button class="delete-comment inline-flex items-center rounded-[15px] bg-black text-white px-3 py-1 text-sm mt-2 hover:bg-black/90" data-id="<?= $c['id'] ?>">Delete</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        const likeBtn = document.getElementById('likeButton');
        const likeCount = document.getElementById('likeCount');
        if (likeBtn) {
            likeBtn.addEventListener('click', async () => {
                const formData = new FormData();
                formData.append('recipe_id', <?= json_encode($recipe_id) ?>);
                const res = await fetch('../src/controllers/toggle_like.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.status === 'liked' || data.status === 'unliked') {
                    const svg = likeBtn.querySelector('svg');
                    const span = likeBtn.querySelector('span');
                    if (data.status === 'liked') {
                        likeBtn.className = 'inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm border-[#ff6347] text-[#ff6347] bg-[#ff6347]/10';
                        if (svg) { svg.setAttribute('fill', '#ff6347'); svg.setAttribute('stroke', '#ff6347'); }
                        if (span) { span.textContent = 'Liked'; }
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    } else {
                        likeBtn.className = 'inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm border-gray-300 text-gray-700 hover:bg-gray-50';
                        if (svg) { svg.setAttribute('fill', 'none'); svg.setAttribute('stroke', 'currentColor'); }
                        if (span) { span.textContent = 'Like'; }
                        likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    }
                } else if (data.error) {
                    alert(data.error);
                }
            });
        }

        const saveBtn = document.getElementById('saveButton');
        if (saveBtn) {
            saveBtn.addEventListener('click', async () => {
                const fd = new FormData();
                fd.append('recipe_id', <?= json_encode($recipe_id) ?>);
                const res = await fetch('../src/controllers/toggle_save.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.status === 'saved' || data.status === 'unsaved') {
                    const svg = saveBtn.querySelector('svg');
                    const span = saveBtn.querySelector('span');
                    if (data.status === 'saved') {
                        saveBtn.className = 'inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm border-[#ff6347] text-[#ff6347] bg-[#ff6347]/10';
                        if (svg) { svg.setAttribute('fill', '#ff6347'); svg.setAttribute('stroke', '#ff6347'); }
                        if (span) { span.textContent = 'Saved'; }
                    } else {
                        saveBtn.className = 'inline-flex items-center gap-1 rounded-[15px] border px-3 py-1 text-sm border-gray-300 text-gray-700 hover:bg-gray-50';
                        if (svg) { svg.setAttribute('fill', 'none'); svg.setAttribute('stroke', 'currentColor'); }
                        if (span) { span.textContent = 'Save'; }
                    }
                } else if (data.error) {
                    alert(data.error);
                }
            });
        }

        const del = document.getElementById('deleteRecipe');
        if (del) {
            del.addEventListener('click', async () => {
                if (!confirm('Are you sure you want to delete this recipe?')) return;
                const fd = new FormData();
                fd.append('recipe_id', <?= json_encode($recipe_id) ?>);
                const res = await fetch('../src/controllers/delete_recipe.php', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();
                if (data.status === 'deleted') {
                    alert('Recipe deleted successfully');
                    window.location.href = 'view_recipes.php';
                } else {
                    alert(data.error);
                }
            });
        }

            // Post comment handler (inserted before delete-comment code)
            const submitBtn = document.getElementById('submitComment');
            if (submitBtn) {
                submitBtn.addEventListener('click', async () => {
                    const text = document.getElementById('commentText').value.trim();
                    if (!text) {
                        alert('Comment cannot be empty');
                        return;
                    }

                    const fd = new FormData();
                    fd.append('recipe_id', <?= json_encode($recipe_id) ?>);
                    fd.append('comment_text', text);

                    const res = await fetch('../src/controllers/add_comment.php', {
                        method: 'POST',
                        body: fd
                    });
                    const data = await res.json();

                    if (data.status === 'ok') {
                        location.reload();
                    } else if (data.error) {
                        alert(data.error);
                    }
                });
            }

        document.querySelectorAll('.delete-comment').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this comment?')) return;
                const fd = new FormData();
                fd.append('comment_id', btn.dataset.id);
                const res = await fetch('../src/controllers/delete_comment.php', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();
                if (data.status === 'deleted') {
                    location.reload();
                } else alert(data.error);
            });
        });
    </script>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>

</html>