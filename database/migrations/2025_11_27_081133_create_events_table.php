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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('description_bn')->nullable();
            $table->string('location');
            $table->string('location_bn')->nullable();
            $table->string('address')->nullable();
            $table->dateTime('event_date');
            $table->dateTime('event_end_date')->nullable();
            $table->string('image')->nullable();
            $table->string('event_type')->default('seminar'); // seminar, workshop, fair, consultation
            $table->integer('max_participants')->nullable();
            $table->integer('registered_count')->default(0);
            $table->boolean('is_online')->default(false);
            $table->string('meeting_link')->nullable();
            $table->json('contact_info')->nullable();
            $table->string('status')->default('upcoming'); // upcoming, ongoing, completed, cancelled
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('event_date');
            $table->index('status');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
