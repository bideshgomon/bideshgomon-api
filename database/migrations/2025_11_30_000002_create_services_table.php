<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade'); // Agency/Consultant
            $table->string('provider_type'); // 'agency', 'consultant', 'admin'
            
            // Basic Info
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('features')->nullable(); // JSON array
            $table->text('requirements')->nullable(); // JSON array
            
            // Pricing
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('agency_posted_price', 10, 2)->nullable();
            $table->decimal('admin_approved_price', 10, 2)->nullable();
            $table->decimal('processing_fee', 10, 2)->default(0);
            $table->string('currency', 3)->default('BDT');
            $table->boolean('is_negotiable')->default(false);
            
            // Service Details
            $table->integer('processing_days')->nullable(); // Estimated completion time
            $table->string('country')->nullable(); // Target country (for visa services)
            $table->string('visa_type')->nullable(); // Tourist, Work, Study, etc.
            $table->json('documents_required')->nullable(); // List of required documents
            
            // Status & Availability
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('max_bookings_per_day')->nullable();
            $table->integer('total_bookings')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            
            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // SEO & Media
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['provider_id', 'provider_type']);
            $table->index('status');
            $table->index('country');
            $table->index('visa_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
