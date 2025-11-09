<?php
// Creates a new account, enforcing password policy and seeding recovery words for future resets.
require_once __DIR__ . '/../../../config/config.php'; // Load $pdo and env()
require_once __DIR__ . '/../../helpers/recovery_words.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect user input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Basic validation
    if ($username === '' || $email === '' || $password === '') {
        $errors[] = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    // Strong password policy: 8+ chars, upper, lower, digit, symbol
    $strongPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';
    if (!preg_match($strongPattern, $password)) {
        $errors[] = 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a symbol.';
    }
    // Check password confirmation
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
}


    if (empty($errors)) {
        try {
            // Check if email or username already exists
            $check = $pdo->prepare("SELECT id FROM user WHERE email = :email OR username = :username");
            $check->execute(['email' => $email, 'username' => $username]);
            if ($check->fetch()) {
                $errors[] = 'Username or email already exists.';
            } else {
                $pdo->beginTransaction();
                // Hash the password before storing
                $hashed = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO user (username, email, password, role)
                    VALUES (:username, :email, :password, 'user')
                ");
                $stmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashed
                ]);

                $newUserId = (int)$pdo->lastInsertId();

                // Recovery words from form or fallback to random
                $words = $_POST['words'] ?? [];
                if (!is_array($words) || count($words) !== 5) {
                    $words = pick_random_words(5);
                }
                $normalized = normalize_recovery_words(array_values($words));
                $hashes = hash_recovery_words($normalized);

                // Insert into user_recovery
                $ur = $pdo->prepare("INSERT INTO user_recovery (user_id, word_hash1, word_hash2, word_hash3, word_hash4, word_hash5, created_at)
                                     VALUES (:uid, :h1, :h2, :h3, :h4, :h5, NOW())");
                $ur->execute([
                    'uid' => $newUserId,
                    'h1' => $hashes[0],
                    'h2' => $hashes[1],
                    'h3' => $hashes[2],
                    'h4' => $hashes[3],
                    'h5' => $hashes[4],
                ]);

                $pdo->commit();

                // Registration success -> redirect to login; words were already shown on the form
                setFlash('success', 'Account created successfully. You can now log in.');
                header('Location: /blog/public/login.php');
                exit;
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $errors[] = 'Database error. Please try again later.';
            @file_put_contents(
                __DIR__ . '/../../../logs/errors.log',
                "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . PHP_EOL,
                FILE_APPEND
            );
        }
    }

    // If there were errors, flash them and redirect back to the register page
    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_old'] = [
            'username' => $username,
            'email' => $email,
        ];
        if (isset($_POST['words']) && is_array($_POST['words'])) {
            $_SESSION['register_words_old'] = array_values($_POST['words']);
        }
        header('Location: /blog/public/register.php');
        exit;
    }
}
