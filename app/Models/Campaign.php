<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'campaign_title',
        'location',
        'date',
        'start_time',
        'end_time',
        'overview',
        'image',
        'status',
        'categories',
        'skill',
        'duration',
        'created_by',
        'updated_by',
    ];

    public function objectives()
    {
        return $this->hasMany(CampaignObjective::class, 'campaign_id');
    }

    public function campaignObjective()
    {
        return $this->belongsToMany(CampaignObjective::class, 'campaign_objectives', 'campaign_id','objective_title','id', 'id');
    }


}
