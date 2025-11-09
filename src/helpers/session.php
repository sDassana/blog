<?php
// Secure session bootstrapping utilities shared across front-facing controllers.

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

