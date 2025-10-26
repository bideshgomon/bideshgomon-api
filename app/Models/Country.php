<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany;
class Country extends Model {
    use HasFactory;
    protected $fillable = ['name', 'code', 'is_active'];
    public function states(): HasMany { return $this->hasMany(State::class); }
    public function destinationTouristVisas(): HasMany { return $this->hasMany(TouristVisa::class, 'destination_country_id'); }
    public function travelInsurances(): HasMany { return $this->hasMany(TravelInsurance::class, 'destination_country_id'); }
}