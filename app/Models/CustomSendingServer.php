<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, string $uid)
 * @method make(array $only)
 */
class CustomSendingServer extends Model
{
    /**
     * @var string
     */
    protected $table = 'custom_sending_servers';

    /**
     * @var string[]
     */
    protected $fillable = ['uid', 'server_id', 'http_request_method', 'json_encoded_post', 'content_type', 'content_type_accept', 'character_encoding', 'ssl_certificate_verification', 'authorization', 'multi_sms_delimiter', 'username_param', 'username_value', 'password_param', 'password_value', 'password_status', 'action_param', 'action_value', 'action_status', 'source_param', 'source_value', 'source_status', 'destination_param', 'message_param', 'unicode_param', 'unicode_value', 'unicode_status', 'route_param', 'route_value', 'route_status', 'language_param', 'language_value', 'language_status', 'custom_one_param', 'custom_one_value', 'custom_one_status', 'custom_two_param', 'custom_two_value', 'custom_two_status', 'custom_three_param', 'custom_three_value', 'custom_three_status',];

    /**
     * Sending Server
     *
     * @return BelongsTo
     */

    public function sending_server(): BelongsTo
    {
        return $this->belongsTo(SendingServer::class);
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
        });
    }

}
