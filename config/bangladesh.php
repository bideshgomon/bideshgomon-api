<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bangladesh-Specific Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains Bangladesh-specific settings for the application
    | including currency, date formats, phone formats, and cultural preferences.
    |
    */

    'currency' => [
        'code' => 'BDT',
        'symbol' => '৳',
        'name' => 'Bangladeshi Taka',
        'decimal_separator' => '.',
        'thousand_separator' => ',',
        'decimals' => 2,
        'format' => '৳%s', // e.g., ৳1,000.00
    ],

    'date_format' => [
        'display' => 'd/m/Y', // DD/MM/YYYY for Bangladesh
        'database' => 'Y-m-d',
        'datetime_display' => 'd/m/Y h:i A', // DD/MM/YYYY 12:30 PM
        'datetime_database' => 'Y-m-d H:i:s',
    ],

    'phone' => [
        'country_code' => '+880',
        'format' => '+880 1XXX-XXXXXX',
        'regex' => '/^(?:\+?880|0)?1[3-9]\d{8}$/',
        'placeholder' => '01712-345678',
        'mobile_operators' => [
            'Grameenphone' => ['017', '013'],
            'Banglalink' => ['019', '014'],
            'Robi' => ['018'],
            'Airtel' => ['016'],
            'Teletalk' => ['015'],
        ],
    ],

    'address' => [
        'format' => ['village_or_house', 'post_office', 'upazila', 'district', 'division'],
        'divisions' => [
            'Dhaka',
            'Chittagong',
            'Rajshahi',
            'Khulna',
            'Barisal',
            'Sylhet',
            'Rangpur',
            'Mymensingh',
        ],
    ],

    'language' => [
        'primary' => 'bn', // Bengali
        'secondary' => 'en', // English
    ],

    'common_destinations' => [
        // Popular countries for Bangladeshi travelers/migrants
        'work' => ['Saudi Arabia', 'UAE', 'Qatar', 'Oman', 'Kuwait', 'Bahrain', 'Malaysia', 'Singapore'],
        'education' => ['United States', 'United Kingdom', 'Canada', 'Australia', 'Malaysia', 'Germany', 'Japan', 'South Korea'],
        'tourism' => ['India', 'Thailand', 'Malaysia', 'Singapore', 'Turkey', 'UAE', 'Nepal', 'Sri Lanka'],
    ],

    'regulatory_bodies' => [
        'travel_agencies' => [
            'ATAB' => 'Association of Travel Agents of Bangladesh',
            'TOAB' => 'Tour Operators Association of Bangladesh',
            'IATA' => 'International Air Transport Association',
        ],
        'education' => [
            'ministry' => 'Ministry of Education, Bangladesh',
            'ugc' => 'University Grants Commission',
        ],
        'recruitment' => [
            'BOESL' => 'Bureau of Manpower, Employment and Training',
        ],
        'hajj' => [
            'ministry' => 'Ministry of Religious Affairs',
        ],
    ],

    'financial' => [
        'min_transaction' => 100, // Minimum BDT for transactions
        'max_transaction' => 1000000, // Maximum BDT for transactions
        'referral_bonus' => 100, // BDT
        'signup_bonus' => 50, // BDT
    ],

    'documents' => [
        'nid_format' => '10 or 17 digits',
        'passport_format' => 'Bangladesh passport: A, B, C, D, E, F, G, H, S, P + 7 digits',
        'tin_format' => '12 digits',
    ],

    'working_hours' => [
        'government' => '9:00 AM - 5:00 PM (Sun-Thu)',
        'private' => '9:00 AM - 6:00 PM (Sun-Thu)',
        'weekend' => ['Friday', 'Saturday'],
    ],

    'holidays' => [
        'national' => [
            '21 February' => 'International Mother Language Day',
            '26 March' => 'Independence Day',
            '14 April' => 'Pohela Boishakh (Bengali New Year)',
            '1 May' => 'Labour Day',
            '16 December' => 'Victory Day',
        ],
        'religious' => [
            'Eid-ul-Fitr' => '2 days',
            'Eid-ul-Adha' => '3 days',
            'Durga Puja' => '2 days',
            'Buddha Purnima' => '1 day',
            'Christmas' => '1 day',
        ],
    ],

    'visa_common_requirements' => [
        'passport' => 'Valid for at least 6 months',
        'photo' => 'Recent passport-size photo (white background)',
        'nid' => 'National ID card copy',
        'bank_statement' => 'Last 6 months bank statement',
        'police_clearance' => 'Police clearance certificate',
    ],

    'education_equivalence' => [
        'SSC' => 'Secondary School Certificate (Class 10)',
        'HSC' => 'Higher Secondary Certificate (Class 12)',
        'Honours' => "Bachelor's Degree (3-4 years)",
        'Masters' => "Master's Degree (1-2 years)",
    ],

    'cultural_preferences' => [
        'name_format' => 'full_name', // Bangladeshis typically use full name, not first/last
        'family_structure' => 'joint_family_common',
        'religion_important' => true,
        'halal_food' => true,
    ],

];
