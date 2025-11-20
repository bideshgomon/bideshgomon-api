<?php

if (! function_exists('format_bd_currency')) {
    /**
     * Format amount in Bangladeshi Taka.
     *
     * @param  float  $amount
     * @param  bool  $showSymbol
     * @return string
     */
    function format_bd_currency($amount, $showSymbol = true)
    {
        $formatted = number_format($amount, 2, '.', ',');

        return $showSymbol ? '৳'.$formatted : $formatted;
    }
}

if (! function_exists('format_bd_phone')) {
    /**
     * Format Bangladeshi phone number.
     *
     * @param  string  $phone
     * @return string
     */
    function format_bd_phone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading 880 or 0
        $phone = preg_replace('/^(880|0)/', '', $phone);

        // Format as 01XXX-XXXXXX
        if (strlen($phone) === 10 && $phone[0] === '1') {
            return '0'.substr($phone, 0, 4).'-'.substr($phone, 4);
        }

        return $phone;
    }
}

if (! function_exists('format_bd_date')) {
    /**
     * Format date in DD/MM/YYYY format (Bangladeshi standard).
     *
     * @param  string|DateTime  $date
     * @param  bool  $includeTime
     * @return string
     */
    function format_bd_date($date, $includeTime = false)
    {
        if (! $date) {
            return '';
        }

        $dateTime = $date instanceof DateTime ? $date : new DateTime($date);
        $format = $includeTime ? 'd/m/Y h:i A' : 'd/m/Y';

        return $dateTime->format($format);
    }
}

if (! function_exists('parse_bd_date')) {
    /**
     * Parse DD/MM/YYYY format date to Y-m-d for database storage.
     *
     * @param  string  $date  Date in DD/MM/YYYY format
     * @return string|null Date in Y-m-d format
     */
    function parse_bd_date($date)
    {
        if (! $date) {
            return null;
        }

        // If already in Y-m-d format, return as is
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Parse DD/MM/YYYY format
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];

            return "{$year}-{$month}-{$day}";
        }

        return null;
    }
}

if (! function_exists('get_bd_divisions')) {
    /**
     * Get list of Bangladesh divisions.
     *
     * @return array
     */
    function get_bd_divisions()
    {
        return [
            'Dhaka',
            'Chittagong',
            'Rajshahi',
            'Khulna',
            'Barisal',
            'Sylhet',
            'Rangpur',
            'Mymensingh',
        ];
    }
}

if (! function_exists('validate_bd_nid')) {
    /**
     * Validate Bangladesh National ID.
     *
     * @param  string  $nid
     * @return bool
     */
    function validate_bd_nid($nid)
    {
        $nid = preg_replace('/[^0-9]/', '', $nid);

        return strlen($nid) === 10 || strlen($nid) === 17;
    }
}

if (! function_exists('validate_bd_phone')) {
    /**
     * Validate Bangladeshi mobile number.
     *
     * @param  string  $phone
     * @return bool
     */
    function validate_bd_phone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Check if it starts with 880 or 0 and has valid operator code
        if (preg_match('/^(?:880|0)?1[3-9]\d{8}$/', $phone)) {
            return true;
        }

        return false;
    }
}

if (! function_exists('get_bd_mobile_operators')) {
    /**
     * Get Bangladesh mobile operators.
     *
     * @return array
     */
    function get_bd_mobile_operators()
    {
        return [
            'Grameenphone' => ['017', '013'],
            'Banglalink' => ['019', '014'],
            'Robi' => ['018'],
            'Airtel' => ['016'],
            'Teletalk' => ['015'],
        ];
    }
}

if (! function_exists('detect_bd_operator')) {
    /**
     * Detect mobile operator from phone number.
     *
     * @param  string  $phone
     * @return string|null
     */
    function detect_bd_operator($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = preg_replace('/^(880|0)/', '', $phone);

        if (strlen($phone) >= 3) {
            $prefix = substr($phone, 0, 3);

            $operators = get_bd_mobile_operators();
            foreach ($operators as $name => $prefixes) {
                if (in_array($prefix, $prefixes)) {
                    return $name;
                }
            }
        }

        return null;
    }
}

