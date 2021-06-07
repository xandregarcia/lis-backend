<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Appropriation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'appropriation_no',
        'for_referral_id',
        'title',
        'date_passed',
        'archive',
        'file',
        
    ];  
    
    protected $casts = [
        'archive' => 'boolean'
    ];

    /**
     * @param $value
     * @return false|string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('F j, Y h:i A');
    }

    public function bokals()
    {
        return $this->belongsToMany(Bokal::class)->withPivot('author', 'co_author');
    }

    public function for_referral()
    {
        // return $this->belongsTo(Group::class,'group_id','id');
        return $this->belongsTo(ForReferral::class);
    }
}
