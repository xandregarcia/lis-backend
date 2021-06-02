<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CommunicationStatus extends Model
{
    use HasFactory;

    public $table = 'communication_status';

    protected $fillable = [
        'approve',
        'endorsement',
        'committee_report',
        'second_reading',
        'third_reading',
        'type'
    ];

    protected $casts = [
        'approve' => 'boolean',
        'endorsement' => 'boolean',
        'committee_report' => 'boolean',
        'second_reading' => 'boolean',
        'third_reading' => 'boolean'
    ];

    public function for_referrals()
    {
        return $this->belongsTo(ForReferral::class,'for_referral_id','id');
    }
}