if (! function_exists('format_bd_passport')) {
    /**
     * Format Bangladesh passport number.
     *
     * @param  string  $passport
     * @return string
     */
    function format_bd_passport($passport)
    {
        $passport = strtoupper(trim($passport));

        // Bangladesh passport format: A-Z + 7 digits
        if (preg_match('/^([A-Z])(\d{7})$/', $passport, $matches)) {
            return $matches[1].$matches[2];
        }

        return $passport;
    }
}

if (! function_exists('get_bd_districts')) {
    /**
     * Get list of Bangladesh districts (shortened list - full list in database).
     *
     * @return array
     */
    function get_bd_districts()
    {
        return [
            // Dhaka Division
            'Dhaka', 'Gazipur', 'Narayanganj', 'Tangail', 'Mymensingh', 'Kishoreganj', 'Netrokona', 'Manikganj', 'Munshiganj', 'Narsingdi', 'Rajbari', 'Faridpur', 'Gopalganj', 'Shariatpur', 'Madaripur',

            // Chittagong Division
            'Chittagong', "Cox's Bazar", 'Comilla', 'Brahmanbaria', 'Noakhali', 'Feni', 'Lakshmipur', 'Chandpur', 'Rangamati', 'Bandarban', 'Khagrachhari',

            // Rajshahi Division
            'Rajshahi', 'Bogura', 'Pabna', 'Sirajganj', 'Natore', 'Naogaon', 'Chapainawabganj', 'Joypurhat',

            // Khulna Division
            'Khulna', 'Bagerhat', 'Satkhira', 'Jessore', 'Jhenaidah', 'Magura', 'Narail', 'Chuadanga', 'Kushtia', 'Meherpur',

            // Barisal Division
            'Barisal', 'Patuakhali', 'Bhola', 'Pirojpur', 'Jhalokati', 'Barguna',

            // Sylhet Division
            'Sylhet', 'Moulvibazar', 'Habiganj', 'Sunamganj',

            // Rangpur Division
            'Rangpur', 'Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Panchagarh', 'Thakurgaon',
        ];
    }
}

if (! function_exists('get_popular_destinations_bd')) {
    /**
     * Get popular destinations for Bangladeshi travelers.
     *
     * @param  string|null  $purpose
     * @return array
     */
    function get_popular_destinations_bd($purpose = null)
    {
        $destinations = config('bangladesh.common_destinations');

        if ($purpose && isset($destinations[$purpose])) {
            return $destinations[$purpose];
        }

        return $destinations;
    }
}

if (! function_exists('format_bd_nid')) {
    /**
     * Format Bangladesh NID.
     *
     * @param  string  $nid
     * @return string
     */
    function format_bd_nid($nid)
    {
        $nid = preg_replace('/[^0-9]/', '', $nid);

        if (strlen($nid) === 10) {
            // Old format: XXXX-XXXXXX
            return substr($nid, 0, 4).'-'.substr($nid, 4);
        } elseif (strlen($nid) === 17) {
            // New format: XXXX XXXX XXXX XXXXX
            return substr($nid, 0, 4).' '.substr($nid, 4, 4).' '.substr($nid, 8, 4).' '.substr($nid, 12);
        }

        return $nid;
    }
}

if (! function_exists('convert_to_bengali_numerals')) {
    /**
     * Convert English numerals to Bengali numerals.
     *
     * @param  string|int  $number
     * @return string
     */
    function convert_to_bengali_numerals($number)
    {
        $bengali = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($english, $bengali, (string) $number);
    }
}

if (! function_exists('is_bd_weekend')) {
    /**
     * Check if date is Bangladesh weekend (Friday/Saturday).
     *
     * @param  string|DateTime  $date
     * @return bool
     */
    function is_bd_weekend($date = null)
    {
        $dateTime = $date ? ($date instanceof DateTime ? $date : new DateTime($date)) : new DateTime();
        $dayOfWeek = $dateTime->format('N'); // 1 (Monday) to 7 (Sunday)

        // Friday = 5, Saturday = 6
        return $dayOfWeek == 5 || $dayOfWeek == 6;
    }
}

if (! function_exists('get_bd_working_days')) {
    /**
     * Get Bangladesh working days (Sunday to Thursday).
     *
     * @return array
     */
    function get_bd_working_days()
    {
        return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
    }
}
