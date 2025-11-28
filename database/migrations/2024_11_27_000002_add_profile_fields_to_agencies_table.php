<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            // Business Information
            $table->string('business_type')->nullable()->after('description'); // recruitment, education, immigration, travel
            $table->year('established_year')->nullable()->after('business_type');
            $table->string('license_number')->nullable()->after('established_year');
            $table->date('license_expiry')->nullable()->after('license_number');
            
            // Services & Expertise
            $table->json('services_offered')->nullable()->after('license_expiry');
            $table->json('countries_expertise')->nullable()->after('services_offered');
            $table->json('languages_spoken')->nullable()->after('countries_expertise');
            
            // Social & Contact
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('facebook_url')->nullable()->after('website');
            $table->string('linkedin_url')->nullable()->after('facebook_url');
            $table->string('twitter_url')->nullable()->after('linkedin_url');
            $table->string('instagram_url')->nullable()->after('twitter_url');
            
            // Statistics & Performance
            $table->integer('total_clients')->default(0)->after('commission_rate');
            $table->integer('successful_applications')->default(0)->after('total_clients');
            $table->decimal('success_rate', 5, 2)->default(0)->after('successful_applications');
            $table->decimal('average_rating', 3, 2)->default(0)->after('success_rate');
            $table->integer('total_reviews')->default(0)->after('average_rating');
            
            // Team & Office
            $table->integer('team_size')->default(1)->after('total_reviews');
            $table->text('office_hours')->nullable()->after('team_size');
            $table->json('office_images')->nullable()->after('office_hours');
            
            // SEO & Marketing
            $table->string('slug')->unique()->nullable()->after('name');
            $table->text('meta_description')->nullable()->after('description');
            $table->json('certifications')->nullable()->after('meta_description');
            $table->json('awards')->nullable()->after('certifications');
            
            // Verification Documents
            $table->json('verification_documents')->nullable()->after('verified_at');
            $table->text('verification_notes')->nullable()->after('verification_documents');
            $table->timestamp('profile_completed_at')->nullable()->after('verification_notes');
            
            // Featured & Premium
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->boolean('is_premium')->default(false)->after('is_featured');
            $table->timestamp('featured_until')->nullable()->after('is_premium');
            $table->timestamp('premium_until')->nullable()->after('featured_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn([
                'business_type',
                'established_year',
                'license_number',
                'license_expiry',
                'services_offered',
                'countries_expertise',
                'languages_spoken',
                'whatsapp',
                'facebook_url',
                'linkedin_url',
                'twitter_url',
                'instagram_url',
                'total_clients',
                'successful_applications',
                'success_rate',
                'average_rating',
                'total_reviews',
                'team_size',
                'office_hours',
                'office_images',
                'slug',
                'meta_description',
                'certifications',
                'awards',
                'verification_documents',
                'verification_notes',
                'profile_completed_at',
                'is_featured',
                'is_premium',
                'featured_until',
                'premium_until',
            ]);
        });
    }
};
