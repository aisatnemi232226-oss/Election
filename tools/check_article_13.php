<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;
$a = Article::find(13);
if (!$a) {
    echo "Article 13 not found\n";
    exit(1);
}
echo "DB: " . ($a->featured_image ?? 'NULL') . "\n";
echo "URL: " . ($a->featured_image_url ?? 'NULL') . "\n";
$publicPath = public_path('storage/article-images/' . basename($a->featured_image));
echo "Public path: " . $publicPath . "\n";
if (file_exists($publicPath)) {
    echo "Public file exists\n";
} else {
    echo "Public file MISSING\n";
}
