<?php
session_start();

// If the user is logged in → go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: /blog/public/dashboard.php");
    exit;
}

// If not logged in → go to public blog list
header("Location: /blog/public/view_recipes.php");
exit;
