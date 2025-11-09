<?php
// Handles POSTed credentials from the public login form and seeds the user session.
require_once __DIR__ . '/../../../config/config.php';

// Always use the themed public login page for GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /blog/public/login.php');
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
                header("Location: /blog/public/view_recipes.php");
                exit;
            } else {
                $errors[] = 'Invalid email or password.';
            }
        } catch (Exception $e) {
            $errors[] = 'Database error. Please try again later.';
            // Persist a short diagnostic entry without exposing details to end users.
            @file_put_contents(
                __DIR__ . '/../../../logs/errors.log',
                "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . PHP_EOL,
                FILE_APPEND
            );
        }
    }

    // On errors, redirect back to the themed public login with messages
    $_SESSION['login_errors'] = $errors;
    $_SESSION['old_email'] = $email;
    header('Location: /blog/public/login.php');
    exit;
}
