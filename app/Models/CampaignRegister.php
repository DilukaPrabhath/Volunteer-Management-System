<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'registered_user_id',
        'campaign_id',
        'description',
        'week_days_start_time',
        'week_days_end_time',
        'week_end_days_start_time',
        'week_end_days_end_time',
        'status',
    ];
}
