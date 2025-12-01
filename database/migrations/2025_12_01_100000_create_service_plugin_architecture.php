<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration creates the plugin-based service architecture:
     * - service_categories: Group services (Immigration, Jobs, Travel, etc.)
     * - services: Each service as a plugin (Tourist Visa, Work Permit, etc.)
     * - service_fields: Dynamic form fields for each service
     * - applications: Polymorphic storage for all service applications
     */
    public function up(): void
    {
        // Service Categories (Immigration, Jobs, Travel, Education, etc.)
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Immigration Services"
            $table->string('slug')->unique(); // "immigration-services"
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon class or SVG path
            $table->string('color')->default('#3B82F6'); // TailwindCSS color
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable(); // Category-level settings
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
            $table->index('sort_order');
        });

        // Services (The "Plugins")
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories')->onDelete('cascade');
            $table->string('name'); // "Tourist Visa - USA"
            $table->string('slug')->unique(); // "tourist-visa-usa"
            $table->text('description')->nullable();
            $table->text('short_description')->nullable(); // For cards
            $table->string('thumbnail')->nullable(); // Featured image
            $table->decimal('base_price', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('BDT');
            $table->unsignedInteger('processing_days')->nullable(); // Expected turnaround
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_approval')->default(true); // Some services auto-approve
            
            // JSON configuration for flexibility
            $table->json('config')->nullable(); // Can store: requirements, documents_needed, pricing_tiers, etc.
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // Tracking
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('applications_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('slug');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('sort_order');
        });

        // Service Fields (Dynamic Form Builder)
        Schema::create('service_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            
            // Field Definition
            $table->string('field_name'); // "passport_number", "travel_date"
            $table->string('field_label'); // "Passport Number", "Date of Travel"
            $table->string('field_type'); // text, textarea, email, number, date, file, select, radio, checkbox
            $table->text('placeholder')->nullable();
            $table->text('help_text')->nullable(); // Shown below input
            $table->string('default_value')->nullable();
            
            // Validation Rules
            $table->boolean('is_required')->default(false);
            $table->string('validation_rules')->nullable(); // Laravel validation: 'required|max:255|regex:/^[A-Z0-9]+$/'
            
            // **CRITICAL: Profile Mapping** (The "Smart Bridge")
            $table->string('profile_map_key')->nullable(); // Maps to user profile: 'user_profiles.passport_number'
            
            // For Select/Radio/Checkbox types
            $table->json('options')->nullable(); // ["Male", "Female", "Other"]
            
            // Conditional Logic (Advanced)
            $table->json('conditional_rules')->nullable(); // Show field only if another field has specific value
            
            // Layout & Order
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('group_name')->nullable(); // Group fields: "Personal Info", "Travel Details"
            $table->unsignedInteger('column_width')->default(12); // Bootstrap cols: 6 = half width, 12 = full
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('service_id');
            $table->index('sort_order');
            $table->index('profile_map_key');
        });

        // Applications (Polymorphic Storage)
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            
            // Application Status
            $table->enum('status', [
                'draft',           // User started but didn't submit
                'pending',         // Submitted, awaiting review
                'under_review',    // Admin is reviewing
                'additional_info', // Admin requested more documents
                'approved',        // Application approved
                'rejected',        // Application rejected
                'cancelled',       // User cancelled
                'completed'        // Service delivered
            ])->default('draft');
            
            // The actual form data (JSON)
            $table->json('form_data')->nullable(); // All user inputs stored here
            
            // Metadata
            $table->string('application_number')->unique(); // AUTO-GENERATED: "APP-2025-001234"
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
            $table->string('payment_reference')->nullable(); // Transaction ID
            
            // Admin Notes
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('requested_documents')->nullable(); // List of additional docs needed
            
            // Timeline
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
            $table->index('service_id');
            $table->index('status');
            $table->index('application_number');
            $table->index('submitted_at');
        });

        // Application Status History (Audit Trail)
        Schema::create('application_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users'); // Who made the change (admin/user)
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('application_id');
        });

        // Application Documents (File Uploads)
        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('document_type'); // "passport_scan", "photo", "bank_statement"
            $table->string('file_path'); // Storage path
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->text('notes')->nullable();
            $table->boolean('is_verified')->default(false); // Admin can mark as verified
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index('application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('application_status_history');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('service_fields');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
