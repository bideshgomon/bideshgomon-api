<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('degrees', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'B.Sc. in Computer Science', 'MBA'
            $table->foreignId('degree_level_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('field_of_study_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('degrees');
    }
};