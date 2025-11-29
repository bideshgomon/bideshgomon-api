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
        Schema::table('job_postings', function (Blueprint $table) {
            // Processing Fee Fields - for agency posted jobs where admin adjusts the application fee
            // Example: Agency posts with 300,000 BDT, Admin approves with 350,000 BDT
            // processing_fee = 50,000 BDT (admin markup)
            
            $table->decimal('agency_posted_fee', 10, 2)->nullable()->after('application_fee')
                ->comment('Original fee amount posted by agency');
            
            $table->decimal('admin_approved_fee', 10, 2)->nullable()->after('agency_posted_fee')
                ->comment('Final fee amount approved by admin');
            
            $table->decimal('processing_fee', 10, 2)->default(0)->after('admin_approved_fee')
                ->comment('Difference between agency and admin fee (admin markup)');
            
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('processing_fee')
                ->comment('Admin approval status for job posting');
            
            $table->timestamp('approved_at')->nullable()->after('approval_status')
                ->comment('When admin approved the job posting');
            
            $table->foreignId('approved_by')->nullable()->after('approved_at')
                ->constrained('users')->nullOnDelete()
                ->comment('Admin user who approved the job');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'agency_posted_fee',
                'admin_approved_fee',
                'processing_fee',
                'approval_status',
                'approved_at',
                'approved_by',
            ]);
        });
    }
};
