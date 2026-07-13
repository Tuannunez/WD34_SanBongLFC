<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo 'stadiums=' . DB::table('stadiums')->count() . PHP_EOL;
echo 'fields=' . DB::table('fields')->count() . PHP_EOL;
foreach (DB::table('stadiums')->select('id', 'name')->get() as $stadium) {
    echo $stadium->id . ':' . $stadium->name . PHP_EOL;
}
