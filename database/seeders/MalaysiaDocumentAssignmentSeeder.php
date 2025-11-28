<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\MasterDocument;
use App\Models\CountryDocumentRequirement;

class MalaysiaDocumentAssignmentSeeder extends Seeder
{
    /**
     * Seed Malaysia tourist visa document assignments using Document Hub
     */
    public function run(): void
    {
        $malaysia = Country::where('name', 'Malaysia')->first();
        
        if (!$malaysia) {
            $this->command->error('Malaysia not found in countries table');
            return;
        }

        // Get documents
        $documents = [
            'Passport' => ['all', null, 'Valid for minimum 6 months with 2 blank pages', 1],
            'Passport-Size Photographs' => ['all', null, '2 photos - 35mm x 45mm, white background', 2],
            'Bank Statements' => ['all', null, 'Last 6 months with minimum balance RM 3,000 equivalent', 3],
            'Cover Letter' => ['all', null, 'Explaining purpose of visit', 4],
            'Flight Itinerary' => ['all', null, 'Confirmed round-trip booking', 5],
            'Hotel Reservations' => ['all', null, 'Confirmed booking for entire stay', 6],
            
            // Job Holder specific
            'Employment Letter / NOC' => ['tourist', 'Job Holder', 'Must include leave approval and return guarantee', 7],
            'Pay Slips' => ['tourist', 'Job Holder', 'Last 3 months salary slips', 8],
            'Employee ID Card' => ['tourist', 'Job Holder', 'Copy of both sides', 9],
            'TIN Certificate' => ['tourist', 'Job Holder', 'Tax identification certificate', 10],
            
            // Business Person specific
            'Trade License' => ['tourist', 'Business Person', 'Valid business license', 11],
            'Company Registration Certificate' => ['tourist', 'Business Person', 'Certificate of incorporation', 12],
            'Business Bank Statements' => ['tourist', 'Business Person', 'Last 6 months company bank statements', 13],
            'Tax Returns (ITR)' => ['tourist', 'Business Person', 'Last 2 years income tax returns', 14],
            
            // Student specific
            'Student ID Card' => ['tourist', 'Student', 'Valid student identification', 15],
            'School/University NOC' => ['tourist', 'Student', 'Approval for travel during vacation', 16],
            'Parent\'s Bank Statements' => ['tourist', 'Student', 'Last 6 months with sufficient funds', 17],
            'Parent\'s Employment Letter' => ['tourist', 'Student', 'Employment proof of sponsoring parent', 18],
            'Birth Certificate' => ['tourist', 'Student', 'Notarized copy to prove relationship', 19],
        ];

        foreach ($documents as $docName => [$visaType, $profession, $notes, $order]) {
            $document = MasterDocument::where('document_name', $docName)->first();
            
            if ($document) {
                CountryDocumentRequirement::create([
                    'country_id' => $malaysia->id,
                    'visa_type' => $visaType === 'all' ? 'tourist' : $visaType,
                    'profession_category' => $profession === 'all' ? null : $profession,
                    'document_id' => $document->id,
                    'is_mandatory' => true,
                    'specific_notes' => $notes,
                    'sort_order' => $order,
                ]);
                $this->command->info("✓ Assigned: {$docName}");
            } else {
                $this->command->warn("✗ Document not found: {$docName}");
            }
        }

        $this->command->info("\n✅ Malaysia document assignments completed!");
    }
}
