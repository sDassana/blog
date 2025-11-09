<?php
// Tiny helper to centralize redirects back into the public directory.
function redirect($path) {
    header("Location: /blog/public/$path");
    exit;
}
