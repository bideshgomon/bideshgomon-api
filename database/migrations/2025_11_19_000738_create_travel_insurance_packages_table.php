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
        Schema::create('travel_insurance_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('features')->nullable(); // JSON array of features
            $table->decimal('price_per_day', 10, 2); // Base price per day
            $table->decimal('min_price', 10, 2); // Minimum package price
            $table->decimal('max_coverage', 15, 2); // Maximum coverage amount
            $table->integer('min_days')->default(1);
            $table->integer('max_days')->default(365);
            $table->integer('min_age')->default(1);
            $table->integer('max_age')->default(70);
            $table->json('covered_countries')->nullable(); // Array of country IDs or 'all'
            $table->json('coverage_details')->nullable(); // Medical, baggage, cancellation, etc.
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('badge_text')->nullable(); // "Most Popular", "Best Value", etc.
            $table->string('badge_color')->nullable(); // emerald, blue, amber, etc.
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('is_active');
            $table->index('is_popular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_insurance_packages');
    }
};
