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
        Schema::table('user_family_members', function (Blueprint $table) {
            // Add missing fields that were in controller but not in model
            $table->string('education_level', 100)->nullable()->after('annual_income');
            $table->string('marital_status', 50)->nullable()->after('education_level');
            $table->boolean('is_dependent')->default(false)->after('marital_status');
            $table->boolean('lives_with_user')->default(false)->after('is_dependent');
            $table->boolean('will_accompany')->default(false)->after('lives_with_user');
            $table->boolean('will_accompany_travel')->default(false)->after('will_accompany');
            $table->boolean('emergency_contact')->default(false)->after('email');
            $table->string('relationship_proof_path')->nullable()->after('id_document_path');
            $table->boolean('relationship_proof_uploaded')->default(false)->after('relationship_proof_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_family_members', function (Blueprint $table) {
            $table->dropColumn([
                'education_level',
                'marital_status',
                'is_dependent',
                'lives_with_user',
                'will_accompany',
                'will_accompany_travel',
                'emergency_contact',
                'relationship_proof_path',
                'relationship_proof_uploaded',
            ]);
        });
    }
};
