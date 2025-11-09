<?php
// Store a one-time message on the session for display after a redirect.
function setFlash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

// Retrieve and clear a previously set flash message of the given type.
function getFlash($type) {
    if (!empty($_SESSION['flash'][$type])) {
        $msg = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $msg;
    }
    return null;
}
?>
