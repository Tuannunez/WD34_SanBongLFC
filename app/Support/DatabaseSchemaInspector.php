<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

final class DatabaseSchemaInspector
{
    /** @var array<string, bool> */
    private array $tableCache = [];

    /** @var array<string, bool> */
    private array $columnCache = [];

    public function tableExists(string $table): bool
    {
        return $this->tableCache[$table] ??= DB::table('information_schema.tables')
            ->where('table_schema', DB::connection()->getDatabaseName())
            ->where('table_name', $table)
            ->exists();
    }

    public function columnExists(string $table, string $column): bool
    {
        $key = $table.'.'.$column;

        return $this->columnCache[$key] ??= DB::table('information_schema.columns')
            ->where('table_schema', DB::connection()->getDatabaseName())
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->exists();
    }

    /**
     * Chỉ giữ lại các phần tử có tên cột thực sự tồn tại trong bảng.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function filterColumns(string $table, array $data): array
    {
        return collect($data)
            ->filter(fn (mixed $value, string $column): bool => $this->columnExists($table, $column))
            ->all();
    }
}
