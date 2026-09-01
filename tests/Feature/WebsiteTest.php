<?php

namespace Tests\Feature;

use App\Models\ContentItem;
use App\Models\User;
use App\Models\PageContent;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_renders_and_registry_is_hidden_by_default(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Concrete confidence')
            ->assertDontSee('"registry":', false);
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

    public function test_public_homepage_includes_every_published_content_item_and_excludes_hidden_items(): void
    {
        foreach (array_keys(ContentItem::TYPES) as $type) {
            for ($index = 1; $index <= 6; $index++) {
                ContentItem::create([
                    'type' => $type,
                    'title' => "Published {$type} {$index}",
                    'sort_order' => $index,
                    'is_published' => true,
                ]);
            }

            ContentItem::create([
                'type' => $type,
                'title' => "Hidden {$type}",
                'sort_order' => 99,
                'is_published' => false,
            ]);
        }

        $response = $this->get('/')->assertOk();

        foreach (array_keys(ContentItem::TYPES) as $type) {
            for ($index = 1; $index <= 6; $index++) {
                $response->assertSee("Published {$type} {$index}");
            }

            $response->assertDontSee("Hidden {$type}");
        }
    }

    public function test_admin_can_open_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_admin_can_edit_fixed_page_content_and_contact_details(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $content = PageContent::DEFAULTS;
        $content['about_heading'] = "Custom concrete heading\nfrom the admin.";
        $content['vision_title'] = 'A newly edited vision.';
        $content['hero_heading'] = "Custom hero heading\nfrom the admin.";
        $content['team_intro'] = 'A custom team introduction.';
        $content['inquiry_name_label'] = 'Your full name';
        $content['footer_tagline'] = 'A custom footer tagline.';
        $content['contact_address'] = "New Plant Road\nBacolod City";
        $content['contact_telephones'] = "034-555-0100\n034-555-0101";
        $content['contact_email'] = 'sales@example.com';

        $this->actingAs($admin)
            ->get('/admin/page-content')
            ->assertOk()
            ->assertSee('Content only')
            ->assertSee('Statistic 1 value')
            ->assertSee('Contact Details')
            ->assertSee('Telephone numbers')
            ->assertSee('Hero')
            ->assertSee('Inquiry Form')
            ->assertSee('Footer')
            ->assertSee('Home Banner')
            ->assertSee('Header Logo')
            ->assertSee('Footer Logo')
            ->assertSee('>Page Content</a>', false)
            ->assertDontSee('href="'.route('admin.page-content.edit', ['tab' => 'banner']).'"', false)
            ->assertDontSee('href="'.route('admin.page-content.edit', ['tab' => 'header-logo']).'"', false)
            ->assertDontSee('href="'.route('admin.page-content.edit', ['tab' => 'footer-logo']).'"', false)
            ->assertDontSee('Add Item');

        $this->actingAs($admin)
            ->get('/admin/page-content?tab=header-logo')
            ->assertOk()
            ->assertSee('aria-selected="true" data-content-tab="header-logo"', false)
            ->assertSee('data-content-tab="footer-logo"', false);

        $this->actingAs($admin)
            ->put('/admin/page-content', $content)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('page_contents', [
            'key' => 'about_heading',
            'value' => "Custom concrete heading\nfrom the admin.",
        ]);

        $this->assertDatabaseHas('page_contents', [
            'key' => 'contact_email',
            'value' => 'sales@example.com',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Custom concrete heading')
            ->assertSee('Custom hero heading')
            ->assertSee('A newly edited vision.')
            ->assertSee('A custom team introduction.')
            ->assertSee('Your full name')
            ->assertSee('A custom footer tagline.')
            ->assertSee('New Plant Road')
            ->assertSee('034-555-0101')
            ->assertSee('sales@example.com');
    }

    public function test_admin_can_upload_separate_home_banner_and_logos(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $content = PageContent::DEFAULTS;
        $content['hero_background_upload'] = UploadedFile::fake()->image('home-banner.jpg', 1920, 1080);
        $content['header_logo_upload'] = UploadedFile::fake()->image('header-logo.png', 640, 180);
        $content['footer_logo_upload'] = UploadedFile::fake()->image('footer-logo.png', 640, 180);
        $content['header_logo_width'] = '310';
        $content['footer_logo_width'] = '240';

        $this->actingAs($admin)
            ->put('/admin/page-content', $content)
            ->assertRedirect()
            ->assertSessionHas('success');

        foreach (['hero_background_image', 'header_logo_image', 'footer_logo_image'] as $key) {
            $value = PageContent::query()->where('key', $key)->value('value');
            $this->assertStringStartsWith('/storage/page-content/', $value);
            Storage::disk('public')->assertExists(str_replace('/storage/', '', $value));
        }

        $this->assertDatabaseHas('page_contents', ['key' => 'header_logo_width', 'value' => '310']);
        $this->assertDatabaseHas('page_contents', ['key' => 'footer_logo_width', 'value' => '240']);
    }

    public function test_admin_can_remove_page_media_and_restore_default(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('page-content/custom-banner.jpg', 'test');
        $admin = User::factory()->create(['is_admin' => true]);
        PageContent::create([
            'key' => 'hero_background_image',
            'value' => '/storage/page-content/custom-banner.jpg',
        ]);

        $this->actingAs($admin)
            ->put('/admin/page-content', ['remove_media' => 'hero_background_image'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('page_contents', [
            'key' => 'hero_background_image',
            'value' => PageContent::DEFAULTS['hero_background_image'],
        ]);
        Storage::disk('public')->assertMissing('page-content/custom-banner.jpg');
    }

    public function test_admin_can_remove_content_item_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('content/custom-image.jpg', 'test');
        $admin = User::factory()->create(['is_admin' => true]);
        $item = ContentItem::create([
            'type' => 'facility',
            'title' => 'Custom Facility',
            'image_path' => 'content/custom-image.jpg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.items.update', $item), ['remove_image' => '1'])
            ->assertRedirect(route('admin.items.edit', $item))
            ->assertSessionHas('success');

        $this->assertNull($item->fresh()->image_path);
        Storage::disk('public')->assertMissing('content/custom-image.jpg');
    }
}
