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
        Schema::table('flight_bookings', function (Blueprint $table) {
            $table->enum('trip_type', ['one_way', 'round_trip', 'multi_city'])->default('one_way')->after('flight_route_id');
            $table->foreignId('return_flight_route_id')->nullable()->after('trip_type')->constrained('flight_routes')->onDelete('restrict');
            $table->date('return_travel_date')->nullable()->after('return_flight_route_id');
            $table->json('multi_city_segments')->nullable()->after('return_travel_date')->comment('Array of flight segments for multi-city trips');
            $table->decimal('return_base_fare', 10, 2)->nullable()->after('base_fare');
            $table->decimal('multi_city_total', 10, 2)->nullable()->after('return_base_fare');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flight_bookings', function (Blueprint $table) {
            $table->dropForeign(['return_flight_route_id']);
            $table->dropColumn([
                'trip_type',
                'return_flight_route_id',
                'return_travel_date',
                'multi_city_segments',
                'return_base_fare',
                'multi_city_total',
            ]);
        });
    }
};
