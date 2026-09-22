<?php
// Auto-fix missing featured_image entries:
// - finds articles whose featured_image points to storage/... but file missing
// - attempts to locate matching files in storage/app/public/article-images by GUID or partial basename
// - updates articles.featured_image to /storage/article-images/<found> if match found
// - otherwise sets featured_image = NULL

$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) { echo ".env not found\n"; exit(1); }
$contents = file_get_contents($envFile);
$vars = [];
foreach (preg_split('/\r?\n/', $contents) as $line) {
    if (preg_match('/^DB_([A-Z_]+)=(.*)$/', $line, $m)) { $vars[$m[1]] = trim($m[2], " \t\n\r\0\x0B\""); }
}
$host = $vars['HOST'] ?? '127.0.0.1'; $db = $vars['DATABASE'] ?? null; $user = $vars['USERNAME'] ?? null; $pass = $vars['PASSWORD'] ?? null; $port = $vars['PORT'] ?? 3306;
if (!$db || !$user) { echo "Missing DB settings in .env. Found: " . json_encode($vars) . "\n"; exit(1); }
$dsn = "mysql:host={$host};dbname={$db};port={$port};charset=utf8mb4";
try { $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); } catch (Exception $e) { echo "PDO connect failed: " . $e->getMessage() . "\n"; exit(1); }

$storageDir = realpath(__DIR__ . '/../storage/app/public/article-images');
$files = [];
if ($storageDir && is_dir($storageDir)) {
    foreach (new DirectoryIterator($storageDir) as $fileinfo) {
        if ($fileinfo->isFile()) $files[] = $fileinfo->getFilename();
    }
}

$stmt = $pdo->query("SELECT id, featured_image FROM articles WHERE featured_image IS NOT NULL AND featured_image <> ''");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$updated = 0;
foreach ($rows as $r) {
    $id = $r['id'];
    $fi = $r['featured_image'];
    if (!$fi) continue;

    // get expected public path
    $publicPath = __DIR__ . '/../public/' . ltrim($fi, '/');
    if (file_exists($publicPath)) {
        // OK
        continue;
    }

    // missing — try to match
    $basename = basename($fi);

    // Try find by GUID inside basename (UUID pattern)
    $found = null;
    if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i', $basename, $m)) {
        $guid = $m[0];
        foreach ($files as $f) {
            if (stripos($f, $guid) !== false) { $found = $f; break; }
        }
    }

    // If not found, try matching by numeric prefix (timestamp)
    if (!$found) {
        if (preg_match('/^(\d{9,})_/', $basename, $m2)) {
            $prefix = $m2[1];
            foreach ($files as $f) {
                if (strpos($f, $prefix) === 0) { $found = $f; break; }
            }
        }
    }

    // If still not found, try partial name tokens
    if (!$found) {
        $tokens = preg_split('/[_\-\. ]+/', preg_replace('/\.[^.]+$/', '', $basename));
        foreach ($files as $f) {
            $lower = strtolower($f);
            $matchCount = 0;
            foreach ($tokens as $t) {
                $t = trim($t);
                if (strlen($t) < 3) continue;
                if (strpos($lower, strtolower($t)) !== false) $matchCount++;
            }
            if ($matchCount >= 2) { $found = $f; break; }
        }
    }

    if ($found) {
        $new = '/storage/article-images/' . $found;
        $pdo->prepare('UPDATE articles SET featured_image = ? WHERE id = ?')->execute([$new, $id]);
        echo "Updated id={$id} -> {$new}\n";
        $updated++;
    } else {
        // No match — clear it so UI shows 'No image' instead of broken
        $pdo->prepare('UPDATE articles SET featured_image = NULL WHERE id = ?')->execute([$id]);
        echo "Cleared id={$id} featured_image (file missing, no match)\n";
        $updated++;
    }
}

echo "Done. {$updated} rows changed.\n";
