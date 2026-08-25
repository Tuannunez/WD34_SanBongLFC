<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '0988888888',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => true,
        ]);

        $this->actingAs($admin);
    }

    public function test_admin_can_create_role_and_redirect_to_index(): void
    {
        $this->actingAsAdmin();

        $response = $this->post('/admin/roles', [
            'name' => 'Quản lý đặt sân',
            'slug' => 'booking-manager',
            'description' => 'Quản lý đặt sân',
            'status' => 'on',
        ]);

        $response->assertRedirect('/admin/roles');
        $this->assertDatabaseHas('roles', [
            'slug' => 'booking-manager',
            'name' => 'Quản lý đặt sân',
        ]);
    }
}
