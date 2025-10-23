<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('technical_education_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'Vocational Training', 'Bootcamp'
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('technical_education_types');
    }
};