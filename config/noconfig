<?php
//config/config.php
//loads .env and creates a PDO $pdo connection for the app.
// Remember: should not expose this file publicly.

// Back-compat: define no-op CSRF helpers if legacy controllers still reference them
if (!function_exists('csrf_abort_if_invalid_redirect')) {
    function csrf_abort_if_invalid_redirect() { /* CSRF disabled: no-op */ }
}
if (!function_exists('csrf_input')) {
    function csrf_input() { return ''; }
}

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        static $env = null;
        if ($env === null) {
            $env = [];
            $path = __DIR__ . '/../.env';
            if (file_exists($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#')) continue;
                    if (!str_contains($line, '=')) continue;
                    [$k, $v] = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v);

                    //remove surrounding quotes
                    $v = preg_replace('/^"(.*)"$/s', '$1', $v);
                    $v = preg_replace("/^'(.*)'$/s", '$1', $v);
                    $env[$k] = $v;

                }
            }
        }
        return $env[$key] ?? $default;
    }
}

$DB_HOST = env('DB_HOST', '127.0.0.1');
$DB_NAME = env('DB_NAME', 'recipe_blog_app');
$DB_USER = env('DB_USER', 'root');
$DB_PASS = env('DB_PASS', '');
$DB_PORT = env('DB_PORT', 3306);

// PDO DSN with charset utf8mb4
// PDO : PHP Data Object
// DSN : Data Source Name
$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};port={$DB_PORT};charset=utf8mb4";

try {
    // PDO options - exceptions, persistent disabled, proper emulation setting
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => false,
    ];
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);

} catch (PDOException $e) {
    // In development, show a helpful error (but never echo raw exception in production)
    // We log details to file and show a generic message to user.
    $msg = "Database connection failed: " . $e->getMessage();
    // Ensure logs folder exists
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0750, true);
    @file_put_contents($logDir . '/errors.log', "[".date('Y-m-d H:i:s')."] ".$msg.PHP_EOL, FILE_APPEND);

    // Display a error message(not the row error) so user know to check logs
    http_response_code(500);
    echo "We are experiencing technical difficulties. Please try again later.";
    // Stop execution
    exit;
}   