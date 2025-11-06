<?php
// src/helpers/session.php
// Secure session starter and flash message helper

if (!function_exists('start_secure_session')) {
    function start_secure_session(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Custom secure settings
            $secure   = false; // set true if using HTTPS
            $httponly = true;
            $samesite = 'Lax'; // can be 'Strict' if you want stricter

            // Prevent resetting params if headers already sent
            if (!headers_sent()) {
                session_set_cookie_params([
                    'lifetime' => 0,
                    'path' => '/',
                    'domain' => '',
                    'secure' => $secure,
                    'httponly' => $httponly,
                    'samesite' => $samesite
                ]);
            }

            @session_start(); // suppress warning if called twice quickly
        }
    }
}

// Flash message helper (shows once)
if (!function_exists('flash')) {
    function flash(string $key, ?string $message = null) {
        start_secure_session();
        if ($message === null) {
            if (!empty($_SESSION['_flash'][$key])) {
                $msg = $_SESSION['_flash'][$key];
                unset($_SESSION['_flash'][$key]);
                return $msg;
            }
            return null;
        } else {
            $_SESSION['_flash'][$key] = $message;
        }
    }
}
