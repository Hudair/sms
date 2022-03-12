<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $array)
 * @method static whereLike(string[] $array, mixed $search)
 * @method static offset(mixed $start)
 * @method static count()
 * @method static where(string $string, mixed $id)
 */
class PlansCoverageCountries extends Model
{

    protected $fillable = ['country_id', 'plan_id', 'options','status'];

    protected $casts = [
            'status' => 'boolean',
    ];

    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($item) {
            // Create new uid
            $uid = uniqid();
            while (self::where('uid', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
    }


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

    /**
     * get route key by uid
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uid';
    }


}
