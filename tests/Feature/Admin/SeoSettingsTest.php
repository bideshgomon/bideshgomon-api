<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\SeoSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeoSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles
        $this->artisan('db:seed', ['--class' => 'RolesSeeder']);
    }

    /** @test */
    public function admin_can_view_seo_settings_page()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $response = $this->actingAs($admin)->get(route('seo-settings.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/SeoSettings/Index')
            ->has('settings')
            ->has('pageTypes')
        );
    }

    /** @test */
    public function non_admin_cannot_access_seo_settings()
    {
        $userRole = Role::where('slug', 'user')->first();
        $user = User::factory()->create(['role_id' => $userRole->id]);

        $response = $this->actingAs($user)->get(route('seo-settings.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_seo_settings()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $data = [
            'title' => 'BideshGomon - Test Page',
            'description' => 'Test description for SEO',
            'keywords' => 'test, seo, bangladesh',
            'og_title' => 'Test OG Title',
            'og_description' => 'Test OG Description',
            'og_image' => 'https://example.com/image.jpg',
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => 'Test Twitter Title',
            'twitter_description' => 'Test Twitter Description',
            'twitter_image' => 'https://example.com/twitter.jpg',
            'twitter_site' => '@BideshGomon',
            'schema_markup' => [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'BideshGomon'
            ],
            'index' => true,
            'follow' => true,
            'robots' => 'max-snippet:-1'
        ];

        $response = $this->actingAs($admin)->put(route('seo-settings.update', 'home'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('seo_settings', [
            'page_type' => 'home',
            'title' => 'BideshGomon - Test Page',
            'keywords' => 'test, seo, bangladesh',
            'twitter_site' => '@BideshGomon'
        ]);
    }

    /** @test */
    public function seo_settings_are_cached()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $data = [
            'title' => 'Cached Title',
            'description' => 'Cached Description',
            'index' => true,
            'follow' => true
        ];

        // Create setting
        $this->actingAs($admin)->put(route('seo-settings.update', 'home'), $data);

        // First call - should cache
        $setting1 = SeoSetting::getForPage('home');
        
        // Second call - should retrieve from cache
        $setting2 = SeoSetting::getForPage('home');

        $this->assertEquals($setting1->title, $setting2->title);
        $this->assertEquals('Cached Title', $setting1->title);
    }

    /** @test */
    public function admin_can_delete_seo_settings()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // Create a setting first
        SeoSetting::create([
            'page_type' => 'home',
            'title' => 'Test Title',
            'description' => 'Test Description',
            'index' => true,
            'follow' => true
        ]);

        $response = $this->actingAs($admin)->delete(route('seo-settings.destroy', 'home'));

        $response->assertRedirect();
        $this->assertDatabaseMissing('seo_settings', [
            'page_type' => 'home'
        ]);
    }

    /** @test */
    public function admin_can_generate_sitemap()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // Create some SEO settings with indexing enabled
        SeoSetting::create([
            'page_type' => 'home',
            'title' => 'Home',
            'description' => 'Home page',
            'index' => true,
            'follow' => true
        ]);

        SeoSetting::create([
            'page_type' => 'services',
            'title' => 'Services',
            'description' => 'Services page',
            'index' => true,
            'follow' => true
        ]);

        SeoSetting::create([
            'page_type' => 'about',
            'title' => 'About',
            'description' => 'About page',
            'index' => false, // This should not be in sitemap
            'follow' => true
        ]);

        $response = $this->actingAs($admin)->post(route('seo-settings.generate-sitemap'));

        $response->assertRedirect();
        $this->assertFileExists(public_path('sitemap.xml'));

        // Check sitemap content
        $sitemap = file_get_contents(public_path('sitemap.xml'));
        $this->assertStringContainsString('<loc>http://localhost</loc>', $sitemap); // home
        $this->assertStringContainsString('<loc>http://localhost/services</loc>', $sitemap); // services
        $this->assertStringNotContainsString('<loc>http://localhost/about</loc>', $sitemap); // about (not indexed)
    }

    /** @test */
    public function seo_validation_rejects_invalid_data()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // Test with invalid URL
        $data = [
            'title' => 'Test',
            'canonical_url' => 'not-a-valid-url',
            'og_image' => 'also-not-a-url',
            'index' => true,
            'follow' => true
        ];

        $response = $this->actingAs($admin)->put(route('seo-settings.update', 'home'), $data);

        $response->assertSessionHasErrors(['canonical_url', 'og_image']);
    }

    /** @test */
    public function robots_meta_is_computed_correctly()
    {
        $setting = new SeoSetting([
            'index' => true,
            'follow' => false,
            'robots' => 'max-snippet:-1'
        ]);

        $robotsMeta = $setting->robots_meta;

        $this->assertStringContainsString('index', $robotsMeta);
        $this->assertStringContainsString('nofollow', $robotsMeta);
        $this->assertStringContainsString('max-snippet:-1', $robotsMeta);
    }

    protected function tearDown(): void
    {
        // Clean up sitemap if exists
        if (file_exists(public_path('sitemap.xml'))) {
            unlink(public_path('sitemap.xml'));
        }

        parent::tearDown();
    }
}
