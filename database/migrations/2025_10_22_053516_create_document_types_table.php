<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'Passport', 'Bank Statement'
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('has_expiry_date')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('document_types');
    }
};