<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class ForReferral extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'receiving_date',
        'category_id',
        'origin_id',
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

    public function category()
    {
        // return $this->belongsTo(Group::class,'group_id','id');
        return $this->belongsTo(Category::class);
    }
    
    public function origin()
    {
        // return $this->belongsTo(Group::class,'group_id','id');
        return $this->belongsTo(Origin::class);
    }

    public function committees()
    {
        return $this->belongsToMany(Committee::class)->withPivot('lead_committee', 'joint_committee');
    }

    public function comm_status()
    {
        return $this->hasOne(CommunicationStatus::class);
    }

}
