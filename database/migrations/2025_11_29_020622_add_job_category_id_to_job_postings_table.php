<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\JobPosting;
use App\Models\JobCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the new column
        Schema::table('job_postings', function (Blueprint $table) {
            $table->foreignId('job_category_id')->nullable()->after('category')->constrained('job_categories')->onDelete('set null');
        });

        // Migrate existing data: map old category strings to new job_category_id
        $this->migrateExistingCategories();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropForeign(['job_category_id']);
            $table->dropColumn('job_category_id');
        });
    }

    /**
     * Migrate existing category data to use job_category_id
     */
    private function migrateExistingCategories(): void
    {
        // Get all job postings with their current category strings
        $jobPostings = JobPosting::whereNotNull('category')->get();

        foreach ($jobPostings as $job) {
            // Try to find matching category by slug or name (case-insensitive)
            $categorySlug = \Illuminate\Support\Str::slug($job->category);
            
            $category = JobCategory::where('slug', $categorySlug)
                ->orWhere('name', 'like', $job->category)
                ->first();

            if ($category) {
                $job->job_category_id = $category->id;
                $job->save();
            } else {
                // Log unmapped categories for review
                \Illuminate\Support\Facades\Log::warning("Could not map job posting #{$job->id} category: {$job->category}");
            }
        }
    }
};
