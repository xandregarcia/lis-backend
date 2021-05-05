<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CommunicationStatus extends Model
{
    use HasFactory;

    public $table = 'communication_status';

    protected $fillable = [
        'for_referral_id',
        'endorsement',
        'committee_report',
        'second_reading',
        'third_reading',
        'passed',
        'approved',
        'furnished',
        'published',
        'type'
    ];

    protected $casts = [
        'endorsement' => 'boolean',
        'committee_report' => 'boolean',
        'second_reading' => 'boolean',
        'third_reading' => 'boolean',
        'approved' => 'boolean',
        'furnished' => 'boolean',
        'published' => 'boolean'
    ];

    public function for_referrals()
    {
        return $this->belongsTo(ForReferral::class,'for_referral_id','id');
    }
}
