<?php
// src/controllers/register.php

session_start();
require_once __DIR__ . '/../../config/config.php'; // Load $pdo and env()
require_once __DIR__ . '/../helpers/flash.php';

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
                // Registration success: redirect to login
                $_SESSION['old_email'] = $email; // prefill login form
                setFlash('success', 'Account created successfully. Please log in to continue.');
                header('Location: /blog/public/login.php');
                exit;
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

    // If there were errors, flash them and redirect back to the register page
    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_old'] = [
            'username' => $username,
            'email' => $email,
        ];
        header('Location: /blog/public/register.php');
        exit;
    }
}
