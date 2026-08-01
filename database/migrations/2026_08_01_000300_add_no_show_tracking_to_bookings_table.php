<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $database = DB::connection()->getDatabaseName();

        $columnExists = static function (string $column) use ($database): bool {
            return DB::table('information_schema.columns')
                ->where('table_schema', $database)
                ->where('table_name', 'bookings')
                ->where('column_name', $column)
                ->exists();
        };

        $missing = [
            'no_show_at' => ! $columnExists('no_show_at'),
            'deposit_forfeited_amount' => ! $columnExists('deposit_forfeited_amount'),
            'check_in_source' => ! $columnExists('check_in_source'),
        ];

        if (! in_array(true, $missing, true)) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) use ($missing): void {
            if ($missing['no_show_at']) {
                $table->timestamp('no_show_at')->nullable();
            }

            if ($missing['deposit_forfeited_amount']) {
                $table->decimal('deposit_forfeited_amount', 15, 2)->default(0);
            }

            if ($missing['check_in_source']) {
                $table->string('check_in_source', 32)->nullable();
            }
        });
    }

    public function down(): void
    {
        // Không tự drop để tránh mất dữ liệu đối soát no-show và tiền cọc đã giữ.
    }
};
