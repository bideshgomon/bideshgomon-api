<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('license_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'Driving License', 'Nursing License'
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('license_types');
    }
};