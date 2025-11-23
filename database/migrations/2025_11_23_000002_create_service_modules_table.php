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
        Schema::create('service_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Tourist Visa, Flight Booking, etc.
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->text('full_description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable(); // Service image
            
            // Status & Availability
            $table->boolean('is_active')->default(false); // Admin can enable/disable
            $table->boolean('is_featured')->default(false);
            $table->boolean('coming_soon')->default(true);
            $table->date('launch_date')->nullable();
            
            // Pricing
            $table->decimal('base_price', 10, 2)->default(0);
            $table->string('price_type')->default('fixed'); // fixed, variable, free, quote
            $table->string('currency')->default('BDT');
            
            // Configuration
            $table->json('requirements')->nullable(); // Profile fields required
            $table->json('documents_required')->nullable(); // Documents needed
            $table->json('processing_time')->nullable(); // {min: 5, max: 10, unit: 'days'}
            $table->json('settings')->nullable(); // Service-specific settings
            
            // Routes & Controllers
            $table->string('route_prefix')->nullable(); // /services/tourist-visa
            $table->string('controller')->nullable(); // TouristVisaController
            
            // Permissions & Access
            $table->json('allowed_roles')->nullable(); // ['user', 'agency', 'consultant']
            $table->integer('min_profile_completion')->default(0); // % required
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // Analytics
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->integer('completions_count')->default(0);
            
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_modules');
    }
};
