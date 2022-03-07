<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $array)
 * @method static where(string $string, mixed $id)
 * @method static insert(array $campaign_groups)
 */
class CampaignsList extends Model
{
    protected $fillable = [
            'campaign_id',
            'contact_list_id',
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

    /**
     * Associations with contact group
     *
     * @return BelongsTo
     */
    public function contactList(): BelongsTo
    {
        return $this->belongsTo(ContactGroups::class);
    }

}
