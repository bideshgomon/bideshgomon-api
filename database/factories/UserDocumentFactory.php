<?php

namespace Database\Factories;

use App\Models\UserDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserDocumentFactory extends Factory
{
    protected $model = UserDocument::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'document_type' => 'passport_scan',
            'status' => UserDocument::STATUS_PENDING,
            'original_filename' => $this->faker->lexify('document_????.pdf'),
            'storage_path' => 'documents/test/'.$this->faker->uuid.'.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => $this->faker->numberBetween(10_000, 200_000),
            'meta' => [],
            'is_primary' => false,
            'confidence_score' => 50,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn() => [
            'status' => UserDocument::STATUS_APPROVED,
            'reviewed_by' => null,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn() => [
            'status' => UserDocument::STATUS_REJECTED,
            'reviewed_by' => null,
            'reviewed_at' => now(),
            'rejection_reason' => 'Invalid scan',
        ]);
    }
}
