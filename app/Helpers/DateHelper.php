<?php

if (!function_exists('format_date_dd_mm_yyyy')) {
    /**
     * Format date to DD MM YYYY format (27 11 2025 or 27 Nov 2025)
     *
     * @param  string|\Carbon\Carbon|null  $date
     * @param  string  $separator  Separator between day, month, year (' ', '-', '/')
     * @param  bool  $monthName  Use month name instead of number
     * @return string
     */
    function format_date_dd_mm_yyyy($date, string $separator = ' ', bool $monthName = false): string
    {
        if (empty($date)) {
            return '';
        }

        try {
            $carbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
            
            if ($monthName) {
                // Format: 27 Nov 2025
                return $carbon->format('d' . $separator . 'M' . $separator . 'Y');
            }
            
            // Format: 27 11 2025
            return $carbon->format('d' . $separator . 'm' . $separator . 'Y');
            
        } catch (\Exception $e) {
            return '';
        }
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date to DD MM YYYY format (alias)
     *
     * @param  string|\Carbon\Carbon|null  $date
     * @param  bool  $monthName
     * @return string
     */
    function format_date($date, bool $monthName = false): string
    {
        return format_date_dd_mm_yyyy($date, ' ', $monthName);
    }
}

if (!function_exists('format_datetime_dd_mm_yyyy')) {
    /**
     * Format datetime to DD MM YYYY HH:MM format
     *
     * @param  string|\Carbon\Carbon|null  $datetime
     * @param  bool  $monthName
     * @return string
     */
    function format_datetime_dd_mm_yyyy($datetime, bool $monthName = false): string
    {
        if (empty($datetime)) {
            return '';
        }

        try {
            $carbon = $datetime instanceof \Carbon\Carbon ? $datetime : \Carbon\Carbon::parse($datetime);
            
            if ($monthName) {
                // Format: 27 Nov 2025 14:30
                return $carbon->format('d M Y H:i');
            }
            
            // Format: 27 11 2025 14:30
            return $carbon->format('d m Y H:i');
            
        } catch (\Exception $e) {
            return '';
        }
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime (alias)
     *
     * @param  string|\Carbon\Carbon|null  $datetime
     * @param  bool  $monthName
     * @return string
     */
    function format_datetime($datetime, bool $monthName = false): string
    {
        return format_datetime_dd_mm_yyyy($datetime, $monthName);
    }
}

if (!function_exists('format_time')) {
    /**
     * Format time to HH:MM AM/PM
     *
     * @param  string|\Carbon\Carbon|null  $datetime
     * @return string
     */
    function format_time($datetime): string
    {
        if (empty($datetime)) {
            return '';
        }

        try {
            $carbon = $datetime instanceof \Carbon\Carbon ? $datetime : \Carbon\Carbon::parse($datetime);
            return $carbon->format('h:i A'); // 02:30 PM
        } catch (\Exception $e) {
            return '';
        }
    }
}

if (!function_exists('parse_dd_mm_yyyy')) {
    /**
     * Parse DD MM YYYY format to Carbon instance
     *
     * @param  string  $date  Date in format like "27 11 2025" or "27-11-2025" or "27/11/2025"
     * @return \Carbon\Carbon|null
     */
    function parse_dd_mm_yyyy(string $date): ?\Carbon\Carbon
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle various separators
            $date = preg_replace('/[\/\-\s]+/', '-', $date);
            
            // Split by separator
            $parts = explode('-', $date);
            
            if (count($parts) === 3) {
                [$day, $month, $year] = $parts;
                return \Carbon\Carbon::createFromFormat('d-m-Y', "{$day}-{$month}-{$year}");
            }
            
            // Fallback to Carbon's parser
            return \Carbon\Carbon::parse($date);
            
        } catch (\Exception $e) {
            return null;
        }
    }
}
