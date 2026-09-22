<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;
use Illuminate\Support\Facades\Storage;

$articles = Article::orderBy('id')->get();
if ($articles->isEmpty()) {
    echo "No articles found.\n";
    exit;
}

foreach ($articles as $a) {
    $fi = $a->featured_image;
    $url = $a->featured_image_url;
    $basename = $fi ? basename($fi) : null;
    $publicPath1 = $fi ? __DIR__ . '/../public/' . ltrim($fi, '/') : null;
    $storagePath = $basename ? __DIR__ . '/../storage/app/public/article-images/' . $basename : null;
    $existsPublic = $publicPath1 ? file_exists($publicPath1) : false;
    $existsStorage = $storagePath ? file_exists($storagePath) : false;
    echo "id={$a->id} | featured_image={$fi} | featured_image_url={$url}\n";
    echo "  basename={$basename} | public_exists=" . ($existsPublic? 'yes':'no') . " | storage_exists=" . ($existsStorage? 'yes':'no') . "\n";
}
