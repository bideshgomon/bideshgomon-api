<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agency_consultant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('consultant_id');
            $table->timestamps();
            $table->foreign('consultant_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['agency_id', 'consultant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_consultant');
    }
};
