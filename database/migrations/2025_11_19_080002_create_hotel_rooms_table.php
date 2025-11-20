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
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            
            // Room Details
            $table->string('room_type'); // Deluxe, Suite, Standard, etc.
            $table->string('name')->nullable(); // Optional custom name
            $table->text('description')->nullable();
            
            // Capacity
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(1);
            $table->integer('total_rooms')->default(10); // How many of this room type available
            
            // Size
            $table->integer('size_sqm')->nullable(); // Room size in square meters
            
            // Bed Configuration
            $table->string('bed_type')->nullable(); // Single, Double, Queen, King
            $table->integer('bed_count')->default(1);
            
            // Pricing
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('weekend_price', 10, 2)->nullable(); // Optional weekend pricing
            $table->string('currency')->default('BDT');
            
            // Amenities (JSON array specific to this room type)
            $table->json('amenities')->nullable(); // ['tv', 'minibar', 'balcony', 'sea_view']
            
            // Images
            $table->json('images')->nullable();
            
            // Availability
            $table->boolean('is_available')->default(true);
            
            // Discount
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->date('discount_valid_from')->nullable();
            $table->date('discount_valid_until')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('hotel_id');
            $table->index('is_available');
            $table->index(['hotel_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
