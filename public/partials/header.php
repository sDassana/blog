<?php
// Reusable <head> partial.
// Usage: set $pageTitle before including, e.g. $pageTitle = 'Login · The Cookie Lovestoblog';
// Falls back to a default site title if $pageTitle not set.
// Add extra per-page head tags by defining $extraHead (string of HTML) before include.
// Ensure config.php loaded earlier if you need session-dependent logic.
if (!isset($pageTitle)) {
	$pageTitle = 'The Cookie Lovestoblog';
}
?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
<!-- Tailwind CDN (v4 runtime). Loaded BEFORE app.css so existing compiled styles keep precedence. -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<link rel="stylesheet" href="/blog/public/css/app.css" />
<?php if (!empty($extraHead)) echo $extraHead; ?>
