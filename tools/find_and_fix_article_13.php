<?php
// boots Laravel and inspects article id=13, searching storage for close filename matches
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;

$id = 13;
$a = Article::find($id);
if (!$a) {
    echo "Article $id not found\n";
    exit(1);
}
$fi = $a->featured_image;
echo "Article id={$a->id} title={$a->title}\n";
echo "DB featured_image: " . ($fi ?: 'NULL') . "\n";

$searchTokens = [];
if ($fi) {
    // try basename
    $bn = basename($fi);
    $searchTokens[] = $bn;
    // try remove extension if any
    $searchTokens[] = preg_replace('/\.[^.]+$/', '', $bn);
    // split on non-alphanum
    $parts = preg_split('/[^A-Za-z0-9_-]+/', $bn);
    foreach ($parts as $p) if (strlen($p) >= 6) $searchTokens[] = $p;
}
$searchTokens[] = 'featured';
$searchTokens = array_unique($searchTokens);

$storagePath = __DIR__ . '/../storage/app/public';
if (!is_dir($storagePath)) {
    echo "Storage path not found: $storagePath\n";
    exit(1);
}

echo "Searching storage for tokens: " . implode(', ', $searchTokens) . "\n";
$matches = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($storagePath));
foreach ($it as $file) {
    if (!$file->isFile()) continue;
    $name = $file->getFilename();
    foreach ($searchTokens as $t) {
        if ($t === '') continue;
        if (stripos($name, $t) !== false) {
            $matches[$file->getRealPath()] = $name;
            break;
        }
    }
}
if (empty($matches)) {
    echo "No matching files found in storage for article $id.\n";
    exit(0);
}

echo "Found matches:\n";
foreach ($matches as $path => $name) {
    $rel = str_replace(realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR, '', $path);
    echo "- $rel\n";
}

// If there's an obvious match (exact basename match), propose update
$bn = basename($fi);
foreach ($matches as $path => $name) {
    if (strcasecmp($name, $bn) === 0) {
        echo "\nExact basename match found: $name\n";
        $newPath = '/storage/' . str_replace('\\', '/', substr($path, strlen(realpath(__DIR__ . '/../storage/app/public/')) + strlen('/storage/app/public/')));
        // craft relative public storage path
        $newPath = '/storage' . str_replace(realpath(__DIR__ . '/../storage/app/public'), '', $path);
        $newPath = str_replace('\\', '/', $newPath);
        echo "Would update article->$id featured_image to: $newPath\n";
        exit(0);
    }
}

echo "No exact basename match; shown candidate files above. Pick one and I can update DB to point to it.\n";
