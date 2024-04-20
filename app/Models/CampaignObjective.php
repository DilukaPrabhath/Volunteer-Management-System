<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignObjective extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'objective_title',
        'campaign_id',
        'status',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}
