<?php
// Repair script: for each article with featured_image, ensure the referenced public file exists under public/storage.
// If not, set featured_image to a fallback existing article-image (first in directory) or NULL.
// Run: php tools/repair_featured_images.php

$publicStorage = __DIR__ . '/../public/storage';
$articleImagesDir = $publicStorage . '/article-images';

if (!is_dir($articleImagesDir)) {
    echo "article-images directory not found at: {$articleImagesDir}\n";
    exit(1);
}

$files = array_values(array_filter(scandir($articleImagesDir), function($f){ return !in_array($f, ['.','..']); }));
if (empty($files)) {
    echo "No files found in article-images to use as fallback.\n";
}
$fallback = !empty($files) ? '/storage/article-images/' . $files[0] : null;

// Read DB via .env and PDO
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) { echo ".env not found\n"; exit(1); }
$contents = file_get_contents($envFile);
$vars = [];
foreach (preg_split('/\r?\n/', $contents) as $line) {
    if (preg_match('/^DB_([A-Z_]+)=(.*)$/', $line, $m)) { $vars[$m[1]] = trim($m[2], " \t\n\r\0\x0B\""); }
}
$host = $vars['HOST'] ?? '127.0.0.1'; $db = $vars['DATABASE'] ?? null; $user = $vars['USERNAME'] ?? null; $pass = $vars['PASSWORD'] ?? null; $port = $vars['PORT'] ?? 3306;
if (!$db || !$user) { echo "Missing DB settings. Found: " . json_encode($vars) . "\n"; exit(1); }
$dsn = "mysql:host={$host};dbname={$db};port={$port};charset=utf8mb4";
try { $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); } catch (Exception $e) { echo "PDO connect failed: " . $e->getMessage() . "\n"; exit(1); }

$stmt = $pdo->query("SELECT id, title, featured_image FROM articles ORDER BY id ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$updated = 0;
foreach ($rows as $r) {
    $id = $r['id'];
    $fi = $r['featured_image'];
    if (empty($fi)) continue;

    // compute public path
    $publicPath = __DIR__ . '/../public/' . ltrim($fi, '/');
    if (!file_exists($publicPath)) {
        echo "Article id={$id} filename missing: {$fi}\n";
        if ($fallback) {
            $new = $fallback;
            $pdo->prepare("UPDATE articles SET featured_image = ? WHERE id = ?")->execute([$new, $id]);
            echo "  -> updated to fallback: {$new}\n";
            $updated++;
        } else {
            $pdo->prepare("UPDATE articles SET featured_image = NULL WHERE id = ?")->execute([$id]);
            echo "  -> cleared featured_image\n";
            $updated++;
        }
    }
}

echo "Repair complete. {$updated} rows changed.\n";
