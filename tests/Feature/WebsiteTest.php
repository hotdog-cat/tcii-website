<?php

namespace Tests\Feature;

use App\Models\ContentItem;
use App\Models\ContactMessage;
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

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('account-trigger', false)
            ->assertSee('account-avatar', false)
            ->assertSee('Sign Out');
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
            ->assertSee('Maintenance')
            ->assertSee('>Page Content</a>', false)
            ->assertDontSee('href="'.route('admin.page-content.edit', ['tab' => 'banner']).'"', false)
            ->assertDontSee('href="'.route('admin.page-content.edit', ['tab' => 'maintenance']).'"', false)
            ->assertDontSee('href="'.route('admin.page-content.edit', ['tab' => 'header-logo']).'"', false)
            ->assertDontSee('href="'.route('admin.page-content.edit', ['tab' => 'footer-logo']).'"', false)
            ->assertDontSee('Add Item');

        $this->actingAs($admin)
            ->get('/admin/page-content?tab=header-logo')
            ->assertOk()
            ->assertSee('aria-selected="true" data-content-tab="header-logo"', false)
            ->assertSee('data-content-tab="footer-logo"', false);

        $this->actingAs($admin)
            ->get('/admin/page-content?tab=maintenance')
            ->assertOk()
            ->assertSee('Public website status')
            ->assertSee('Website Under Maintenance')
            ->assertSee('Admin pages remain available');

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

    public function test_admin_can_enable_maintenance_mode_for_public_site(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $content = PageContent::DEFAULTS;
        $content['maintenance_enabled'] = '1';
        $content['maintenance_heading'] = 'Temporary Maintenance Window';
        $content['maintenance_message'] = 'For urgent dispatch requests, contact us:';
        $content['contact_email'] = 'urgent@example.com';

        $this->actingAs($admin)
            ->put('/admin/page-content', $content)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('page_contents', [
            'key' => 'maintenance_enabled',
            'value' => '1',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('"maintenance_enabled":"1"', false)
            ->assertSee('Temporary Maintenance Window')
            ->assertSee('For urgent dispatch requests, contact us:')
            ->assertSee('urgent@example.com');
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

    public function test_admin_can_leave_contact_telephone_numbers_blank(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $content = PageContent::DEFAULTS;
        $content['contact_telephones'] = '';

        $this->actingAs($admin)
            ->put('/admin/page-content', $content)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('page_contents', [
            'key' => 'contact_telephones',
            'value' => '',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('"contact_telephones":""', false);
    }

    public function test_admin_can_save_whole_sections_as_draft(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $content = PageContent::DEFAULTS;
        $content['section_team_published'] = '0';
        $content['section_products_published'] = '0';

        $this->actingAs($admin)
            ->get('/admin/page-content')
            ->assertOk()
            ->assertSee('Team publication status')
            ->assertSee('Products publication status')
            ->assertSee('>Draft</option>', false);

        $this->actingAs($admin)
            ->put('/admin/page-content', $content)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('page_contents', [
            'key' => 'section_team_published',
            'value' => '0',
        ]);
        $this->assertDatabaseHas('page_contents', [
            'key' => 'section_products_published',
            'value' => '0',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('"section_team_published":"0"', false)
            ->assertSee('"section_products_published":"0"', false);
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

    public function test_admin_content_item_type_is_locked_to_current_section(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $item = ContentItem::create([
            'type' => 'project',
            'title' => 'Existing Project',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.items.create', ['type' => 'project']))
            ->assertOk()
            ->assertSee('value="Projects"', false)
            ->assertSee('name="type" value="project"', false)
            ->assertDontSee('name="description"', false)
            ->assertDontSee('<select name="type"', false);

        $this->actingAs($admin)
            ->post(route('admin.items.store', ['type' => 'project']), [
                'type' => 'team',
                'title' => 'Locked New Project',
                'description' => 'This should not persist.',
                'sort_order' => 2,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.items.index', ['type' => 'project']));

        $this->assertDatabaseHas('content_items', [
            'type' => 'project',
            'title' => 'Locked New Project',
            'description' => null,
        ]);
        $this->assertDatabaseMissing('content_items', [
            'type' => 'team',
            'title' => 'Locked New Project',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.items.update', $item), [
                'type' => 'team',
                'title' => 'Still A Project',
                'description' => 'This should also not persist.',
                'sort_order' => 3,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.items.index', ['type' => 'project']));

        $this->assertSame('project', $item->fresh()->type);
        $this->assertNull($item->fresh()->description);
    }

    public function test_admin_description_field_is_identified_for_team_and_products(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $teamMember = ContentItem::create([
            'type' => 'team',
            'title' => 'Team Member',
            'subtitle' => 'Role',
            'description' => 'TM',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $product = ContentItem::create([
            'type' => 'product',
            'title' => 'Custom Product',
            'description' => 'Product copy shown publicly.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.items.create', ['type' => 'team']))
            ->assertOk()
            ->assertSee('Team marker / board placement')
            ->assertSee('name="description"', false)
            ->assertSee('BOARD', false);

        $this->actingAs($admin)
            ->get(route('admin.items.edit', $teamMember))
            ->assertOk()
            ->assertSee('Team marker / board placement')
            ->assertSee('TM');

        $this->actingAs($admin)
            ->get(route('admin.items.edit', $product))
            ->assertOk()
            ->assertSee('Product description')
            ->assertSee('Product copy shown publicly.');

        $this->actingAs($admin)
            ->put(route('admin.items.update', $product), [
                'title' => 'Custom Product',
                'description' => 'Updated public product copy.',
                'sort_order' => 1,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.items.index', ['type' => 'product']));

        $this->assertSame('Updated public product copy.', $product->fresh()->description);
    }

    public function test_admin_can_change_content_item_publication_status_from_actions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $item = ContentItem::create([
            'type' => 'facility',
            'title' => 'Action Controlled Facility',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.items.index', ['type' => 'facility']))
            ->assertOk()
            ->assertSee(route('admin.items.publication', $item), false)
            ->assertSee('>Draft</option>', false);

        $this->actingAs($admin)
            ->patch(route('admin.items.publication', $item), ['is_published' => '0'])
            ->assertRedirect(route('admin.items.index', ['type' => 'facility']))
            ->assertSessionHas('success');

        $this->assertFalse($item->fresh()->is_published);
        $this->get('/')->assertOk()->assertDontSee('Action Controlled Facility');

        $this->actingAs($admin)
            ->patch(route('admin.items.publication', $item), ['is_published' => '1'])
            ->assertRedirect(route('admin.items.index', ['type' => 'facility']))
            ->assertSessionHas('success');

        $this->assertTrue($item->fresh()->is_published);
        $this->get('/')->assertOk()->assertSee('Action Controlled Facility');
    }

    public function test_admin_can_manage_users_and_view_activity_log(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('User management');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Content Editor',
                'email' => 'editor@example.com',
                'contact_number' => '555-0100',
                'role' => User::ROLE_EDITOR,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'editor@example.com',
            'role' => User::ROLE_EDITOR,
            'contact_number' => '555-0100',
        ]);
        $this->assertDatabaseHas('activity_logs', ['action' => 'user.created']);

        $this->actingAs($admin)
            ->get(route('admin.activity-log.index'))
            ->assertOk()
            ->assertSee('System activity log')
            ->assertSee('user.created');
    }

    public function test_editor_can_access_cms_but_cannot_delete_or_open_admin_only_sections(): void
    {
        $editor = User::factory()->create(['role' => User::ROLE_EDITOR, 'is_admin' => false]);
        $item = ContentItem::create([
            'type' => 'facility',
            'title' => 'Editor Protected Facility',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $message = ContactMessage::create([
            'name' => 'Protected Sender',
            'email' => 'sender@example.com',
            'message' => 'Please do not delete this.',
        ]);

        $this->actingAs($editor)->get('/admin')->assertOk();
        $this->actingAs($editor)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.activity-log.index'))->assertForbidden();

        $this->actingAs($editor)
            ->get(route('admin.items.index', ['type' => 'facility']))
            ->assertOk()
            ->assertDontSee('Delete this item?', false);

        $this->actingAs($editor)->delete(route('admin.items.destroy', $item))->assertForbidden();
        $this->assertDatabaseHas('content_items', ['id' => $item->id]);

        $this->actingAs($editor)->delete(route('admin.messages.destroy', $message))->assertForbidden();
        $this->assertDatabaseHas('contact_messages', ['id' => $message->id]);
    }
}
