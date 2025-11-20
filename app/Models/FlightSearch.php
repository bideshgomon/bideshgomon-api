<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_type',
        'origin_airport_code',
        'destination_airport_code',
        'travel_date',
        'return_date',
        'multi_city_segments',
        'passengers_count',
        'flight_class',
        'ip_address',
        'user_agent',
        'search_count',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
        'multi_city_segments' => 'array',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get popular routes based on search frequency
     */
    public static function getPopularRoutes($limit = 10)
    {
        return static::selectRaw('
                origin_airport_code,
                destination_airport_code,
                SUM(search_count) as total_searches,
                COUNT(*) as search_frequency,
                MAX(travel_date) as latest_search
            ')
            ->where('trip_type', '!=', 'multi_city')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('origin_airport_code', 'destination_airport_code')
            ->orderByDesc('total_searches')
            ->limit($limit)
            ->get();
    }

    /**
     * Get trending routes (searched recently and frequently)
     */
    public static function getTrendingRoutes($limit = 6)
    {
        return static::selectRaw('
                origin_airport_code,
                destination_airport_code,
                SUM(search_count) as total_searches,
                COUNT(*) as search_frequency
            ')
            ->where('trip_type', '!=', 'multi_city')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('origin_airport_code', 'destination_airport_code')
            ->orderByDesc('search_frequency')
            ->orderByDesc('total_searches')
            ->limit($limit)
            ->get();
    }
}
