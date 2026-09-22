<?php
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

$stmt = $pdo->query("SELECT id, title, featured_image FROM articles WHERE featured_image IS NOT NULL AND featured_image <> '' LIMIT 20");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) { echo "No non-empty featured_image rows found.\n"; exit(0); }
foreach ($rows as $r) {
    echo "id={$r['id']} | title={$r['title']} | featured_image={$r['featured_image']}\n";
}
