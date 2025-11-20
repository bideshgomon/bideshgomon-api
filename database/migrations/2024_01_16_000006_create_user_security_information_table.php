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
        Schema::create('user_security_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // Criminal Record
            $table->boolean('has_criminal_record')->default(false);
            $table->text('criminal_record_details')->nullable();
            $table->string('country_of_conviction', 2)->nullable();
            $table->date('conviction_date')->nullable();
            $table->string('offense_type')->nullable();
            $table->string('sentence_duration')->nullable(); // e.g., "6 months", "2 years"
            $table->boolean('sentence_served')->default(false);
            $table->date('sentence_completion_date')->nullable();
            
            // Deportation History
            $table->boolean('has_been_deported')->default(false);
            $table->string('deportation_country', 2)->nullable();
            $table->date('deportation_date')->nullable();
            $table->text('deportation_reason')->nullable();
            $table->boolean('deportation_ban_active')->default(false);
            $table->date('deportation_ban_expiry')->nullable();
            
            // Visa Violations
            $table->boolean('has_overstayed_visa')->default(false);
            $table->string('overstay_country', 2)->nullable();
            $table->integer('overstay_duration_days')->nullable();
            $table->date('overstay_date')->nullable();
            $table->text('overstay_explanation')->nullable();
            
            $table->boolean('has_worked_illegally')->default(false);
            $table->string('illegal_work_country', 2)->nullable();
            $table->text('illegal_work_details')->nullable();
            
            $table->boolean('has_violated_visa_conditions')->default(false);
            $table->text('visa_violation_details')->nullable();
            
            // Immigration Bans
            $table->boolean('has_immigration_ban')->default(false);
            $table->string('ban_country', 2)->nullable();
            $table->date('ban_start_date')->nullable();
            $table->date('ban_end_date')->nullable();
            $table->text('ban_reason')->nullable();
            
            // Military Service
            $table->boolean('has_military_service')->default(false);
            $table->string('military_country', 2)->nullable();
            $table->string('military_branch')->nullable(); // Army, Navy, Air Force, etc.
            $table->string('military_rank')->nullable();
            $table->date('military_service_start')->nullable();
            $table->date('military_service_end')->nullable();
            $table->enum('military_discharge_type', [
                'honorable',
                'general',
                'dishonorable',
                'medical',
                'other',
                'not_applicable'
            ])->default('not_applicable');
            
            // Security Concerns
            $table->boolean('has_terrorist_affiliation')->default(false);
            $table->text('terrorist_affiliation_details')->nullable();
            
            $table->boolean('member_of_banned_organization')->default(false);
            $table->text('banned_organization_details')->nullable();
            
            $table->boolean('has_security_clearance')->default(false);
            $table->string('security_clearance_level')->nullable();
            $table->string('security_clearance_country', 2)->nullable();
            
            // Police Clearance
            $table->boolean('has_police_clearance')->default(false);
            $table->string('police_clearance_country', 2)->nullable();
            $table->date('police_clearance_issue_date')->nullable();
            $table->date('police_clearance_expiry_date')->nullable();
            $table->string('police_clearance_reference')->nullable();
            
            // Background Check
            $table->boolean('background_check_completed')->default(false);
            $table->string('background_check_agency')->nullable();
            $table->date('background_check_date')->nullable();
            $table->enum('background_check_result', [
                'clear',
                'issues_found',
                'pending',
                'not_applicable'
            ])->default('not_applicable');
            
            // Document Storage
            $table->string('police_clearance_certificate_path')->nullable();
            $table->string('good_conduct_certificate_path')->nullable();
            $table->string('court_records_path')->nullable();
            $table->string('military_discharge_papers_path')->nullable();
            $table->json('additional_security_documents')->nullable();
            
            // Declaration
            $table->boolean('security_questions_answered')->default(false);
            $table->timestamp('security_declaration_date')->nullable();
            $table->text('additional_security_notes')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('has_criminal_record');
            $table->index('has_been_deported');
            $table->index('has_immigration_ban');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_security_information');
    }
};
