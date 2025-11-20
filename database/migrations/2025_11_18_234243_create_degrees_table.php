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
        Schema::create('degrees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // Bachelor of Science, Master of Arts
            $table->string('name_bn', 100)->nullable(); // Bengali name
            $table->string('short_name', 20); // B.Sc., M.A., Ph.D.
            $table->enum('level', ['secondary', 'higher_secondary', 'diploma', 'bachelor', 'master', 'doctorate']); // Education level
            $table->integer('typical_duration_years')->default(4); // Typical years to complete
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('level');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('degrees');
    }
};
