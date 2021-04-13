<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

<<<<<<< Updated upstream
class ThirdReading extends Model
{
    use HasFactory;
=======
use Carbon\Carbon;

class ThirdReading extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'for_referral_id',
        'date_received',
        'agenda_date',
        'file'
    ];    

    /**
     * @param $value
     * @return false|string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('F j, Y h:i A');
    }

    public function for_referral()
    {
        return $this->belongsTo(ForReferral::class);
    }
>>>>>>> Stashed changes
}
