<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, $uid)
 * @method static select(string $string)
 * @method static count()
 * @method static offset(mixed $start)
 * @method static whereLike(string[] $array, mixed $search)
 * @method static cursor()
 * @method static create(array $data)
 * @property false|mixed status
 * @property mixed       name
 * @property mixed       code
 */
class Currency extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'name',
            'user_id',
            'code',
            'format',
            'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
            'status' => 'boolean',
    ];

    /**
     * Find item by uid.
     *
     * @param $uid
     *
     * @return object
     */
    public static function findByUid($uid): object
    {
        return self::where('uid', $uid)->first();
    }


    /**
     * Associations.
     *
     * @return BelongsTo
     *
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo('App\Models\Admin');
    }

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

            // uppercase for currency code
            $item->code = strtoupper($item->code);
        });

        static::updating(function ($item) {
            // uppercase for currency code
            $item->code = strtoupper($item->code);
        });
    }

    /**
     * Get all items.
     *
     * @return mixed
     */
    public static function getAll()
    {
        return Currency::select('*');
    }


    /**
     * Disable customer.
     *
     * @return boolean
     */
    public function disable(): bool
    {
        $this->status = false;

        return $this->save();
    }

    /**
     * Enable customer.
     *
     * @return boolean
     */
    public function enable(): bool
    {
        $this->status = true;

        return $this->save();
    }

    /**
     * Display currency name.
     *
     * @return string
     */
    public function displayName(): string
    {
        return $this->name." (".$this->code.")";
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

}
