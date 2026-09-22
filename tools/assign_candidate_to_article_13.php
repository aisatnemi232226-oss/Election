<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;

$id = 13;
$article = Article::find($id);
if (!$article) {
    echo "Article $id not found\n";
    exit(1);
}

// Candidate path that exists in storage
$newFeatured = '/storage/article-images/1745985664_featured-image-results-e1729526694256.jpg';

$article->featured_image = $newFeatured;
$article->save();

echo "Updated article id={$article->id} title={$article->title}\n";
echo "DB featured_image now: " . ($article->featured_image ?? 'NULL') . "\n";
// reload model to ensure accessor works
$article = Article::find($id);
echo "Model featured_image_url: " . ($article->featured_image_url ?? 'NULL') . "\n";

// Check file existence on disk
$publicPath = realpath(__DIR__ . '/../public');
$storagePublic = realpath(__DIR__ . '/../storage/app/public');
$relative = str_replace('/storage', '', $article->featured_image);
$diskPath = $storagePublic . str_replace('/', DIRECTORY_SEPARATOR, $relative);
if (file_exists($diskPath)) {
    echo "File exists on disk: $diskPath\n";
} else {
    echo "File NOT found on disk at: $diskPath\n";
}

echo "Done. Refresh admin page to see the change.\n";
