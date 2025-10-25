<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('document_types')->truncate();
        Schema::enableForeignKeyConstraints();

        $types = [
            // --- Identity & Citizenship ---
            ['name' => 'Passport', 'description' => 'Government-issued passport.', 'has_expiry_date' => true],
            ['name' => 'National ID', 'description' => 'Government-issued national identification card.', 'has_expiry_date' => true],
            ['name' => 'Birth Certificate', 'description' => 'Official birth certificate.'],
            ['name' => 'Driving License', 'description' => 'Government-issued driving license.', 'has_expiry_date' => true],
            ['name' => 'Smart ID Card', 'description' => 'Smart National ID card (Bangladesh).', 'has_expiry_date' => true],
            ['name' => 'Family Certificate', 'description' => 'Certificate showing family details or relation.'],
            ['name' => 'Citizenship Certificate', 'description' => 'Certificate confirming Bangladeshi citizenship.'],

            // --- Education & Qualification ---
            ['name' => 'Academic Transcript', 'description' => 'Official record of courses and grades.'],
            ['name' => 'Degree Certificate', 'description' => 'Certificate confirming degree completion.'],
            ['name' => 'Mark Sheet', 'description' => 'Academic marks obtained per subject.'],
            ['name' => 'School Certificate (SSC)', 'description' => 'Secondary School Certificate.'],
            ['name' => 'College Certificate (HSC)', 'description' => 'Higher Secondary Certificate.'],
            ['name' => 'Diploma Certificate', 'description' => 'Certificate from technical or vocational training institute.'],
            ['name' => 'Training Certificate', 'description' => 'Certificate of technical or skill-based training.'],
            ['name' => 'Language Proficiency Certificate', 'description' => 'e.g., IELTS, TOEFL, PTE, JLPT, EPS TOPIK.', 'has_expiry_date' => true],
            ['name' => 'Trade Test Certificate', 'description' => 'Certificate of trade test result from BMET or authorized center.'],

            // --- Employment & Professional ---
            ['name' => 'CV / Résumé', 'description' => 'Curriculum Vitae or Résumé document.'],
            ['name' => 'Job Experience Letter', 'description' => 'Certificate confirming previous job experience.'],
            ['name' => 'Employment Contract', 'description' => 'Official signed employment contract.'],
            ['name' => 'Offer Letter (Job)', 'description' => 'Job offer letter from an employer.'],
            ['name' => 'Appointment Letter', 'description' => 'Formal appointment confirmation letter.'],
            ['name' => 'Recommendation Letter', 'description' => 'Letter of recommendation from an employer or teacher.'],
            ['name' => 'Work Permit', 'description' => 'Legal authorization to work abroad.', 'has_expiry_date' => true],
            ['name' => 'BMET Smart Card', 'description' => 'Overseas Employment Smart Card from BMET.', 'has_expiry_date' => true],
            ['name' => 'BMET Registration Card', 'description' => 'Proof of registration with Bangladesh BMET.'],
            ['name' => 'BMET Clearance Certificate', 'description' => 'BMET clearance for overseas employment.'],

            // --- Financial Documents ---
            ['name' => 'Bank Statement', 'description' => 'Proof of financial capacity.'],
            ['name' => 'Bank Solvency Certificate', 'description' => 'Official letter showing financial solvency.'],
            ['name' => 'Salary Slip', 'description' => 'Official salary statement from employer.'],
            ['name' => 'Tax Identification Number (TIN)', 'description' => 'Tax identification certificate.'],
            ['name' => 'Income Certificate', 'description' => 'Certificate of income issued by local authority.'],
            ['name' => 'Nominee Declaration', 'description' => 'Document declaring nominee for financial matters.'],

            // --- Health & Medical ---
            ['name' => 'Medical Examination Report', 'description' => 'Report from a required medical check.'],
            ['name' => 'COVID-19 Vaccine Card', 'description' => 'Vaccination certificate for COVID-19.'],
            ['name' => 'Health Fitness Certificate', 'description' => 'Medical fitness certificate from approved doctor.'],
            ['name' => 'Medical Test Certificate', 'description' => 'Pre-departure medical test report.'],

            // --- Legal & Clearance ---
            ['name' => 'Police Clearance Certificate', 'description' => 'Certificate of no criminal record.', 'has_expiry_date' => true],
            ['name' => 'Character Certificate', 'description' => 'Issued by local authority confirming good conduct.'],
            ['name' => 'Affidavit', 'description' => 'Sworn statement or declaration.'],
            ['name' => 'Marriage Certificate', 'description' => 'Official marriage registration certificate.'],
            ['name' => 'Divorce Certificate', 'description' => 'Certificate confirming legal divorce.'],
            ['name' => 'Death Certificate', 'description' => 'Certificate confirming death of a relative (if required).'],

            // --- Travel & Visa ---
            ['name' => 'Visa Application Form', 'description' => 'Official visa application form.'],
            ['name' => 'Visa Grant Notice', 'description' => 'Confirmation of visa approval.'],
            ['name' => 'Travel Insurance', 'description' => 'Proof of travel insurance coverage.', 'has_expiry_date' => true],
            ['name' => 'Air Ticket', 'description' => 'Confirmed flight ticket.'],
            ['name' => 'Boarding Pass', 'description' => 'Proof of flight boarding.'],
            ['name' => 'Migration Clearance Card', 'description' => 'BMET or MOE migration clearance document.'],
            ['name' => 'Overseas Employment Visa', 'description' => 'Employment visa issued by host country.', 'has_expiry_date' => true],
            ['name' => 'Visa Sticker Page', 'description' => 'Passport page showing visa sticker.'],

            // --- Educational Migration & Student Documents ---
            ['name' => 'Offer Letter (University)', 'description' => 'Admission offer letter from a university.'],
            ['name' => 'Acceptance Letter', 'description' => 'Official letter of acceptance from university.'],
            ['name' => 'CAS Letter', 'description' => 'Confirmation of Acceptance for Studies (for UK students).'],
            ['name' => 'Tuition Fee Payment Receipt', 'description' => 'Proof of tuition fee payment.'],
            ['name' => 'Statement of Purpose (SOP)', 'description' => 'Essay explaining academic and career goals.'],
            ['name' => 'Reference Letter (Academic)', 'description' => 'Reference from a teacher or supervisor.'],
            ['name' => 'Scholarship Letter', 'description' => 'Letter confirming scholarship award.'],

            // --- Business & Trade ---
            ['name' => 'Trade License', 'description' => 'Business trade license issued by local authority.'],
            ['name' => 'Company Registration Certificate', 'description' => 'Proof of company or business registration.'],
            ['name' => 'Export License', 'description' => 'License for exporting goods abroad.'],
            ['name' => 'Chamber of Commerce Certificate', 'description' => 'Membership certificate from local chamber.'],

            // --- Digital / Miscellaneous ---
            // --- FIX: Removed 'requires_upload' ---
            ['name' => 'Portfolio Link', 'description' => 'Link to online portfolio (use value field for URL).'],
            // ------------------------------------
            ['name' => 'Profile Photo', 'description' => 'Recent passport-size photograph.'],
            ['name' => 'NOC (No Objection Certificate)', 'description' => 'Issued by authority or employer.'],
            ['name' => 'Experience Certificate', 'description' => 'Proof of professional or vocational experience.'],
            ['name' => 'Skill Certification', 'description' => 'Proof of skill test completion (BMET or training center).'],
            ['name' => 'Migration Form', 'description' => 'Official migration request form.'],
            ['name' => 'Other', 'description' => 'Any other relevant document.'],
        ];

        foreach ($types as $typeData) {
            // Find by 'name', and create/update with all data.
            DocumentType::updateOrCreate(['name' => $typeData['name']], $typeData);
        }
    }
}