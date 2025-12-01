<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Service;
use App\Models\ServiceModule;
use App\Models\Country;
use App\Models\BlogPost;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        // Add static pages
        $this->addStaticPages($sitemap);
        
        // Add services
        $this->addServices($sitemap);
        
        // Add service modules
        $this->addServiceModules($sitemap);
        
        // Add countries
        $this->addCountries($sitemap);
        
        // Add blog posts (if exists)
        $this->addBlogPosts($sitemap);

        return $sitemap->toResponse(request());
    }

    protected function addStaticPages($sitemap)
    {
        // Homepage
        $sitemap->add(
            Url::create(route('homepage'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        // About
        $sitemap->add(
            Url::create(url('/about'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8)
        );

        // Services
        $sitemap->add(
            Url::create(route('services.index'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.9)
        );

        // Contact
        $sitemap->add(
            Url::create(url('/contact'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.7)
        );

        // Legal pages
        $sitemap->add(
            Url::create(route('legal.privacy'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                ->setPriority(0.3)
        );

        $sitemap->add(
            Url::create(route('legal.terms'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                ->setPriority(0.3)
        );

        $sitemap->add(
            Url::create(route('legal.refund'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                ->setPriority(0.3)
        );
    }

    protected function addServices($sitemap)
    {
        Service::where('is_active', true)
            ->get()
            ->each(function ($service) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('services.show', $service->slug))
                        ->setLastModificationDate($service->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            });
    }

    protected function addServiceModules($sitemap)
    {
        ServiceModule::where('is_active', true)
            ->get()
            ->each(function ($module) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('services.module', $module->slug))
                        ->setLastModificationDate($module->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7)
                );
            });
    }

    protected function addCountries($sitemap)
    {
        Country::where('is_active', true)
            ->get()
            ->each(function ($country) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('countries.show', $country->code))
                        ->setLastModificationDate($country->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.6)
                );
            });
    }

    protected function addBlogPosts($sitemap)
    {
        // Check if BlogPost model exists
        if (!class_exists(BlogPost::class)) {
            return;
        }

        BlogPost::where('is_published', true)
            ->get()
            ->each(function ($post) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('blog.show', $post->slug))
                        ->setLastModificationDate($post->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.6)
                );
            });
    }
}
