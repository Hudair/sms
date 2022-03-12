<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed name
 * @method static create(array $array)
 * @method static cursor()
 * @method static where(string $string, string $uid)
 * @method static count()
 * @method static offset($start)
 * @method static whereLike(string[] $array, $search)
 * @method static find(mixed $country_code)
 */
class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = ['name', 'iso_code', 'country_code', 'status'];


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
            $item->uid    = $uid;
            $item->status = true;
        });
    }

    /**
     * @var array
     */
    protected $casts = [
            'status' => 'boolean',
    ];

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
