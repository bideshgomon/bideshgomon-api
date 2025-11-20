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
        Schema::create('user_travel_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_passport_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_visa_history_id')->nullable()->constrained('user_visa_history')->onDelete('set null');
            
            // Destination
            $table->string('country_visited', 2); // ISO 3166-1 alpha-2
            $table->string('city_visited')->nullable();
            $table->string('region_visited')->nullable(); // State/Province
            
            // Travel Dates
            $table->date('entry_date');
            $table->date('exit_date')->nullable();
            $table->integer('duration_days')->nullable(); // Calculated or manual
            
            // Purpose & Details
            $table->enum('purpose', [
                'tourism',
                'business',
                'study',
                'work',
                'family_visit',
                'medical',
                'conference',
                'transit',
                'other'
            ])->default('tourism');
            $table->text('purpose_details')->nullable();
            
            // Accommodation
            $table->enum('accommodation_type', [
                'hotel',
                'hostel',
                'friend_family',
                'rental',
                'own_property',
                'other'
            ])->nullable();
            $table->text('accommodation_address')->nullable();
            $table->string('accommodation_name')->nullable(); // Hotel name, host name, etc.
            
            // Travel Details
            $table->json('travel_companions')->nullable(); // ["spouse", "children", "friends"]
            $table->string('entry_port')->nullable(); // Airport/border crossing
            $table->string('exit_port')->nullable();
            $table->string('flight_number')->nullable();
            
            // Document References
            $table->string('flight_ticket_path')->nullable();
            $table->string('hotel_booking_path')->nullable();
            $table->string('travel_insurance_path')->nullable();
            $table->string('photos_path')->nullable(); // Proof of visit
            
            // Compliance
            $table->boolean('compliance_issues')->default(false);
            $table->text('compliance_notes')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('country_visited');
            $table->index('entry_date');
            $table->index('purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_travel_history');
    }
};
