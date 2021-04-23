<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationStatus extends Model
{
    use HasFactory;

    public $table = 'communication_status';

    protected $fillable = [
        "endorsement",
        "committee_report",
        "second_reading",
        "third_reading"
    ];

    protected $casts = [
        "endorsement" => "boolean",
        "committee_report" => "boolean",
        "second_reading" => "boolean",
        "third_reading" => "boolean"
    ];
}
