<?php

namespace Database\Seeders;

use App\Models\VisaType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VisaTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temporarily disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Truncate the table
        DB::table('visa_types')->truncate();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // Comprehensive list of visa types - Bangladesh context
        $visaTypes = [
            // Work/Employment Visas (Very common for Bangladeshi workers)
            [
                'name' => 'Work Visa',
                'slug' => 'work-visa',
                'description' => 'For individuals seeking employment in a foreign country. Popular destinations include Middle East, Japan, Korea, Malaysia, and Western countries.',
                'is_active' => true,
            ],
            [
                'name' => 'Skilled Worker Visa',
                'slug' => 'skilled-worker-visa',
                'description' => 'For skilled professionals with job offers in specialized fields.',
                'is_active' => true,
            ],
            [
                'name' => 'Temporary Work Visa',
                'slug' => 'temporary-work-visa',
                'description' => 'For short-term or seasonal employment opportunities.',
                'is_active' => true,
            ],
            [
                'name' => 'Intra-Company Transfer Visa',
                'slug' => 'intra-company-transfer-visa',
                'description' => 'For employees transferred within the same company to a foreign branch.',
                'is_active' => true,
            ],

            // Student Visas (Major category for Bangladeshi students)
            [
                'name' => 'Student Visa',
                'slug' => 'student-visa',
                'description' => 'For international students pursuing education abroad. Top destinations: USA, UK, Canada, Australia, Germany, Malaysia.',
                'is_active' => true,
            ],
            [
                'name' => 'Student Dependent Visa',
                'slug' => 'student-dependent-visa',
                'description' => 'For dependents (spouse/children) of international students.',
                'is_active' => true,
            ],
            [
                'name' => 'Post-Study Work Visa',
                'slug' => 'post-study-work-visa',
                'description' => 'Allows graduates to work in the country after completing studies.',
                'is_active' => true,
            ],

            // Tourist/Visit Visas (Very common for Bangladeshis)
            [
                'name' => 'Tourist Visa',
                'slug' => 'tourist-visa',
                'description' => 'For leisure travel, sightseeing, and vacation purposes.',
                'is_active' => true,
            ],
            [
                'name' => 'Visit Visa',
                'slug' => 'visit-visa',
                'description' => 'For visiting family members or friends in another country.',
                'is_active' => true,
            ],
            [
                'name' => 'Business Visit Visa',
                'slug' => 'business-visit-visa',
                'description' => 'For short-term business meetings, conferences, and trade activities.',
                'is_active' => true,
            ],
            [
                'name' => 'Medical Visa',
                'slug' => 'medical-visa',
                'description' => 'For receiving medical treatment abroad. Popular destinations: India, Thailand, Singapore.',
                'is_active' => true,
            ],
            [
                'name' => 'Medical Attendant Visa',
                'slug' => 'medical-attendant-visa',
                'description' => 'For accompanying a patient seeking medical treatment.',
                'is_active' => true,
            ],

            // Religious Visas (Important for Bangladeshi Muslims)
            [
                'name' => 'Hajj Visa',
                'slug' => 'hajj-visa',
                'description' => 'For performing Hajj pilgrimage in Saudi Arabia.',
                'is_active' => true,
            ],
            [
                'name' => 'Umrah Visa',
                'slug' => 'umrah-visa',
                'description' => 'For performing Umrah (minor pilgrimage) in Saudi Arabia.',
                'is_active' => true,
            ],
            [
                'name' => 'Religious Pilgrimage Visa',
                'slug' => 'religious-pilgrimage-visa',
                'description' => 'For religious visits and pilgrimages to holy sites.',
                'is_active' => true,
            ],

            // Business Visas
            [
                'name' => 'Business Visa',
                'slug' => 'business-visa',
                'description' => 'For conducting business activities, attending meetings, and exploring business opportunities.',
                'is_active' => true,
            ],
            [
                'name' => 'Investor Visa',
                'slug' => 'investor-visa',
                'description' => 'For individuals making significant investments in a foreign country.',
                'is_active' => true,
            ],
            [
                'name' => 'Entrepreneur Visa',
                'slug' => 'entrepreneur-visa',
                'description' => 'For starting and operating a business in another country.',
                'is_active' => true,
            ],

            // Family/Dependent Visas
            [
                'name' => 'Family Reunion Visa',
                'slug' => 'family-reunion-visa',
                'description' => 'For joining family members who are permanent residents or citizens.',
                'is_active' => true,
            ],
            [
                'name' => 'Spouse Visa',
                'slug' => 'spouse-visa',
                'description' => 'For spouses of citizens or permanent residents.',
                'is_active' => true,
            ],
            [
                'name' => 'Dependent Visa',
                'slug' => 'dependent-visa',
                'description' => 'For dependents (children, parents) of visa holders.',
                'is_active' => true,
            ],
            [
                'name' => 'Fiancé(e) Visa',
                'slug' => 'fiance-visa',
                'description' => 'For entering a country to marry a citizen or resident.',
                'is_active' => true,
            ],

            // Immigration/Permanent Residence
            [
                'name' => 'Permanent Residence Visa',
                'slug' => 'permanent-residence-visa',
                'description' => 'For obtaining permanent residency status in another country.',
                'is_active' => true,
            ],
            [
                'name' => 'Immigration Visa',
                'slug' => 'immigration-visa',
                'description' => 'For permanently relocating to another country.',
                'is_active' => true,
            ],
            [
                'name' => 'Skilled Migration Visa',
                'slug' => 'skilled-migration-visa',
                'description' => 'Points-based visa for skilled workers seeking permanent residence.',
                'is_active' => true,
            ],

            // Transit Visas
            [
                'name' => 'Transit Visa',
                'slug' => 'transit-visa',
                'description' => 'For passing through a country while traveling to another destination.',
                'is_active' => true,
            ],
            [
                'name' => 'Airport Transit Visa',
                'slug' => 'airport-transit-visa',
                'description' => 'For transiting through an airport without entering the country.',
                'is_active' => true,
            ],

            // Special Purpose Visas
            [
                'name' => 'Training Visa',
                'slug' => 'training-visa',
                'description' => 'For attending training programs or workshops.',
                'is_active' => true,
            ],
            [
                'name' => 'Internship Visa',
                'slug' => 'internship-visa',
                'description' => 'For internships and practical training opportunities.',
                'is_active' => true,
            ],
            [
                'name' => 'Research Visa',
                'slug' => 'research-visa',
                'description' => 'For researchers and academics conducting research.',
                'is_active' => true,
            ],
            [
                'name' => 'Exchange Visitor Visa',
                'slug' => 'exchange-visitor-visa',
                'description' => 'For cultural exchange programs and educational exchanges.',
                'is_active' => true,
            ],
            [
                'name' => 'Volunteer Visa',
                'slug' => 'volunteer-visa',
                'description' => 'For participating in volunteer work or humanitarian projects.',
                'is_active' => true,
            ],
            [
                'name' => 'Journalist Visa',
                'slug' => 'journalist-visa',
                'description' => 'For journalists and media professionals on assignment.',
                'is_active' => true,
            ],
            [
                'name' => 'Artist/Performer Visa',
                'slug' => 'artist-performer-visa',
                'description' => 'For artists, performers, and entertainers.',
                'is_active' => true,
            ],
            [
                'name' => 'Athlete Visa',
                'slug' => 'athlete-visa',
                'description' => 'For athletes participating in sports events.',
                'is_active' => true,
            ],

            // Diplomatic & Official
            [
                'name' => 'Diplomatic Visa',
                'slug' => 'diplomatic-visa',
                'description' => 'For diplomats and government officials.',
                'is_active' => true,
            ],
            [
                'name' => 'Official Visa',
                'slug' => 'official-visa',
                'description' => 'For government employees on official business.',
                'is_active' => true,
            ],

            // Special Categories
            [
                'name' => 'Refugee Visa',
                'slug' => 'refugee-visa',
                'description' => 'For individuals fleeing persecution or conflict.',
                'is_active' => true,
            ],
            [
                'name' => 'Asylum Visa',
                'slug' => 'asylum-visa',
                'description' => 'For seeking asylum in another country.',
                'is_active' => true,
            ],
            [
                'name' => 'Retirement Visa',
                'slug' => 'retirement-visa',
                'description' => 'For retirees wishing to live in another country.',
                'is_active' => true,
            ],
            [
                'name' => 'Working Holiday Visa',
                'slug' => 'working-holiday-visa',
                'description' => 'For young people to work and travel (age restrictions apply).',
                'is_active' => true,
            ],
            [
                'name' => 'Au Pair Visa',
                'slug' => 'au-pair-visa',
                'description' => 'For cultural exchange through childcare work with host families.',
                'is_active' => true,
            ],
            [
                'name' => 'Domestic Worker Visa',
                'slug' => 'domestic-worker-visa',
                'description' => 'For domestic workers accompanying employers.',
                'is_active' => true,
            ],
            [
                'name' => 'Seafarer Visa',
                'slug' => 'seafarer-visa',
                'description' => 'For crew members of ships and vessels.',
                'is_active' => true,
            ],

            // E-Visas and Online Visas
            [
                'name' => 'E-Visa',
                'slug' => 'e-visa',
                'description' => 'Electronic visa obtained online without embassy visit.',
                'is_active' => true,
            ],
            [
                'name' => 'Visa on Arrival',
                'slug' => 'visa-on-arrival',
                'description' => 'Visa issued upon arrival at the destination airport.',
                'is_active' => true,
            ],

            // Other
            [
                'name' => 'Other',
                'slug' => 'other',
                'description' => 'Other visa types not listed above.',
                'is_active' => true,
            ],
        ];

        // Insert the data
        foreach ($visaTypes as $type) {
            VisaType::create($type);
        }

        $this->command->info('Visa types seeded successfully!');
    }
}
