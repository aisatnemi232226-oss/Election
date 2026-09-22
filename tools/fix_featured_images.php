<?php
// One-off script to normalize featured_image paths in the articles table.
// Run: php tools/fix_featured_images.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Running featured_image path normalization...\n";

$affected = DB::table('articles')
    ->where('featured_image', 'like', '/storage/featured-images/%')
    ->update([
        'featured_image' => DB::raw("REPLACE(featured_image, '/storage/featured-images/', '/storage/article-images/')")
    ]);

echo "Updated $affected rows.\n";

if ($affected > 0) {
    echo "Done. Please refresh the admin page in your browser.\n";
} else {
    echo "No rows matched the pattern.\n";
}
