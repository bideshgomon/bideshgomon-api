<?php

namespace Database\Seeders;

use App\Models\VisaRequirement;
use App\Models\VisaRequirementDocument;
use App\Models\ProfessionVisaRequirement;
use App\Models\ServiceModule;
use Illuminate\Database\Seeder;

class VisaRequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds comprehensive tourist visa requirements for major destination countries.
     * Tourist visa is prioritized with profession-specific requirements and fees.
     */
    public function run(): void
    {
        $this->command->info('🌍 Seeding Visa Requirements...');

        // Get visa service module
        $visaService = ServiceModule::where('slug', 'visa-application')->first();

        // ===================================
        // 1. USA TOURIST VISA (B-2)
        // ===================================
        $this->seedUSATouristVisa($visaService);

        // ===================================
        // 2. UK TOURIST VISA (Standard Visitor)
        // ===================================
        $this->seedUKTouristVisa($visaService);

        // ===================================
        // 3. CANADA TOURIST VISA (Visitor Visa)
        // ===================================
        $this->seedCanadaTouristVisa($visaService);

        // ===================================
        // 4. SCHENGEN TOURIST VISA (Type C)
        // ===================================
        $this->seedSchengenTouristVisa($visaService);

        // ===================================
        // 5. AUSTRALIA TOURIST VISA (Subclass 600)
        // ===================================
        $this->seedAustraliaTouristVisa($visaService);

        // ===================================
        // 6. UAE TOURIST VISA
        // ===================================
        $this->seedUAETouristVisa($visaService);

        // ===================================
        // 7. MALAYSIA TOURIST VISA
        // ===================================
        $this->seedMalaysiaTouristVisa($visaService);

        // ===================================
        // 8. THAILAND TOURIST VISA
        // ===================================
        $this->seedThailandTouristVisa($visaService);

        // ===================================
        // 9. SINGAPORE TOURIST VISA
        // ===================================
        $this->seedSingaporeTouristVisa($visaService);

        // ===================================
        // 10. INDIA TOURIST VISA (e-Tourist Visa)
        // ===================================
        $this->seedIndiaTouristVisa($visaService);

        $this->command->info('✅ Visa requirements seeded successfully!');
    }

    /**
     * Seed USA Tourist Visa requirements
     */
    private function seedUSATouristVisa($visaService)
    {
        $usa = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'United States',
            'country_code' => 'USA',
            'visa_type' => 'tourist',
            'visa_category' => 'B-2 (Tourist/Visitor)',
            'general_requirements' => "1. Valid passport with at least 6 months validity beyond intended stay
2. Completed DS-160 form (Online Non-immigrant Visa Application)
3. One recent photograph (2x2 inches, white background)
4. Visa application fee payment receipt
5. Interview appointment confirmation
6. Proof of strong ties to Bangladesh (employment letter, property documents, family ties)
7. Bank statements for last 6 months showing sufficient funds
8. Travel itinerary and hotel reservations
9. Purpose of visit documentation
10. Previous US visa history (if applicable)",
            'eligibility_criteria' => "- Must demonstrate intent to return to Bangladesh
- No criminal record
- Not overstayed in any country previously
- Sufficient financial resources to cover trip expenses
- No intention to work or study in the USA",
            'processing_time_info' => "Standard: 15-21 business days after interview
Express: Not available for US visas
Interview wait times vary by season (peak season: May-August)",
            'validity_info' => "Typical validity: 10 years (multiple entry)
Maximum stay per visit: Up to 6 months (decided by CBP officer at entry port)",
            'important_notes' => "⚠️ CRITICAL: 
- Interview is MANDATORY at US Embassy Dhaka
- Visa approval is at discretion of consular officer
- High rejection rate for first-time applicants without strong ties
- Previous visa rejection affects future applications
- Bring ALL original documents to interview",
            'min_bank_balance' => 500000.00,
            'bank_statement_months' => 6,
            'financial_requirements' => "Minimum BDT 5,00,000 in bank account
Consistent income/salary deposits visible
No sudden large deposits
Property ownership preferred
Tax returns (if applicable)",
            'government_fee' => 16000.00,
            'service_fee' => 15000.00,
            'processing_fee_standard' => 3000.00,
            'processing_days_standard' => 21,
            'interview_required' => true,
            'interview_details' => "Interview Location: US Embassy, Dhaka
Interview Duration: 5-15 minutes
Bring: All original documents, passport, DS-160 confirmation, appointment letter
Dress Code: Formal business attire
Language: English (translator not provided)",
            'biometrics_required' => true,
            'biometrics_details' => "10-finger biometric scan done at embassy during interview
Digital photograph taken at embassy",
            'application_method' => 'online',
            'embassy_website' => 'https://bd.usembassy.gov/',
            'application_process' => "Step 1: Pay visa fee at Sonali Bank
Step 2: Fill DS-160 form online
Step 3: Create profile on https://cgifederal.secure.force.com/
Step 4: Schedule interview appointment
Step 5: Attend interview with all documents
Step 6: Passport collection (if approved)",
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->addUSADocuments($usa);
        $this->addUSAProfessionRequirements($usa);

        $this->command->info('  ✅ USA Tourist Visa');
    }

    private function addUSADocuments($visaReq)
    {
        $documents = [
            ['name' => 'Valid Passport', 'type' => 'passport', 'desc' => "Original passport with minimum 6 months validity\nAt least 2 blank pages\nNo damage or water marks\nAll old passports (if any)", 'mandatory' => true, 'qty' => 1, 'format' => 'Original'],
            ['name' => 'DS-160 Confirmation', 'type' => 'form', 'desc' => "DS-160 confirmation page with barcode\nMust match passport details exactly\nRecent submission (within 30 days of interview)", 'mandatory' => true, 'qty' => 1, 'format' => 'Print out'],
            ['name' => 'Appointment Confirmation', 'type' => 'form', 'desc' => "Interview appointment letter\nVisa fee payment receipt from Sonali Bank", 'mandatory' => true, 'qty' => 1, 'format' => 'Print out'],
            ['name' => 'Recent Photograph', 'type' => 'photo', 'desc' => "2 x 2 inches (51mm x 51mm)\nWhite background\nTaken within last 6 months\nNo glasses, no smile\n80% face coverage", 'mandatory' => true, 'qty' => 2, 'format' => 'Color photo'],
            ['name' => 'Bank Statements', 'type' => 'financial', 'desc' => "Last 6 months bank statements\nAll pages with bank seal and signature\nConsistent income/deposits visible\nMinimum balance: BDT 5,00,000\nOriginal statements (no photocopies)", 'mandatory' => true, 'qty' => 1, 'format' => 'Original with bank seal'],
            ['name' => 'Employment Letter', 'type' => 'employment', 'desc' => "Company letterhead\nJob title, salary, joining date\nLeave approval for travel dates\nCompany seal and authorized signature\nHR contact information", 'mandatory' => true, 'qty' => 1, 'format' => 'Original on letterhead'],
            ['name' => 'Property Documents', 'type' => 'financial', 'desc' => "Land ownership deed\nHouse ownership papers\nRental agreements\nProperty tax receipts\nAny asset ownership proof", 'mandatory' => false, 'qty' => 1, 'format' => 'Original or certified copy'],
            ['name' => 'Travel Itinerary', 'type' => 'travel', 'desc' => "Detailed day-by-day travel plan\nHotel reservations (refundable)\nFlight booking (not yet ticketed)\nPlaces to visit\nReturn flight booking", 'mandatory' => true, 'qty' => 1, 'format' => 'Print out'],
            ['name' => 'Family Ties Proof', 'type' => 'personal', 'desc' => "Marriage certificate\nChildren's birth certificates\nFamily photographs\nSpouse employment letter\nProof of family staying in Bangladesh", 'mandatory' => false, 'qty' => 1, 'format' => 'Copy acceptable'],
            ['name' => 'Previous US Visa', 'type' => 'visa_history', 'desc' => "Old passport with US visa\nPrevious I-94 entry/exit records\nUS travel photos\nProof of timely return from previous visits", 'mandatory' => false, 'qty' => 1, 'format' => 'Original'],
        ];

        foreach ($documents as $index => $doc) {
            VisaRequirementDocument::create([
                'visa_requirement_id' => $visaReq->id,
                'document_name' => $doc['name'],
                'document_type' => $doc['type'],
                'description' => $doc['desc'],
                'is_mandatory' => $doc['mandatory'],
                'quantity' => $doc['qty'],
                'format' => $doc['format'],
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function addUSAProfessionRequirements($visaReq)
    {
        $professions = [
            [
                'category' => 'employed',
                'title' => 'Salaried Employee',
                'requirements' => "1. Employment letter on company letterhead
2. Salary slips for last 6 months
3. Tax returns (if applicable)
4. Company trade license copy
5. HR contact verification letter
6. Leave approval from employer",
                'additional_docs' => "- Company registration certificate
- Office ID card
- Promotion letters (if any)
- Salary increment letters",
                'balance' => 500000.00,
                'financial_proof' => "Salary deposits must be visible in bank statements
Consistent monthly income required
Tax deduction certificates strengthen application",
                'risk' => 1,
            ],
            [
                'category' => 'self_employed',
                'title' => 'Self-Employed/Freelancer',
                'requirements' => "1. Business registration documents
2. Tax returns for last 2 years
3. Client contracts or invoices
4. Business bank account statements
5. Proof of ongoing business operations
6. Professional credentials or licenses",
                'additional_docs' => "- Portfolio of work
- Client recommendation letters
- Business premises proof
- GST/VAT registration (if applicable)",
                'balance' => 700000.00,
                'financial_proof' => "Higher bank balance required
Proof of consistent business income
Multiple source income documentation
Asset ownership strongly recommended",
                'risk' => 2,
            ],
            [
                'category' => 'business_owner',
                'title' => 'Business Owner',
                'requirements' => "1. Company registration certificate
2. Trade license
3. Tax returns (personal and business)
4. Business bank statements (6 months)
5. Memorandum of Association
6. List of employees and payroll",
                'additional_docs' => "- Company office lease agreement
- Business utility bills
- Export/Import licenses (if applicable)
- Board resolution for travel",
                'balance' => 1000000.00,
                'financial_proof' => "Both personal and business accounts
Proof of business stability
Tax compliance certificates
Property ownership adds credibility",
                'risk' => 1,
            ],
            [
                'category' => 'student',
                'title' => 'Student',
                'requirements' => "1. Student ID card
2. University enrollment certificate
3. Semester grade sheets
4. Parents' employment letters
5. Parents' bank statements (6 months)
6. Sponsorship letter from parents
7. Property documents in parents' name",
                'additional_docs' => "- Birth certificate
- Relationship proof with sponsor
- University bonafide certificate
- Exam routine/calendar",
                'balance' => 400000.00,
                'financial_proof' => "Parents' financial documents required
Consistent income in parents' accounts
Property ownership in family name
Educational institution reputation matters",
                'risk' => 2,
            ],
            [
                'category' => 'retired',
                'title' => 'Retired Person',
                'requirements' => "1. Retirement certificate
2. Pension payment statements
3. Last 6 months bank statements
4. Previous employment history
5. Property documents
6. Children's employment letters (if accompanying)",
                'additional_docs' => "- Pension book
- Provident fund statements
- Investment documents
- Health insurance",
                'balance' => 800000.00,
                'financial_proof' => "Regular pension deposits visible
Adequate savings and investments
Property and asset documentation
Family ties proof important",
                'risk' => 1,
            ],
            [
                'category' => 'unemployed',
                'title' => 'Unemployed',
                'requirements' => "1. Sponsorship letter from employed family member
2. Sponsor's employment letter
3. Sponsor's bank statements (6 months)
4. Sponsor's property documents
5. Relationship proof with sponsor
6. Reason for unemployment explanation
7. Previous employment history (if any)",
                'additional_docs' => "- Affidavit of sponsorship
- Marriage certificate (if spouse sponsoring)
- Birth certificate (if parent sponsoring)
- Joint property ownership proof",
                'balance' => 300000.00,
                'financial_proof' => "Sponsor must have strong financial standing
Joint account with sponsor helps
Property ownership in name or jointly
Valid reason for unemployment needed",
                'risk' => 3,
            ],
            [
                'category' => 'homemaker',
                'title' => 'Homemaker',
                'requirements' => "1. Marriage certificate
2. Husband's employment letter
3. Husband's bank statements (6 months)
4. Joint property documents
5. Children's birth certificates
6. Sponsorship letter from husband
7. Family photographs",
                'additional_docs' => "- Joint bank account statements
- Utility bills in name
- Children's school certificates
- Previous travel history with family",
                'balance' => 400000.00,
                'financial_proof' => "Husband's strong financial profile essential
Joint accounts preferred
Property in joint names strengthens case
Children's education docs show ties",
                'risk' => 2,
            ],
        ];

        foreach ($professions as $index => $prof) {
            ProfessionVisaRequirement::create([
                'visa_requirement_id' => $visaReq->id,
                'profession_category' => $prof['category'],
                'profession_title' => $prof['title'],
                'additional_requirements' => $prof['requirements'],
                'additional_documents' => $prof['additional_docs'],
                'min_bank_balance_override' => $prof['balance'],
                'financial_proof_details' => $prof['financial_proof'],
                'risk_level' => $prof['risk'],
                'sort_order' => $index + 1,
            ]);
        }
    }

    /**
     * Seed UK Tourist Visa requirements
     */
    private function seedUKTouristVisa($visaService)
    {
        $uk = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'United Kingdom',
            'country_code' => 'GBR',
            'visa_type' => 'tourist',
            'visa_category' => 'Standard Visitor Visa',
            'general_requirements' => "1. Valid passport with at least 6 months validity
2. Online visa application form completed
3. Recent passport-sized photographs
4. Bank statements (last 6 months)
5. Employment or business documents
6. Hotel reservations and travel itinerary
7. Return flight booking
8. Purpose of visit explanation
9. Financial sponsorship documents (if applicable)
10. Previous travel history",
            'eligibility_criteria' => "- Genuine visitor with intention to leave UK
- No intention to work or study
- Sufficient funds for trip and stay
- Strong ties to home country",
            'processing_time_info' => "Standard: 15-20 working days
Priority Service: 5 working days (additional fee)
Super Priority: 24 hours (additional fee)",
            'validity_info' => "Validity: 6 months (standard)
Multiple entry allowed within validity
Maximum stay: 180 days total per year",
            'important_notes' => "⚠️ Interview may be required
⚠️ Biometric appointment mandatory
⚠️ No refund if visa rejected
⚠️ Processing time may vary",
            'min_bank_balance' => 400000.00,
            'bank_statement_months' => 6,
            'financial_requirements' => "Minimum £3,000 equivalent in BDT
Regular income sources
Sufficient funds to cover entire trip",
            'government_fee' => 12000.00,
            'service_fee' => 12000.00,
            'processing_fee_standard' => 2500.00,
            'processing_fee_express' => 8000.00,
            'processing_fee_urgent' => 15000.00,
            'processing_days_standard' => 20,
            'processing_days_express' => 5,
            'processing_days_urgent' => 1,
            'interview_required' => false,
            'biometrics_required' => true,
            'biometrics_details' => "Biometric appointment at VFS Global
Fingerprints and photograph required
Additional fee for biometrics",
            'application_method' => 'online',
            'embassy_website' => 'https://www.gov.uk/standard-visitor-visa',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->command->info('  ✅ UK Tourist Visa');
    }

    /**
     * Seed Canada Tourist Visa requirements
     */
    private function seedCanadaTouristVisa($visaService)
    {
        $canada = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'Canada',
            'country_code' => 'CAN',
            'visa_type' => 'tourist',
            'visa_category' => 'Temporary Resident Visa (TRV)',
            'general_requirements' => "1. Valid passport (6 months minimum validity)
2. Completed application form (IMM 5257)
3. Two recent photographs
4. Proof of financial support
5. Employment letter or business documents
6. Travel history and purpose
7. Ties to Bangladesh documentation
8. Invitation letter (if visiting someone)
9. Travel itinerary
10. Medical exam (if required)",
            'min_bank_balance' => 450000.00,
            'bank_statement_months' => 6,
            'government_fee' => 10000.00,
            'service_fee' => 10000.00,
            'processing_days_standard' => 25,
            'biometrics_required' => true,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $this->command->info('  ✅ Canada Tourist Visa');
    }

    /**
     * Seed Schengen Tourist Visa requirements
     */
    private function seedSchengenTouristVisa($visaService)
    {
        $schengen = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'Schengen Area',
            'country_code' => 'SCH',
            'visa_type' => 'tourist',
            'visa_category' => 'Short Stay Visa (Type C)',
            'general_requirements' => "1. Valid passport (3 months beyond visa expiry)
2. Completed Schengen visa application form
3. Two recent passport photographs
4. Travel medical insurance (minimum €30,000 coverage)
5. Flight reservation (round trip)
6. Hotel bookings for entire stay
7. Bank statements (last 3 months)
8. Employment certificate with leave approval
9. Detailed travel itinerary
10. Covering letter explaining purpose",
            'eligibility_criteria' => "- Valid purpose for travel
- Sufficient financial means
- Travel insurance coverage
- Intention to return",
            'min_bank_balance' => 350000.00,
            'bank_statement_months' => 3,
            'financial_requirements' => "€50-60 per day for entire trip
Proof of accommodation
Travel insurance mandatory",
            'government_fee' => 8000.00,
            'service_fee' => 8000.00,
            'processing_days_standard' => 15,
            'interview_required' => true,
            'biometrics_required' => true,
            'validity_info' => "Validity: Up to 90 days within 180 days period
Single or multiple entry",
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $this->command->info('  ✅ Schengen Tourist Visa');
    }

    /**
     * Seed Australia Tourist Visa requirements
     */
    private function seedAustraliaTouristVisa($visaService)
    {
        $australia = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'Australia',
            'country_code' => 'AUS',
            'visa_type' => 'tourist',
            'visa_category' => 'Visitor Visa (Subclass 600)',
            'general_requirements' => "1. Valid passport
2. Online visa application (ImmiAccount)
3. Recent passport photograph
4. Financial documents
5. Employment/business evidence
6. Travel plans and purpose
7. Health insurance (recommended)
8. Character requirements
9. Previous visa history
10. Ties to Bangladesh",
            'min_bank_balance' => 500000.00,
            'bank_statement_months' => 6,
            'government_fee' => 14500.00,
            'service_fee' => 12000.00,
            'processing_days_standard' => 20,
            'application_method' => 'online',
            'validity_info' => "Stay: Up to 3, 6, or 12 months
Multiple entry allowed",
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $this->command->info('  ✅ Australia Tourist Visa');
    }

    /**
     * Seed UAE Tourist Visa requirements
     */
    private function seedUAETouristVisa($visaService)
    {
        $uae = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'United Arab Emirates',
            'country_code' => 'ARE',
            'visa_type' => 'tourist',
            'visa_category' => 'Tourist Visa (30/90 days)',
            'general_requirements' => "1. Valid passport (minimum 6 months)
2. Recent passport-size photograph
3. Confirmed flight tickets
4. Hotel booking confirmation
5. Bank statements (last 3 months)
6. Travel insurance
7. Sponsor documents (if applicable)
8. Visit visa application form",
            'min_bank_balance' => 200000.00,
            'bank_statement_months' => 3,
            'government_fee' => 3500.00,
            'service_fee' => 4500.00,
            'processing_days_standard' => 5,
            'processing_days_express' => 2,
            'validity_info' => "30 days or 90 days validity
Single or multiple entry available",
            'is_active' => true,
            'sort_order' => 6,
        ]);

        $this->command->info('  ✅ UAE Tourist Visa');
    }

    /**
     * Seed Malaysia Tourist Visa requirements
     */
    private function seedMalaysiaTouristVisa($visaService)
    {
        $malaysia = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'Malaysia',
            'country_code' => 'MYS',
            'visa_type' => 'tourist',
            'visa_category' => 'Social Visit Visa',
            'general_requirements' => "1. Valid passport (6 months validity)
2. Visa application form (IM.47)
3. Recent passport photographs
4. Confirmed return flight
5. Hotel reservation
6. Bank statements (3 months)
7. Employment letter
8. Cover letter",
            'min_bank_balance' => 150000.00,
            'bank_statement_months' => 3,
            'government_fee' => 2800.00,
            'service_fee' => 3500.00,
            'processing_days_standard' => 7,
            'validity_info' => "Stay: 30 days
Can be extended in Malaysia",
            'is_active' => true,
            'sort_order' => 7,
        ]);

        $this->command->info('  ✅ Malaysia Tourist Visa');
    }

    /**
     * Seed Thailand Tourist Visa requirements
     */
    private function seedThailandTouristVisa($visaService)
    {
        $thailand = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'Thailand',
            'country_code' => 'THA',
            'visa_type' => 'tourist',
            'visa_category' => 'Tourist Visa (TR)',
            'general_requirements' => "1. Valid passport (6 months minimum)
2. Visa application form
3. Recent photograph (4x6 cm)
4. Bank statements (last 6 months)
5. Confirmed flight tickets
6. Hotel reservations
7. Employment certificate
8. Personal bank account with min 20,000 BDT",
            'min_bank_balance' => 100000.00,
            'bank_statement_months' => 6,
            'government_fee' => 3500.00,
            'service_fee' => 3000.00,
            'processing_days_standard' => 5,
            'validity_info' => "60 days stay
Can extend 30 days in Thailand",
            'is_active' => true,
            'sort_order' => 8,
        ]);

        $this->command->info('  ✅ Thailand Tourist Visa');
    }

    /**
     * Seed Singapore Tourist Visa requirements
     */
    private function seedSingaporeTouristVisa($visaService)
    {
        $singapore = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'Singapore',
            'country_code' => 'SGP',
            'visa_type' => 'tourist',
            'visa_category' => 'Tourist Visa',
            'general_requirements' => "1. Valid passport (6 months validity)
2. Completed Form 14A
3. Recent passport photograph
4. Bank statements (3 months)
5. Employment letter
6. Flight booking
7. Hotel reservation
8. Cover letter",
            'min_bank_balance' => 150000.00,
            'bank_statement_months' => 3,
            'government_fee' => 3000.00,
            'service_fee' => 4000.00,
            'processing_days_standard' => 5,
            'validity_info' => "30 days single entry
Multiple entry available",
            'is_active' => true,
            'sort_order' => 9,
        ]);

        $this->command->info('  ✅ Singapore Tourist Visa');
    }

    /**
     * Seed India Tourist Visa requirements
     */
    private function seedIndiaTouristVisa($visaService)
    {
        $india = VisaRequirement::create([
            'service_module_id' => $visaService?->id,
            'country' => 'India',
            'country_code' => 'IND',
            'visa_type' => 'tourist',
            'visa_category' => 'e-Tourist Visa',
            'general_requirements' => "1. Valid passport (6 months validity)
2. Recent photograph (digital)
3. Passport bio page scan
4. Return flight ticket
5. Hotel booking
6. Financial proof
7. Online application form",
            'min_bank_balance' => 100000.00,
            'bank_statement_months' => 3,
            'government_fee' => 1500.00,
            'service_fee' => 2500.00,
            'processing_days_standard' => 4,
            'processing_days_express' => 1,
            'application_method' => 'online',
            'validity_info' => "120 days validity
Stay: 90 days",
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $this->command->info('  ✅ India e-Tourist Visa');
    }
}

