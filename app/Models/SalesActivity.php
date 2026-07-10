<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'sales_name',
        'destination_type',
        'destination_name',
        'team_involved',
        'activity_type',
        'activities',
        'report',
    ];
}
