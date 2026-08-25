<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_includes_phone_field(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('name="phone"', false);
        $response->assertSee('placeholder="Số điện thoại"', false);
    }

    public function test_user_phone_is_saved_when_registering(): void
    {
        $response = $this->post('/register', [
            'name' => 'Nguyễn Văn A',
            'email' => 'test@example.com',
            'phone' => '0987654321',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'phone' => '0987654321',
        ]);
        $this->assertDatabaseCount('users', 1);
        $this->assertTrue(User::where('email', 'test@example.com')->first()->phone === '0987654321');
    }
}
