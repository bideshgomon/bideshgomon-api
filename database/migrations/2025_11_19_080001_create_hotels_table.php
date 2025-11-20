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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Location
            $table->string('city');
            $table->string('area')->nullable();
            $table->text('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('country')->default('Bangladesh');
            
            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            
            // Rating & Reviews
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('total_reviews')->default(0);
            
            // Hotel Details
            $table->integer('star_rating')->default(3); // 1-5 stars
            $table->string('property_type')->default('hotel'); // hotel, resort, apartment, guesthouse
            
            // Amenities (JSON array)
            $table->json('amenities')->nullable(); // ['wifi', 'pool', 'gym', 'restaurant', 'parking']
            
            // Images
            $table->string('featured_image')->nullable();
            $table->json('images')->nullable(); // Array of image URLs
            
            // Pricing
            $table->decimal('price_from', 10, 2)->nullable(); // Lowest room price
            $table->string('currency')->default('BDT');
            
            // Policies
            $table->time('check_in_time')->default('14:00');
            $table->time('check_out_time')->default('12:00');
            $table->text('cancellation_policy')->nullable();
            $table->text('house_rules')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('city');
            $table->index('star_rating');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index(['city', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
