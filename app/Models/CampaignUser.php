<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignUser extends Model
{
    use HasFactory;

    protected $fillable = ['campaign_id', 'name', 'email', 'email_sent_status'];

    /**
     * Get the campaign that owns the user.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}

