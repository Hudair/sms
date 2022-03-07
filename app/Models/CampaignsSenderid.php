<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $array)
 * @method static where(string $string, mixed $id)
 * @method static find(mixed $int)
 */
class CampaignsSenderid extends Model
{
    protected $fillable = [
            'campaign_id',
            'sender_id',
            'originator',
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
