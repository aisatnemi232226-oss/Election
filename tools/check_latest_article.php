<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$a = App\Models\Article::latest()->first();
if (! $a) {
    echo "No articles found\n";
    exit(0);
}

echo "DB: " . ($a->featured_image ?? 'NULL') . PHP_EOL;
echo "Model URL: " . ($a->featured_image_url ?? 'NULL') . PHP_EOL;
