<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $uid)
 * @method static create(array $array)
 * @method static whereIn(string $string, $contact_groups)
 */
class ContactGroupsOptoutKeywords extends Model
{
    use HasFactory;

    protected $table = 'contact_groups_optout_keywords';

    protected $fillable = [
            'contact_group',
            'keyword',
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

    public function ContactGroups()
    {
        return $this->hasOne(ContactGroups::class, 'id', 'contact_group');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

}
