<?php

namespace Database\Seeders;

use App\Models\BankName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BankNameSeeder extends Seeder
{
    /**
     * Run the database seeds - Bangladesh banks
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('bank_names')->truncate();
        Schema::enableForeignKeyConstraints();

        $banks = [
            // State-Owned Commercial Banks
            ['name' => 'Sonali Bank Limited', 'name_bn' => 'সোনালী ব্যাংক লিমিটেড', 'slug' => 'sonali-bank', 'short_name' => 'Sonali', 'swift_code' => 'BSONBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.sonalibank.com.bd', 'description' => 'Largest commercial bank in Bangladesh', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Janata Bank Limited', 'name_bn' => 'জনতা ব্যাংক লিমিটেড', 'slug' => 'janata-bank', 'short_name' => 'Janata', 'swift_code' => 'JANBBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.jb.com.bd', 'description' => 'Second largest state-owned bank', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Agrani Bank Limited', 'name_bn' => 'অগ্রণী ব্যাংক লিমিটেড', 'slug' => 'agrani-bank', 'short_name' => 'Agrani', 'swift_code' => 'AGBKBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.agranibank.org', 'description' => 'Leading government commercial bank', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Rupali Bank Limited', 'name_bn' => 'রূপালী ব্যাংক লিমিটেড', 'slug' => 'rupali-bank', 'short_name' => 'Rupali', 'swift_code' => 'BDEOBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.rupalibank.org', 'description' => 'State-owned commercial bank', 'is_active' => true, 'sort_order' => 4],
            
            // Private Commercial Banks (Most Popular)
            ['name' => 'Dutch-Bangla Bank Limited', 'name_bn' => 'ডাচ্-বাংলা ব্যাংক লিমিটেড', 'slug' => 'dutch-bangla-bank', 'short_name' => 'DBBL', 'swift_code' => 'DBBLBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.dutchbanglabank.com', 'description' => 'Leading digital bank with Rocket mobile banking', 'is_active' => true, 'sort_order' => 10],
            ['name' => 'BRAC Bank Limited', 'name_bn' => 'ব্র্যাক ব্যাংক লিমিটেড', 'slug' => 'brac-bank', 'short_name' => 'BRAC', 'swift_code' => 'BRAKBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.bracbank.com', 'description' => 'Popular SME and retail bank', 'is_active' => true, 'sort_order' => 11],
            ['name' => 'Eastern Bank Limited', 'name_bn' => 'ইস্টার্ন ব্যাংক লিমিটেড', 'slug' => 'eastern-bank', 'short_name' => 'EBL', 'swift_code' => 'EBLDBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.ebl.com.bd', 'description' => 'First private sector bank', 'is_active' => true, 'sort_order' => 12],
            ['name' => 'City Bank Limited', 'name_bn' => 'সিটি ব্যাংক লিমিটেড', 'slug' => 'city-bank', 'short_name' => 'City', 'swift_code' => 'CIBLBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.thecitybank.com', 'description' => 'Modern commercial bank', 'is_active' => true, 'sort_order' => 13],
            ['name' => 'Prime Bank Limited', 'name_bn' => 'প্রাইম ব্যাংক লিমিটেড', 'slug' => 'prime-bank', 'short_name' => 'Prime', 'swift_code' => 'PRBLBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.primebank.com.bd', 'description' => 'Leading private commercial bank', 'is_active' => true, 'sort_order' => 14],
            ['name' => 'Mutual Trust Bank Limited', 'name_bn' => 'মিউচুয়াল ট্রাস্ট ব্যাংক লিমিটেড', 'slug' => 'mutual-trust-bank', 'short_name' => 'MTB', 'swift_code' => 'MTBLBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.mutualtrustbank.com', 'description' => 'Customer-focused commercial bank', 'is_active' => true, 'sort_order' => 15],
            ['name' => 'Standard Chartered Bank', 'name_bn' => 'স্ট্যান্ডার্ড চার্টার্ড ব্যাংক', 'slug' => 'standard-chartered', 'short_name' => 'SCB', 'swift_code' => 'SCBLBDDX', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.sc.com/bd', 'description' => 'International bank in Bangladesh', 'is_active' => true, 'sort_order' => 16],
            ['name' => 'HSBC Bangladesh', 'name_bn' => 'এইচএসবিসি বাংলাদেশ', 'slug' => 'hsbc-bangladesh', 'short_name' => 'HSBC', 'swift_code' => 'HSBCBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.hsbc.com.bd', 'description' => 'Global banking in Bangladesh', 'is_active' => true, 'sort_order' => 17],
            
            // Islamic Banks
            ['name' => 'Islami Bank Bangladesh Limited', 'name_bn' => 'ইসলামী ব্যাংক বাংলাদেশ লিমিটেড', 'slug' => 'islami-bank', 'short_name' => 'IBBL', 'swift_code' => 'IBBLBDDH', 'routing_number' => null, 'type' => 'Islamic', 'website' => 'https://www.islamibankbd.com', 'description' => 'Largest Islamic bank in Bangladesh', 'is_active' => true, 'sort_order' => 20],
            ['name' => 'Al-Arafah Islami Bank Limited', 'name_bn' => 'আল-আরাফাহ ইসলামী ব্যাংক লিমিটেড', 'slug' => 'al-arafah-bank', 'short_name' => 'AIBL', 'swift_code' => 'ALARBDDH', 'routing_number' => null, 'type' => 'Islamic', 'website' => 'https://www.al-arafahbank.com', 'description' => 'Shariah-based banking services', 'is_active' => true, 'sort_order' => 21],
            ['name' => 'Social Islami Bank Limited', 'name_bn' => 'সোশ্যাল ইসলামী ব্যাংক লিমিটেড', 'slug' => 'social-islami-bank', 'short_name' => 'SIBL', 'swift_code' => 'SISBBDDH', 'routing_number' => null, 'type' => 'Islamic', 'website' => 'https://www.siblbd.com', 'description' => 'Islamic banking and investment', 'is_active' => true, 'sort_order' => 22],
            ['name' => 'Exim Bank Limited', 'name_bn' => 'এক্সিম ব্যাংক লিমিটেড', 'slug' => 'exim-bank', 'short_name' => 'EXIM', 'swift_code' => 'EXBKBDDH', 'routing_number' => null, 'type' => 'Islamic', 'website' => 'https://www.eximbank bd.com', 'description' => 'Export Import Bank of Bangladesh', 'is_active' => true, 'sort_order' => 23],
            
            // Specialized Banks
            ['name' => 'Bangladesh Development Bank Limited', 'name_bn' => 'বাংলাদেশ উন্নয়ন ব্যাংক লিমিটেড', 'slug' => 'bangladesh-development-bank', 'short_name' => 'BDBL', 'swift_code' => null, 'routing_number' => null, 'type' => 'specialized', 'website' => 'https://www.bdbl.com.bd', 'description' => 'Industrial development financing', 'is_active' => true, 'sort_order' => 30],
            ['name' => 'Bangladesh Krishi Bank', 'name_bn' => 'বাংলাদেশ কৃষি ব্যাংক', 'slug' => 'bangladesh-krishi-bank', 'short_name' => 'BKB', 'swift_code' => 'BKBABDDH', 'routing_number' => null, 'type' => 'specialized', 'website' => 'https://www.krishibank.gov.bd', 'description' => 'Agricultural financing', 'is_active' => true, 'sort_order' => 31],
            ['name' => 'Rajshahi Krishi Unnayan Bank', 'name_bn' => 'রাজশাহী কৃষি উন্নয়ন ব্যাংক', 'slug' => 'rajshahi-krishi-bank', 'short_name' => 'RAKUB', 'swift_code' => null, 'routing_number' => null, 'type' => 'specialized', 'website' => 'https://www.rakub.gov.bd', 'description' => 'Regional agricultural development', 'is_active' => true, 'sort_order' => 32],
            
            // More Private Banks
            ['name' => 'Bank Asia Limited', 'name_bn' => 'ব্যাংক এশিয়া লিমিটেড', 'slug' => 'bank-asia', 'short_name' => 'Bank Asia', 'swift_code' => 'BALBBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.bankasia-bd.com', 'description' => 'Technology-driven banking', 'is_active' => true, 'sort_order' => 40],
            ['name' => 'Southeast Bank Limited', 'name_bn' => 'সাউথইস্ট ব্যাংক লিমিটেড', 'slug' => 'southeast-bank', 'short_name' => 'SEBL', 'swift_code' => 'SEBDBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.sebankbd.com', 'description' => 'Modern retail banking', 'is_active' => true, 'sort_order' => 41],
            ['name' => 'Dhaka Bank Limited', 'name_bn' => 'ঢাকা ব্যাংক লিমিটেড', 'slug' => 'dhaka-bank', 'short_name' => 'Dhaka Bank', 'swift_code' => 'DHBKBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.dhakabankltd.com', 'description' => 'Capital city focused banking', 'is_active' => true, 'sort_order' => 42],
            ['name' => 'Mercantile Bank Limited', 'name_bn' => 'মার্কেন্টাইল ব্যাংক লিমিটেড', 'slug' => 'mercantile-bank', 'short_name' => 'MBL', 'swift_code' => 'MERBBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.mblbd.com', 'description' => 'Trade and commerce banking', 'is_active' => true, 'sort_order' => 43],
            ['name' => 'National Bank Limited', 'name_bn' => 'ন্যাশনাল ব্যাংক লিমিটেড', 'slug' => 'national-bank', 'short_name' => 'NBL', 'swift_code' => 'NBLIBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.nblbd.com', 'description' => 'Nationwide banking network', 'is_active' => true, 'sort_order' => 44],
            ['name' => 'Uttara Bank Limited', 'name_bn' => 'উত্তরা ব্যাংক লিমিটেড', 'slug' => 'uttara-bank', 'short_name' => 'UBL', 'swift_code' => 'UTBLBDDH', 'routing_number' => null, 'type' => 'commercial', 'website' => 'https://www.uttarabank-bd.com', 'description' => 'Customer-centric banking', 'is_active' => true, 'sort_order' => 45],
        ];

        foreach ($banks as $bank) {
            BankName::create($bank);
        }

        $this->command->info('Bangladesh banks seeded: ' . count($banks) . ' entries');
    }
}
