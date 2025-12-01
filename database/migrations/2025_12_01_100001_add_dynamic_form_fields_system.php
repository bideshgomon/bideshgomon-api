<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Extends existing service architecture with:
     * - service_form_fields: Dynamic form builder with profile mapping
     * - Enhances applications table with better polymorphic support
     */
    public function up(): void
    {
        // Add missing columns to service_modules for plugin configuration
        Schema::table('service_modules', function (Blueprint $table) {
            if (!Schema::hasColumn('service_modules', 'config')) {
                $table->json('config')->nullable()->after('sort_order');
            }
            if (!Schema::hasColumn('service_modules', 'requires_approval')) {
                $table->boolean('requires_approval')->default(true)->after('is_featured');
            }
            if (!Schema::hasColumn('service_modules', 'processing_days')) {
                $table->unsignedInteger('processing_days')->nullable()->after('sort_order');
            }
            if (!Schema::hasColumn('service_modules', 'settings')) {
                $table->json('settings')->nullable()->after('config');
            }
        });

        // Service Form Fields (Dynamic Form Builder with Profile Mapping)
        Schema::create('service_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_module_id')->constrained('service_modules')->onDelete('cascade');
            
            // Field Definition
            $table->string('field_name'); // "passport_number", "travel_date"
            $table->string('field_label'); // "Passport Number", "Date of Travel"
            $table->string('field_type'); // text, textarea, email, number, date, datetime, file, select, radio, checkbox, tel
            $table->text('placeholder')->nullable();
            $table->text('help_text')->nullable(); // Shown below input
            $table->string('default_value')->nullable();
            
            // Validation Rules
            $table->boolean('is_required')->default(false);
            $table->string('validation_rules')->nullable(); // Laravel validation: 'required|max:255|regex:/^[A-Z0-9]+$/'
            $table->integer('min_length')->nullable();
            $table->integer('max_length')->nullable();
            
            // **CRITICAL: Profile Mapping** (The "Smart Bridge")
            $table->string('profile_map_key')->nullable(); // Maps to user profile: 'user_profiles.passport_number'
            $table->string('profile_map_table')->nullable(); // Which table: user_profiles, user_passports, etc.
            $table->string('profile_map_column')->nullable(); // Which column in that table
            
            // For Select/Radio/Checkbox types
            $table->json('options')->nullable(); // ["Male", "Female", "Other"] or [{"value": "m", "label": "Male"}]
            $table->boolean('allow_multiple')->default(false); // For checkbox groups
            
            // File Upload Settings (for file type fields)
            $table->string('accepted_file_types')->nullable(); // ".pdf,.jpg,.png"
            $table->integer('max_file_size')->nullable(); // in KB
            
            // Conditional Logic (Advanced)
            $table->json('conditional_rules')->nullable(); // Show field only if another field has specific value
            // Example: {"show_if": {"field": "travel_type", "value": "multiple"}}
            
            // Layout & Organization
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('group_name')->nullable(); // Group fields: "Personal Info", "Travel Details", "Documents"
            $table->string('section_name')->nullable(); // For multi-step forms
            $table->unsignedInteger('column_width')->default(12); // Bootstrap cols: 6 = half width, 12 = full
            $table->string('css_class')->nullable(); // Custom CSS classes
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_readonly')->default(false); // User can't edit (e.g., pre-filled email)
            $table->timestamps();
            
            $table->index('service_module_id');
            $table->index('sort_order');
            $table->index('profile_map_key');
            $table->index(['group_name', 'sort_order']);
        });

        // Add columns to service_applications if not exist
        if (!Schema::hasColumn('service_applications', 'form_data')) {
            Schema::table('service_applications', function (Blueprint $table) {
                $table->json('form_data')->nullable()->after('application_data');
                $table->json('profile_snapshot')->nullable()->after('form_data'); // Store user profile at time of application
            });
        }

        // Application Documents (File Uploads)
        if (!Schema::hasTable('application_documents')) {
            Schema::create('application_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained('service_applications')->onDelete('cascade');
                $table->string('field_name'); // Which form field this file belongs to
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
                $table->index('field_name');
            });
        }

        // Application Status History (Audit Trail)
        if (!Schema::hasTable('application_status_history')) {
            Schema::create('application_status_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained('service_applications')->onDelete('cascade');
                $table->foreignId('changed_by')->nullable()->constrained('users'); // Who made the change
                $table->string('from_status')->nullable();
                $table->string('to_status');
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable(); // Additional context
                $table->timestamps();
                
                $table->index('application_id');
                $table->index('changed_by');
            });
        }

        // Profile Field Mapping Reference (Admin Tool)
        Schema::create('profile_field_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // user_profiles, user_passports, etc.
            $table->string('column_name'); // passport_number, date_of_birth, etc.
            $table->string('display_name'); // "Passport Number", "Date of Birth"
            $table->string('data_type'); // string, date, integer, decimal, boolean
            $table->string('field_category')->nullable(); // "Personal", "Passport", "Contact", etc.
            $table->text('description')->nullable();
            $table->boolean('is_sensitive')->default(false); // PII data
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['table_name', 'column_name']);
            $table->index('field_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_field_mappings');
        Schema::dropIfExists('application_status_history');
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('service_form_fields');
        
        Schema::table('service_modules', function (Blueprint $table) {
            $table->dropColumn(['config', 'requires_approval', 'processing_days']);
        });
        
        Schema::table('service_applications', function (Blueprint $table) {
            $table->dropColumn(['form_data', 'profile_snapshot']);
        });
    }
};
