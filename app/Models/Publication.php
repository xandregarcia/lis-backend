<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'first_publication',
        'second_publication',
        'third_publication',
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
        return $this->belongsTo(Publisher::class);
    }

    public function ordinances()
    {
        return $this->belongsTo(Ordinance::class);
    }
}
