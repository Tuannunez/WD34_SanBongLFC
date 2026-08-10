<?php
// Quick test file to verify models are working
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    // Test models
    $models = [
        'App\Models\FieldTypeBasePrice',
        'App\Models\TimeSlotSurcharge',
        'App\Models\TimeSlot',
        'App\Models\FieldTimeSlotPrice',
        'App\Models\StadiumTimeSlotPrice',
    ];

    echo "🔍 Kiểm tra các models...\n";
    echo str_repeat('=', 50) . "\n";

    $successCount = 0;
    foreach ($models as $class) {
        if (class_exists($class)) {
            echo "✅ " . basename($class) . " - OK\n";
            $successCount++;
        } else {
            echo "❌ " . basename($class) . " - NOT FOUND\n";
        }
    }

    echo str_repeat('=', 50) . "\n";
    echo "✓ Kết quả: $successCount/" . count($models) . " models hoạt động\n\n";

    // Test controller
    echo "🔍 Kiểm tra Controller...\n";
    echo str_repeat('=', 50) . "\n";

    $controllerClass = 'App\Http\Controllers\Admin\TimeSlotsController';
    if (class_exists($controllerClass)) {
        echo "✅ TimeSlotsController - OK\n";
        
        // Check methods
        $methods = [
            'pricing',
            'storeFieldTypePrice',
            'addTimeSurcharge',
            'deleteTimeSurcharge',
            'updateTimeSlotInfo',
            'bulkUpdatePrices',
            'exportPrices',
        ];

        $reflection = new ReflectionClass($controllerClass);
        $existingMethods = [];
        foreach ($methods as $method) {
            if ($reflection->hasMethod($method)) {
                echo "  ✓ Method: $method\n";
                $existingMethods[] = $method;
            } else {
                echo "  ✗ Method: $method (missing)\n";
            }
        }

        echo "\n✓ Tìm thấy " . count($existingMethods) . "/" . count($methods) . " methods mới\n";
    } else {
        echo "❌ TimeSlotsController - NOT FOUND\n";
    }

    echo str_repeat('=', 50) . "\n";
    echo "\n✨ Tất cả kiểm tra hoàn tất!\n";
    echo "\n📚 Tài liệu: Xem file HUONG_DAN_GIA_LINH_HOAT.md\n";

} catch (\Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
}
