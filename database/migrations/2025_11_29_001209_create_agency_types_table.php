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
        Schema::create('agency_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Visa & Immigration Specialist, Education Consultant, etc.
            $table->string('slug')->unique(); // visa-immigration-specialist
            $table->string('icon')->nullable(); // Icon identifier
            $table->string('color')->default('blue'); // UI color theme
            $table->text('description')->nullable();
            
            // Capabilities & Features
            $table->json('allowed_service_modules')->nullable(); // Which services can they offer
            $table->json('required_certifications')->nullable(); // Required licenses/certs
            $table->json('capabilities')->nullable(); // Special features they can access
            
            // Commission & Pricing
            $table->decimal('default_commission_rate', 5, 2)->default(15.00);
            $table->decimal('min_commission_rate', 5, 2)->default(10.00);
            $table->decimal('max_commission_rate', 5, 2)->default(25.00);
            $table->boolean('can_set_own_pricing')->default(true);
            
            // Business Rules
            $table->boolean('requires_verification')->default(true);
            $table->boolean('requires_insurance')->default(false);
            $table->boolean('can_manage_team')->default(true);
            $table->boolean('can_create_resources')->default(true); // Universities, schools, etc.
            $table->boolean('needs_country_assignment')->default(false);
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_types');
    }
};
