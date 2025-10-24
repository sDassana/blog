<?php
function redirect($path) {
    header("Location: /blog/public/$path");
    exit;
}
