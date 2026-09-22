<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ElectionArticleController;
use App\Models\ElectionArticle;

$ea = ElectionArticle::first();
if (! $ea) {
    echo "No ElectionArticle records found\n";
    exit(0);
}
$id = $ea->id;
echo "Using election_article id: $id\n";

$ctrl = new ElectionArticleController();
$response = $ctrl->show($id);

// response is a JsonResponse
if ($response instanceof Illuminate\Http\JsonResponse) {
    echo $response->getContent() . PHP_EOL;
} else {
    var_dump($response);
}
