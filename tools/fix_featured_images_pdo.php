<?php
// One-off PDO-based script to update featured_image paths without bootstrapping Laravel.
// Run: php tools/fix_featured_images_pdo.php

$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    echo ".env file not found at $envFile\n";
    exit(1);
}

$contents = file_get_contents($envFile);
$vars = [];
foreach (preg_split('/\r?\n/', $contents) as $line) {
    if (preg_match('/^DB_([A-Z_]+)=(.*)$/', $line, $m)) {
        $vars[$m[1]] = trim($m[2], " \t\n\r\0\x0B\"");
    }
}

$host = $vars['HOST'] ?? ($vars['HOST'] ?? '127.0.0.1');
$db   = $vars['DATABASE'] ?? null;
$user = $vars['USERNAME'] ?? null;
$pass = $vars['PASSWORD'] ?? null;
$port = $vars['PORT'] ?? 3306;

if (!$db || !$user) {
    echo "Missing DB settings in .env. Detected values: " . json_encode($vars) . "\n";
    exit(1);
}

$dsn = "mysql:host={$host};dbname={$db};port={$port};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "PDO connect failed: " . $e->getMessage() . "\n";
    exit(1);
}

$sql = "UPDATE `articles` SET `featured_image` = REPLACE(`featured_image`, '/storage/featured-images/', '/storage/article-images/') WHERE `featured_image` LIKE '/storage/featured-images/%'";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "Updated {$count} rows.\n";
} catch (Exception $e) {
    echo "SQL error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Done. Refresh the admin page.\n";
