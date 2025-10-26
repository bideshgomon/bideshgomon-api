<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; use Illuminate\Database\Eloquent\Relations\HasMany;
class Flight extends Model {
    use HasFactory;
    protected $fillable = ['airline_id', 'origin_airport_id', 'destination_airport_id', 'flight_number', 'departure_at', 'arrival_at', 'price', 'available_seats'];
    protected $casts = ['departure_at' => 'datetime', 'arrival_at' => 'datetime', 'price' => 'decimal:2'];
    public function airline(): BelongsTo { return $this->belongsTo(Airline::class); }
    public function originAirport(): BelongsTo { return $this->belongsTo(Airport::class, 'origin_airport_id'); }
    public function destinationAirport(): BelongsTo { return $this->belongsTo(Airport::class, 'destination_airport_id'); }
    public function bookings(): HasMany { return $this->hasMany(FlightBooking::class); }
}