<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class StadiumsCrudTest extends TestCase
{
    public function test_admin_can_create_stadium_without_price_field(): void
    {
        $response = $this->post('/admin/stadiums', [
            'name' => 'Sân bóng test',
            'address' => '123 Nguyễn Trãi',
            'open_time' => '08:00',
            'close_time' => '22:00',
        ]);

        $response->assertRedirect('/admin/stadiums');
        $this->assertDatabaseHas('stadiums', ['name' => 'Sân bóng test']);
    }
}
