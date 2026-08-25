<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginPhoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_phone_number(): void
    {
        User::factory()->create([
            'name' => 'Nguyễn Văn A',
            'email' => 'user@example.com',
            'phone' => '0987654321',
            'password' => Hash::make('password123'),
            'status' => true,
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'login' => '0987654321',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }
}
