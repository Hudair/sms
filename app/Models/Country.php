<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed name
 */
class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = ['status'];


    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($item) {
            $item->status = true;
        });
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

}
