<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // This 'code' (e.g., 'AF') matches your seeder
            $table->string('code', 10)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            // Removed iso_code_3, country_code, continent, nationality
        });
    }
    public function down(): void {
        Schema::dropIfExists('countries');
    }
};