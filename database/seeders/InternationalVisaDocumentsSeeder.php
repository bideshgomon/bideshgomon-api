<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentCategory;
use App\Models\MasterDocument;

class InternationalVisaDocumentsSeeder extends Seeder
{
    /**
     * Seed international standard visa documents based on:
     * - Schengen visa requirements
     * - US Department of State standards
     * - UK Home Office requirements
     * - Australian Department of Home Affairs
     * - Canadian Immigration standards
     */
    public function run(): void
    {
        // 1. Identity Documents Category
        $identityCategory = DocumentCategory::create([
            'name' => 'Identity Documents',
            'description' => 'Personal identification documents',
            'sort_order' => 1,
        ]);

        $identityDocs = [
            [
                'document_name' => 'Passport',
                'description' => 'Valid passport with minimum 6 months validity',
                'specifications' => 'Must have at least 2 blank pages. Machine-readable passport required. Validity must extend 6 months beyond intended stay.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 3650, // 10 years
                'international_standard' => 'ICAO Doc 9303',
                'sort_order' => 1,
            ],
            [
                'document_name' => 'National ID Card',
                'description' => 'Government-issued national identity card',
                'specifications' => 'Copy of both sides. Must be valid and not expired.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 3650,
                'international_standard' => 'ISO/IEC 7810',
                'sort_order' => 2,
            ],
            [
                'document_name' => 'Birth Certificate',
                'description' => 'Official birth certificate',
                'specifications' => 'Original or certified copy. Must show full name, date of birth, place of birth, and parents\' names.',
                'translation_required' => true,
                'notarization_required' => true,
                'typical_validity_days' => null,
                'international_standard' => 'UN Legal Identity',
                'sort_order' => 3,
            ],
            [
                'document_name' => 'Marriage Certificate',
                'description' => 'Official marriage certificate (if applicable)',
                'specifications' => 'Original or certified copy. Required if traveling with spouse or name changed after marriage.',
                'translation_required' => true,
                'notarization_required' => true,
                'typical_validity_days' => null,
                'international_standard' => 'UN Legal Identity',
                'sort_order' => 4,
            ],
            [
                'document_name' => 'Passport-Size Photographs',
                'description' => 'Recent passport-size color photographs',
                'specifications' => 'Usually 2-4 photos required. White or light background. 35mm x 45mm or 2x2 inches. Taken within last 6 months. No glasses, neutral expression.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 180, // 6 months
                'international_standard' => 'ICAO Photo Standards',
                'sort_order' => 5,
            ],
        ];

        foreach ($identityDocs as $doc) {
            MasterDocument::create(array_merge($doc, ['category_id' => $identityCategory->id]));
        }

        // 2. Financial Documents Category
        $financialCategory = DocumentCategory::create([
            'name' => 'Financial Documents',
            'description' => 'Proof of financial stability and ability to support travel',
            'sort_order' => 2,
        ]);

        $financialDocs = [
            [
                'document_name' => 'Bank Statements',
                'description' => 'Bank statements for last 3-6 months',
                'specifications' => 'Must be stamped and signed by bank. Should show regular transactions and sufficient balance. Original bank statements preferred.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 30,
                'international_standard' => 'IBAN/SWIFT',
                'sort_order' => 1,
            ],
            [
                'document_name' => 'Bank Solvency Certificate',
                'description' => 'Certificate from bank showing account balance',
                'specifications' => 'On bank letterhead with seal. Must state account holder name, account number, current balance, and average balance.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 30,
                'international_standard' => 'Banking Standard',
                'sort_order' => 2,
            ],
            [
                'document_name' => 'Tax Returns (ITR)',
                'description' => 'Income tax returns for last 2-3 years',
                'specifications' => 'Government-stamped acknowledgment. Shows annual income and tax paid. Required for business owners and self-employed.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 365,
                'international_standard' => 'Tax Authority Standard',
                'sort_order' => 3,
            ],
            [
                'document_name' => 'TIN Certificate',
                'description' => 'Tax Identification Number certificate',
                'specifications' => 'Government-issued TIN certificate showing taxpayer registration.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => null,
                'international_standard' => 'Tax Authority Standard',
                'sort_order' => 4,
            ],
            [
                'document_name' => 'Sponsorship Letter',
                'description' => 'Letter from sponsor (if sponsored)',
                'specifications' => 'Must state relationship, purpose of sponsorship, financial commitment. Include sponsor\'s ID, bank statements, and employment proof.',
                'translation_required' => true,
                'notarization_required' => true,
                'typical_validity_days' => 90,
                'international_standard' => 'Notarized Affidavit',
                'sort_order' => 5,
            ],
        ];

        foreach ($financialDocs as $doc) {
            MasterDocument::create(array_merge($doc, ['category_id' => $financialCategory->id]));
        }

        // 3. Employment Documents Category
        $employmentCategory = DocumentCategory::create([
            'name' => 'Employment Documents',
            'description' => 'Documents related to employment status',
            'sort_order' => 3,
        ]);

        $employmentDocs = [
            [
                'document_name' => 'Employment Letter / NOC',
                'description' => 'Letter from employer on company letterhead',
                'specifications' => 'Must include: employee name, designation, salary, joining date, leave approval, company contact. Signed by HR/Manager with company seal.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 90,
                'international_standard' => 'Corporate Standard',
                'sort_order' => 1,
            ],
            [
                'document_name' => 'Pay Slips',
                'description' => 'Salary slips for last 3-6 months',
                'specifications' => 'Company-issued payslips showing monthly salary, deductions, and net pay.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 180,
                'international_standard' => 'Corporate Standard',
                'sort_order' => 2,
            ],
            [
                'document_name' => 'Employee ID Card',
                'description' => 'Company-issued employee identification card',
                'specifications' => 'Copy of both sides. Must be valid and show employee name, photo, designation.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 365,
                'international_standard' => 'Corporate Standard',
                'sort_order' => 3,
            ],
            [
                'document_name' => 'Visiting Card',
                'description' => 'Professional business card',
                'specifications' => 'Shows name, designation, company name, contact information.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 365,
                'international_standard' => 'Corporate Standard',
                'sort_order' => 4,
            ],
        ];

        foreach ($employmentDocs as $doc) {
            MasterDocument::create(array_merge($doc, ['category_id' => $employmentCategory->id]));
        }

        // 4. Business Documents Category
        $businessCategory = DocumentCategory::create([
            'name' => 'Business Documents',
            'description' => 'Documents for business owners and self-employed',
            'sort_order' => 4,
        ]);

        $businessDocs = [
            [
                'document_name' => 'Trade License',
                'description' => 'Valid business/trade license',
                'specifications' => 'Government-issued trade license showing business registration, owner name, business type, validity period.',
                'translation_required' => true,
                'notarization_required' => true,
                'typical_validity_days' => 365,
                'international_standard' => 'Business Registration',
                'sort_order' => 1,
            ],
            [
                'document_name' => 'Company Registration Certificate',
                'description' => 'Certificate of incorporation',
                'specifications' => 'Shows company name, registration number, date of incorporation, registered office address.',
                'translation_required' => true,
                'notarization_required' => true,
                'typical_validity_days' => null,
                'international_standard' => 'Corporate Law',
                'sort_order' => 2,
            ],
            [
                'document_name' => 'Memorandum of Association',
                'description' => 'Company MOA and Articles of Association',
                'specifications' => 'Legal document defining company objectives, share structure, rights and responsibilities.',
                'translation_required' => true,
                'notarization_required' => true,
                'typical_validity_days' => null,
                'international_standard' => 'Corporate Law',
                'sort_order' => 3,
            ],
            [
                'document_name' => 'Company Letterhead',
                'description' => 'Official company letterhead sample',
                'specifications' => 'Shows company name, logo, address, contact information.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 365,
                'international_standard' => 'Corporate Standard',
                'sort_order' => 4,
            ],
            [
                'document_name' => 'Business Bank Statements',
                'description' => 'Company bank statements for last 6 months',
                'specifications' => 'Must show business transactions, turnover, and healthy cash flow.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 30,
                'international_standard' => 'Banking Standard',
                'sort_order' => 5,
            ],
        ];

        foreach ($businessDocs as $doc) {
            MasterDocument::create(array_merge($doc, ['category_id' => $businessCategory->id]));
        }

        // 5. Educational Documents Category
        $educationCategory = DocumentCategory::create([
            'name' => 'Educational Documents',
            'description' => 'Documents for students',
            'sort_order' => 5,
        ]);

        $educationDocs = [
            [
                'document_name' => 'Student ID Card',
                'description' => 'Valid student identification card',
                'specifications' => 'Copy of both sides. Must be current and show student name, photo, institution name.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 365,
                'international_standard' => 'Academic Standard',
                'sort_order' => 1,
            ],
            [
                'document_name' => 'School/University NOC',
                'description' => 'No Objection Certificate from educational institution',
                'specifications' => 'On institution letterhead. States student name, program, approval for travel during vacation.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 90,
                'international_standard' => 'Academic Standard',
                'sort_order' => 2,
            ],
            [
                'document_name' => 'Parent\'s Bank Statements',
                'description' => 'Bank statements of parents/guardian',
                'specifications' => 'Last 6 months bank statements of sponsor parent showing sufficient funds.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 30,
                'international_standard' => 'Banking Standard',
                'sort_order' => 3,
            ],
            [
                'document_name' => 'Parent\'s Employment Letter',
                'description' => 'Employment proof of sponsoring parent',
                'specifications' => 'Letter from parent\'s employer showing position, salary, employment duration.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 90,
                'international_standard' => 'Corporate Standard',
                'sort_order' => 4,
            ],
            [
                'document_name' => 'Parent\'s Marriage Certificate',
                'description' => 'Marriage certificate of parents',
                'specifications' => 'Proves relationship between sponsoring parents and student.',
                'translation_required' => true,
                'notarization_required' => true,
                'typical_validity_days' => null,
                'international_standard' => 'UN Legal Identity',
                'sort_order' => 5,
            ],
        ];

        foreach ($educationDocs as $doc) {
            MasterDocument::create(array_merge($doc, ['category_id' => $educationCategory->id]));
        }

        // 6. Travel Documents Category
        $travelCategory = DocumentCategory::create([
            'name' => 'Travel Documents',
            'description' => 'Travel itinerary and bookings',
            'sort_order' => 6,
        ]);

        $travelDocs = [
            [
                'document_name' => 'Flight Itinerary',
                'description' => 'Confirmed flight booking or itinerary',
                'specifications' => 'Shows departure and return dates, flight numbers, booking reference. Note: Some embassies accept dummy bookings.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 30,
                'international_standard' => 'IATA',
                'sort_order' => 1,
            ],
            [
                'document_name' => 'Hotel Reservations',
                'description' => 'Confirmed hotel bookings',
                'specifications' => 'Shows hotel name, address, check-in/out dates, booking confirmation. Must cover entire stay.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 30,
                'international_standard' => 'Hospitality Standard',
                'sort_order' => 2,
            ],
            [
                'document_name' => 'Travel Insurance',
                'description' => 'Travel medical insurance',
                'specifications' => 'Coverage minimum: €30,000 for Schengen, $50,000 for US. Must cover medical emergency, repatriation, emergency evacuation.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 180,
                'international_standard' => 'Insurance Standard',
                'sort_order' => 3,
            ],
            [
                'document_name' => 'Tour Itinerary',
                'description' => 'Detailed day-by-day travel plan',
                'specifications' => 'Shows places to visit, activities planned, transportation arrangements.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 30,
                'international_standard' => 'Tourism Standard',
                'sort_order' => 4,
            ],
        ];

        foreach ($travelDocs as $doc) {
            MasterDocument::create(array_merge($doc, ['category_id' => $travelCategory->id]));
        }

        // 7. Supporting Documents Category
        $supportingCategory = DocumentCategory::create([
            'name' => 'Supporting Documents',
            'description' => 'Additional supporting documentation',
            'sort_order' => 7,
        ]);

        $supportingDocs = [
            [
                'document_name' => 'Cover Letter',
                'description' => 'Personal cover letter to visa officer',
                'specifications' => 'Explains purpose of visit, travel plans, ties to home country, assurance of return. Signed and dated.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 30,
                'international_standard' => 'Visa Application Standard',
                'sort_order' => 1,
            ],
            [
                'document_name' => 'Invitation Letter',
                'description' => 'Invitation from host in destination country',
                'specifications' => 'Letter from inviter with their ID copy, address proof, explaining relationship and purpose of visit.',
                'translation_required' => false,
                'notarization_required' => true,
                'typical_validity_days' => 90,
                'international_standard' => 'Notarized Affidavit',
                'sort_order' => 2,
            ],
            [
                'document_name' => 'Previous Visas',
                'description' => 'Copies of previous visas and travel history',
                'specifications' => 'Photocopies of previous visas, entry/exit stamps. Demonstrates travel history and compliance.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => null,
                'international_standard' => 'Visa History',
                'sort_order' => 3,
            ],
            [
                'document_name' => 'Property Documents',
                'description' => 'Property ownership documents (if applicable)',
                'specifications' => 'Land deed, house ownership papers. Proves ties to home country.',
                'translation_required' => true,
                'notarization_required' => true,
                'typical_validity_days' => null,
                'international_standard' => 'Property Law',
                'sort_order' => 4,
            ],
            [
                'document_name' => 'Police Clearance Certificate',
                'description' => 'Criminal background check certificate',
                'specifications' => 'Issued by police authority. Must be recent (usually within 6 months). Required for long-term visas.',
                'translation_required' => true,
                'notarization_required' => true,
                'typical_validity_days' => 180,
                'international_standard' => 'Law Enforcement Standard',
                'sort_order' => 5,
            ],
        ];

        foreach ($supportingDocs as $doc) {
            MasterDocument::create(array_merge($doc, ['category_id' => $supportingCategory->id]));
        }

        // 8. Medical Documents Category
        $medicalCategory = DocumentCategory::create([
            'name' => 'Medical Documents',
            'description' => 'Health and medical certificates',
            'sort_order' => 8,
        ]);

        $medicalDocs = [
            [
                'document_name' => 'Medical Certificate',
                'description' => 'Health fitness certificate from doctor',
                'specifications' => 'Certificate from registered physician stating applicant is fit to travel. Required for some countries.',
                'translation_required' => true,
                'notarization_required' => false,
                'typical_validity_days' => 90,
                'international_standard' => 'Medical Standard',
                'sort_order' => 1,
            ],
            [
                'document_name' => 'Vaccination Certificate',
                'description' => 'Proof of required vaccinations',
                'specifications' => 'WHO Yellow Card or country-specific vaccination proof (Yellow Fever, COVID-19, etc.).',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 3650,
                'international_standard' => 'WHO IHR',
                'sort_order' => 2,
            ],
            [
                'document_name' => 'TB Test Certificate',
                'description' => 'Tuberculosis screening certificate',
                'specifications' => 'Required for UK and some other countries. Must be from approved clinic.',
                'translation_required' => false,
                'notarization_required' => false,
                'typical_validity_days' => 180,
                'international_standard' => 'WHO TB Guidelines',
                'sort_order' => 3,
            ],
        ];

        foreach ($medicalDocs as $doc) {
            MasterDocument::create(array_merge($doc, ['category_id' => $medicalCategory->id]));
        }
    }
}
