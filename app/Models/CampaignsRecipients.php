<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static insert(array $numbers)
 * @method static where(string $string, $id)
 * @method static create(array $array)
 */
class CampaignsRecipients extends Model
{

    protected $fillable = [
            'campaign_id',
            'recipient',
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
