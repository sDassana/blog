<?php
require __DIR__ . '/config.php';

try {
    $stmt = $pdo->query('SELECT 1 AS ok');
    $row = $stmt->fetch();
    if ($row && isset($row['ok'])) {
        echo "DB connection OK — SELECT 1 returned: " . $row['ok'];
    } else {
        echo "DB connection seems open but query failed.";
    }
} catch (Exception $e) {
    echo "DB test failed: " . htmlspecialchars($e->getMessage());
}
