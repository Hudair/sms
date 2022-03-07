<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, $plan_id)
 * @method static create(int[] $server)
 * @method static insert(array[] $plan_sending_server)
 * @property mixed fitness
 * @property mixed plan_id
 * @property mixed is_primary
 */
class PlansSendingServer extends Model
{

    protected $fillable = ['fitness'];

    /**
     * Sending Servers
     *
     * @return BelongsTo
     */

    public function sendingServer(): BelongsTo
    {
        return $this->belongsTo(SendingServer::class);
    }


    /**
     * ShowFitness
     *
     * @return false|float
     */

    public function showFitness()
    {
        $sum = self::where('plan_id', $this->plan_id)
            ->sum('fitness');

        return round(($this->fitness / $sum) * 100);
    }

    /**
     * Check if primary
     *
     * @return mixed
     *
     */
    public function isPrimary()
    {
        return $this->is_primary;
    }

}
