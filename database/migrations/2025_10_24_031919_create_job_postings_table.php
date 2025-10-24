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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_category_id')->nullable()->constrained()->onDelete('set null'); // Link to job_categories
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null'); // Optional country link

            $table->string('title'); // e.g., "Senior Software Engineer"
            $table->string('company_name');
            $table->string('location_city')->nullable();
            $table->string('employment_type')->nullable(); // e.g., "Full-time", "Contract"

            $table->text('description');
            $table->text('responsibilities')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('skills_required')->nullable(); // Could be JSON later

            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->nullable()->default('USD');
            $table->string('salary_period')->nullable()->default('Yearly'); // e.g., "Hourly", "Monthly", "Yearly"

            $table->string('apply_url')->nullable(); // Link to apply
            $table->date('posting_date')->nullable();
            $table->date('closing_date')->nullable();

            $table->string('status')->default('active'); // e.g., "active", "expired", "filled"
            $table->boolean('is_featured')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};