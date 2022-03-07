<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $array)
 */
class CampaignsSendingServer extends Model
{
    protected $fillable = [
            'campaign_id',
            'sending_server_id',
            'fitness',
    ];

    /**
     * Associations with campaign
     *
     * @return BelongsTo
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaigns::class);
    }

}
