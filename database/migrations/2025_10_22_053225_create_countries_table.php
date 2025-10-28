<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso_code', 2)->unique(); // ISO 3166-1 alpha-2
            $table->string('iso_code_3', 3)->unique(); // ISO 3166-1 alpha-3
            $table->string('country_code', 10)->nullable(); // e.g., +1, +44
            $table->string('capital')->nullable();
            $table->string('currency')->nullable(); // e.g., USD, EUR
            $table->string('continent')->nullable(); // e.g., Asia, Europe
            $table->string('subregion')->nullable(); // e.g., Western Europe
            $table->string('nationality')->nullable(); // e.g., German, French
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('countries');
    }
};