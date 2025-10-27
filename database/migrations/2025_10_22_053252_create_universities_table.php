<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            // --- [PATCH START] Remove ->after() ---
            $table->string('city')->nullable(); // Define column without ->after()
            // --- [PATCH END] ---
            $table->string('name'); // Define name after city if desired order
            $table->string('website_url')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->json('intake_months')->nullable();
            $table->integer('ranking')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('universities');
    }
};