<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StadiumsCrudTest extends TestCase
{
    public function test_admin_can_create_stadium_without_price_field(): void
    {
        $fieldTypeId = DB::table('field_types')->value('id');

        $response = $this->post('/admin/stadiums', [
            'name' => 'Sân bóng test',
            'field_type_id' => $fieldTypeId,
            'address' => '123 Nguyễn Trãi',
            'open_time' => '08:00',
            'close_time' => '22:00',
        ]);

        $response->assertRedirect('/admin/stadiums');
        $this->assertDatabaseHas('stadiums', ['name' => 'Sân bóng test']);
    }
}
