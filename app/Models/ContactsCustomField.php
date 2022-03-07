<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $uid)
 * @method static create(array $array)
 */
class ContactsCustomField extends Model
{
    use HasFactory;


    protected $table = 'contacts_custom_field';

    protected $fillable = [
            'contact_id',
            'name',
            'tag',
            'type',
            'required',
            'value',
    ];


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
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

}
