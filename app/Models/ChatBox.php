<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $uid)
 * @method static create(array $array)
 */
class ChatBox extends Model
{
    protected $fillable = [
            'user_id',
            'from',
            'to',
            'notification',
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

        static::deleted(function ($item) {
            if ( ! is_null($item->boxMessages)) {
                $item->boxMessages->delete();
            }
        });
    }


    public function boxMessages()
    {
        $this->belongsTo(ChatBoxMessage::class, 'box_id', 'id');
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
