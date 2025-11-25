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
        Schema::create('agency_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            $table->foreignId('service_module_id')->constrained('service_modules')->onDelete('cascade');
            $table->enum('resource_type', ['university', 'school', 'language_center', 'training_institute', 'job_portal', 'other']);
            $table->string('resource_name');
            $table->string('resource_code')->nullable()->comment('University code, school ID, etc.');
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('city')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_primary_owner')->default(true)->comment('First agency to add owns it');
            $table->boolean('requires_admin_approval')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->timestamp('admin_approved_at')->nullable();
            $table->foreignId('admin_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->decimal('special_commission_rate', 5, 2)->nullable()->comment('Override default commission');
            $table->json('metadata')->nullable()->comment('Additional resource data');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['agency_id', 'service_module_id']);
            $table->index(['resource_type', 'resource_name']);
            $table->index(['is_approved', 'is_active']);
            
            // Unique constraint: One resource can only have one primary owner per service
            $table->unique(['service_module_id', 'resource_type', 'resource_name', 'is_primary_owner'], 'unique_primary_owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_resources');
    }
};
