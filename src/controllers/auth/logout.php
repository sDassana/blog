<?php
// src/controllers/auth/logout.php
session_start();
session_unset();  // Remove all session variables
session_destroy(); // End the session

// Optional: prevent caching of authenticated pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Redirect back to login page
require_once __DIR__ . '/../../helpers/redirect.php';
redirect('login.php');
