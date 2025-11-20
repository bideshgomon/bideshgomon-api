<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            // BDT as base currency (rate = 1)
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳', 'exchange_rate_to_bdt' => 1.00],
            
            // Major world currencies (example rates - should be updated regularly)
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate_to_bdt' => 110.50],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate_to_bdt' => 120.25],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate_to_bdt' => 140.75],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'exchange_rate_to_bdt' => 82.30],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate_to_bdt' => 72.15],
            ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$', 'exchange_rate_to_bdt' => 67.80],
            
            // Middle East currencies
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'ر.س', 'exchange_rate_to_bdt' => 29.45],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'exchange_rate_to_bdt' => 30.08],
            ['code' => 'QAR', 'name' => 'Qatari Riyal', 'symbol' => 'ر.ق', 'exchange_rate_to_bdt' => 30.35],
            ['code' => 'KWD', 'name' => 'Kuwaiti Dinar', 'symbol' => 'د.ك', 'exchange_rate_to_bdt' => 360.25],
            ['code' => 'BHD', 'name' => 'Bahraini Dinar', 'symbol' => 'د.ب', 'exchange_rate_to_bdt' => 293.15],
            ['code' => 'OMR', 'name' => 'Omani Rial', 'symbol' => 'ر.ع', 'exchange_rate_to_bdt' => 287.40],
            ['code' => 'JOD', 'name' => 'Jordanian Dinar', 'symbol' => 'د.ا', 'exchange_rate_to_bdt' => 155.88],
            
            // Asian currencies
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate_to_bdt' => 1.32],
            ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => '₨', 'exchange_rate_to_bdt' => 0.40],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'exchange_rate_to_bdt' => 15.35],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate_to_bdt' => 0.74],
            ['code' => 'KRW', 'name' => 'South Korean Won', 'symbol' => '₩', 'exchange_rate_to_bdt' => 0.083],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'exchange_rate_to_bdt' => 24.75],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'exchange_rate_to_bdt' => 82.10],
            ['code' => 'THB', 'name' => 'Thai Baht', 'symbol' => '฿', 'exchange_rate_to_bdt' => 3.15],
            ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'exchange_rate_to_bdt' => 0.0070],
            ['code' => 'PHP', 'name' => 'Philippine Peso', 'symbol' => '₱', 'exchange_rate_to_bdt' => 1.97],
            ['code' => 'VND', 'name' => 'Vietnamese Dong', 'symbol' => '₫', 'exchange_rate_to_bdt' => 0.0045],
            
            // European currencies
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'Fr', 'exchange_rate_to_bdt' => 125.60],
            ['code' => 'SEK', 'name' => 'Swedish Krona', 'symbol' => 'kr', 'exchange_rate_to_bdt' => 10.55],
            ['code' => 'NOK', 'name' => 'Norwegian Krone', 'symbol' => 'kr', 'exchange_rate_to_bdt' => 10.35],
            ['code' => 'DKK', 'name' => 'Danish Krone', 'symbol' => 'kr', 'exchange_rate_to_bdt' => 16.15],
            ['code' => 'PLN', 'name' => 'Polish Zloty', 'symbol' => 'zł', 'exchange_rate_to_bdt' => 27.80],
            ['code' => 'CZK', 'name' => 'Czech Koruna', 'symbol' => 'Kč', 'exchange_rate_to_bdt' => 4.85],
            ['code' => 'HUF', 'name' => 'Hungarian Forint', 'symbol' => 'Ft', 'exchange_rate_to_bdt' => 0.31],
            ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => '₽', 'exchange_rate_to_bdt' => 1.20],
            ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => '₺', 'exchange_rate_to_bdt' => 3.30],
            
            // Americas
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$', 'exchange_rate_to_bdt' => 22.15],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$', 'exchange_rate_to_bdt' => 6.45],
            ['code' => 'ARS', 'name' => 'Argentine Peso', 'symbol' => '$', 'exchange_rate_to_bdt' => 0.11],
            
            // Africa
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R', 'exchange_rate_to_bdt' => 6.05],
            ['code' => 'EGP', 'name' => 'Egyptian Pound', 'symbol' => 'ج.م', 'exchange_rate_to_bdt' => 2.25],
            ['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦', 'exchange_rate_to_bdt' => 0.073],
            ['code' => 'KES', 'name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'exchange_rate_to_bdt' => 0.86],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
