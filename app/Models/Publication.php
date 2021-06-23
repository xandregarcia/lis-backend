<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Publication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ordinance_id',
        'publisher_id',
        'first_from',
        'first_to',
        'second_from',
        'second_to',
        'third_from',
        'third_to',
    ];    

    /**
     * @param $value
     * @return false|string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('F j, Y h:i A');
    }

    public function publishers()
    {
        return $this->belongsTo(Publisher::class,'publisher_id','id');
    }

    public function ordinances()
    {
        return $this->belongsTo(Ordinance::class,'ordinance_id','id');
    }
}
