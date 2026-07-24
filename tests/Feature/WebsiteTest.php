<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_renders_and_registry_is_hidden_by_default(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Engineering Strength.')
            ->assertDontSee('Built on verified standards.');
    }

    public function test_contact_form_stores_an_inquiry(): void
    {
        $this->post('/contact', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'message' => 'We need ready-mixed concrete.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_messages', ['email' => 'client@example.com']);
    }

    public function test_admin_can_open_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }
}
