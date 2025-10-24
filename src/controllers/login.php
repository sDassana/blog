<?php
// src/controllers/login.php
session_start();
require_once __DIR__ . '/../../config/config.php';

/**
 * Render a simple, clean login page with inline CSS.
 */
function render_login_form(string $email = '', array $errors = []): void {
    $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login · The Cookie Lovestoblog</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 text-gray-800">
        <main class="w-full max-w-md mx-auto mt-10 mb-20 bg-white border border-gray-200 rounded-xl shadow-md" role="main" aria-labelledby="login-title">
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-b from-white to-gray-50 rounded-t-xl">
                <h1 id="login-title" class="text-xl font-bold tracking-tight">Welcome back</h1>
                <p class="text-sm text-gray-500">Sign in to continue to your account</p>
            </div>
            <div class="p-6">
                <?php if (!empty($errors)): ?>
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm" role="alert" aria-live="polite">
                        <strong class="font-semibold">We couldn’t sign you in:</strong>
                        <ul class="list-disc pl-5 mt-1">
                            <?php foreach ($errors as $e): ?>
                                <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" novalidate class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm text-gray-600 mb-1">Email</label>
                        <input id="email" name="email" type="email" value="<?php echo $safeEmail; ?>" autocomplete="email" required placeholder="you@example.com" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
                    </div>
                    <div>
                        <label for="password" class="block text-sm text-gray-600 mb-1">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="••••••••" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
                    </div>
                    <div class="flex items-center justify-between gap-3 pt-1">
                        <button class="inline-flex items-center justify-center rounded-lg bg-amber-600 text-white px-4 py-2 font-semibold shadow hover:bg-amber-700 active:translate-y-px" type="submit">Sign in</button>
                        <a class="text-amber-700 hover:underline text-sm" href="/blog/public/register.php">Create account</a>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 text-center text-sm text-gray-600">
                <a class="text-amber-700 hover:underline" href="/blog/public/view_recipes.php">← Back to recipes</a>
            </div>
        </main>
    </body>
    </html>
    <?php
}

// If GET, render the styled login form (uses any flash errors previously set)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $oldEmail = $_SESSION['old_email'] ?? '';
    $errs = $_SESSION['login_errors'] ?? [];
    unset($_SESSION['old_email'], $_SESSION['login_errors']);
    render_login_form($oldEmail, $errs);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];

    if ($email === '' || $password === '') {
        $errors[] = 'Email and password are required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM user WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password correct → create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect to dashboard
                header("Location: /blog/public/dashboard.php");
                exit;
            } else {
                $errors[] = 'Invalid email or password.';
            }
        } catch (Exception $e) {
            $errors[] = 'Database error. Please try again later.';
            @file_put_contents(
                __DIR__ . '/../../logs/errors.log',
                "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . PHP_EOL,
                FILE_APPEND
            );
        }
    }

    // On errors, render the styled form with messages (also keep flash for compatibility)
    $_SESSION['login_errors'] = $errors;
    $_SESSION['old_email'] = $email;
    render_login_form($email, $errors);
    exit;
}
