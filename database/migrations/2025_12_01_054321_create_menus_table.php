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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('location', 50); // 'header_main', 'footer_column_1', 'footer_column_2', etc.
            $table->string('label', 100);
            $table->string('url', 500)->nullable();
            $table->string('route_name', 100)->nullable(); // Laravel route name
            $table->string('icon', 50)->nullable(); // Heroicon name
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_external')->default(false); // External link?
            $table->string('target', 20)->default('_self'); // '_blank' for new tab
            $table->json('permissions')->nullable(); // ['admin', 'user'] - who can see this
            $table->timestamps();

            $table->index(['location', 'order']);
            $table->index('parent_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
