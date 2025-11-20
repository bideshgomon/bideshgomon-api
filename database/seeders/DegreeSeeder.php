<?php

namespace Database\Seeders;

use App\Models\Degree;
use Illuminate\Database\Seeder;

class DegreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $degrees = [
            // Secondary Level
            ['name' => 'Secondary School Certificate', 'name_bn' => 'মাধ্যমিক স্কুল সার্টিফিকেট', 'short_name' => 'SSC', 'level' => 'secondary', 'typical_duration_years' => 2],
            ['name' => 'O Level', 'name_bn' => 'ও লেভেল', 'short_name' => 'O Level', 'level' => 'secondary', 'typical_duration_years' => 2],
            
            // Higher Secondary Level
            ['name' => 'Higher Secondary Certificate', 'name_bn' => 'উচ্চ মাধ্যমিক সার্টিফিকেট', 'short_name' => 'HSC', 'level' => 'higher_secondary', 'typical_duration_years' => 2],
            ['name' => 'A Level', 'name_bn' => 'এ লেভেল', 'short_name' => 'A Level', 'level' => 'higher_secondary', 'typical_duration_years' => 2],
            
            // Diploma Level
            ['name' => 'Diploma in Engineering', 'name_bn' => 'ডিপ্লোমা ইন ইঞ্জিনিয়ারিং', 'short_name' => 'Dip.Eng', 'level' => 'diploma', 'typical_duration_years' => 4],
            ['name' => 'Diploma in Commerce', 'name_bn' => 'ডিপ্লোমা ইন কমার্স', 'short_name' => 'Dip.Com', 'level' => 'diploma', 'typical_duration_years' => 3],
            ['name' => 'Diploma in Computer Science', 'name_bn' => 'ডিপ্লোমা ইন কম্পিউটার সায়েন্স', 'short_name' => 'Dip.CS', 'level' => 'diploma', 'typical_duration_years' => 3],
            
            // Bachelor Level
            ['name' => 'Bachelor of Science', 'name_bn' => 'ব্যাচেলর অব সায়েন্স', 'short_name' => 'B.Sc.', 'level' => 'bachelor', 'typical_duration_years' => 4],
            ['name' => 'Bachelor of Arts', 'name_bn' => 'ব্যাচেলর অব আর্টস', 'short_name' => 'B.A.', 'level' => 'bachelor', 'typical_duration_years' => 4],
            ['name' => 'Bachelor of Business Administration', 'name_bn' => 'ব্যাচেলর অব বিজনেস অ্যাডমিনিস্ট্রেশন', 'short_name' => 'BBA', 'level' => 'bachelor', 'typical_duration_years' => 4],
            ['name' => 'Bachelor of Engineering', 'name_bn' => 'ব্যাচেলর অব ইঞ্জিনিয়ারিং', 'short_name' => 'B.Eng.', 'level' => 'bachelor', 'typical_duration_years' => 4],
            ['name' => 'Bachelor of Technology', 'name_bn' => 'ব্যাচেলর অব টেকনোলজি', 'short_name' => 'B.Tech', 'level' => 'bachelor', 'typical_duration_years' => 4],
            ['name' => 'Bachelor of Computer Science', 'name_bn' => 'ব্যাচেলর অব কম্পিউটার সায়েন্স', 'short_name' => 'B.CS', 'level' => 'bachelor', 'typical_duration_years' => 4],
            ['name' => 'Bachelor of Commerce', 'name_bn' => 'ব্যাচেলর অব কমার্স', 'short_name' => 'B.Com', 'level' => 'bachelor', 'typical_duration_years' => 4],
            ['name' => 'Bachelor of Social Science', 'name_bn' => 'ব্যাচেলর অব সোশ্যাল সায়েন্স', 'short_name' => 'BSS', 'level' => 'bachelor', 'typical_duration_years' => 4],
            ['name' => 'Bachelor of Laws', 'name_bn' => 'ব্যাচেলর অব ল', 'short_name' => 'LL.B.', 'level' => 'bachelor', 'typical_duration_years' => 4],
            ['name' => 'Bachelor of Medicine', 'name_bn' => 'ব্যাচেলর অব মেডিসিন', 'short_name' => 'MBBS', 'level' => 'bachelor', 'typical_duration_years' => 5],
            
            // Master Level
            ['name' => 'Master of Science', 'name_bn' => 'মাস্টার অব সায়েন্স', 'short_name' => 'M.Sc.', 'level' => 'master', 'typical_duration_years' => 2],
            ['name' => 'Master of Arts', 'name_bn' => 'মাস্টার অব আর্টস', 'short_name' => 'M.A.', 'level' => 'master', 'typical_duration_years' => 2],
            ['name' => 'Master of Business Administration', 'name_bn' => 'মাস্টার অব বিজনেস অ্যাডমিনিস্ট্রেশন', 'short_name' => 'MBA', 'level' => 'master', 'typical_duration_years' => 2],
            ['name' => 'Master of Engineering', 'name_bn' => 'মাস্টার অব ইঞ্জিনিয়ারিং', 'short_name' => 'M.Eng.', 'level' => 'master', 'typical_duration_years' => 2],
            ['name' => 'Master of Computer Science', 'name_bn' => 'মাস্টার অব কম্পিউটার সায়েন্স', 'short_name' => 'M.CS', 'level' => 'master', 'typical_duration_years' => 2],
            ['name' => 'Master of Commerce', 'name_bn' => 'মাস্টার অব কমার্স', 'short_name' => 'M.Com', 'level' => 'master', 'typical_duration_years' => 2],
            ['name' => 'Master of Social Science', 'name_bn' => 'মাস্টার অব সোশ্যাল সায়েন্স', 'short_name' => 'MSS', 'level' => 'master', 'typical_duration_years' => 2],
            ['name' => 'Master of Laws', 'name_bn' => 'মাস্টার অব ল', 'short_name' => 'LL.M.', 'level' => 'master', 'typical_duration_years' => 1],
            ['name' => 'Master of Public Health', 'name_bn' => 'মাস্টার অব পাবলিক হেলথ', 'short_name' => 'MPH', 'level' => 'master', 'typical_duration_years' => 2],
            
            // Doctorate Level
            ['name' => 'Doctor of Philosophy', 'name_bn' => 'ডক্টর অব ফিলোসফি', 'short_name' => 'Ph.D.', 'level' => 'doctorate', 'typical_duration_years' => 4],
            ['name' => 'Doctor of Medicine', 'name_bn' => 'ডক্টর অব মেডিসিন', 'short_name' => 'M.D.', 'level' => 'doctorate', 'typical_duration_years' => 3],
            ['name' => 'Doctor of Engineering', 'name_bn' => 'ডক্টর অব ইঞ্জিনিয়ারিং', 'short_name' => 'D.Eng.', 'level' => 'doctorate', 'typical_duration_years' => 4],
        ];

        foreach ($degrees as $degree) {
            Degree::create($degree);
        }
    }
}
