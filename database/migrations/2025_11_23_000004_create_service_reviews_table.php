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
        Schema::create('service_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_module_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_application_id')->nullable()->constrained()->nullOnDelete();
            
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->text('review')->nullable();
            $table->json('ratings_breakdown')->nullable(); // {speed: 5, support: 4, value: 5}
            
            $table->boolean('is_verified')->default(false); // Verified purchase
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(false);
            
            $table->text('admin_response')->nullable();
            $table->timestamp('admin_responded_at')->nullable();
            
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            
            $table->timestamps();
            
            // One review per user per service
            $table->unique(['user_id', 'service_module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reviews');
    }
};
