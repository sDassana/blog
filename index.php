<?php
session_start();

// Always send users to the public recipe listing by default
header('Location: /blog/public/view_recipes.php');
exit;
