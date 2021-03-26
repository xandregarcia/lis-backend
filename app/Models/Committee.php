<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Committee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'chairman',
        'vice_chairman',
        'members'
    ];    

    /**
     * @param $value
     * @return false|string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('F j, Y h:i A');
    } 

    public function bokal()
    {
        return $this->belongsTo(Bokal::class,'chairman','id');
        // return $this->hasMany(Bokal::class);
    }
    public function bokal2()
    {
        return $this->belongsTo(Bokal::class,'vice_chairman','id');
        // return $this->hasMany(Bokal::class);
    }

    public function bokals3()
    {
        return $this->belongsTo(Bokal::class,'members','id');
    }
}