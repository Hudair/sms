<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlansCoverageCountries extends Model
{
    /**
     * Country
     *
     * @return BelongsTo
     */

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Country
     *
     * @return BelongsTo
     */

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

}
