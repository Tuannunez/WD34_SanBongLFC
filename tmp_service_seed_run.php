<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$items = [
    ['name' => 'Thuê sân bóng', 'description' => 'Đặt sân theo khung giờ', 'price' => 200000, 'unit' => 'giờ'],
    ['name' => 'Nước uống', 'description' => 'Nước suối, nước ngọt', 'price' => 10000, 'unit' => 'chai'],
    ['name' => 'Thuê bóng', 'description' => 'Bóng thi đấu chất lượng', 'price' => 50000, 'unit' => 'trận'],
    ['name' => 'Áo bib', 'description' => 'Áo phân đội', 'price' => 20000, 'unit' => 'bộ'],
    ['name' => 'Thuê găng tay', 'description' => 'Găng tay thủ môn', 'price' => 30000, 'unit' => 'cặp'],
    ['name' => 'Phòng tắm', 'description' => 'Sử dụng sau khi đá bóng', 'price' => 0, 'unit' => 'lượt'],
    ['name' => 'Bãi gửi xe', 'description' => 'Gửi xe cho khách', 'price' => 5000, 'unit' => 'xe'],
];

foreach ($items as $item) {
    Service::updateOrCreate(['name' => $item['name']], $item);
}

$services = Service::whereIn('name', array_column($items, 'name'))->get();
$summary = [];
foreach ($services as $service) {
    $summary[] = $service->name . '|' . $service->price . '|' . ($service->unit ?? '') . '|' . $service->description;
}
file_put_contents(__DIR__ . '/tmp_service_seed_result.txt', implode("\n", $summary));
echo "DONE";
