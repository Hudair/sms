<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $uid)
 * @method static truncate()
 * @property mixed name
 */
class EmailTemplates extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'name', 'slug', 'subject', 'content', 'status',
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

    public function template_tags($template): array
    {
        $tags = [];
        switch ($template) {

            case 'customer_registration':
                $tags['app_name']      = 'required';
                $tags['first_name']    = 'optional';
                $tags['last_name']     = 'optional';
                $tags['login_url']     = 'required';
                $tags['email_address'] = 'required';
                $tags['password']      = 'optional';
                break;

            case 'registration_verification':
                $tags['app_name']         = 'required';
                $tags['first_name']       = 'optional';
                $tags['last_name']        = 'optional';
                $tags['verification_url'] = 'required';
                break;

            case 'password_reset':
                $tags['app_name']      = 'optional';
                $tags['first_name']    = 'optional';
                $tags['last_name']     = 'optional';
                $tags['login_url']     = 'required';
                $tags['email_address'] = 'required';
                $tags['password']      = 'required';
                break;


            case 'forgot_password':
                $tags['app_name']             = 'optional';
                $tags['first_name']           = 'optional';
                $tags['last_name']            = 'optional';
                $tags['forgot_password_link'] = 'required';
                break;

            case 'login_notification':
                $tags['app_name']   = 'required';
                $tags['time']       = 'required';
                $tags['ip_address'] = 'required';
                break;

            case 'registration_notification':
                $tags['app_name']             = 'optional';
                $tags['first_name']           = 'optional';
                $tags['last_name']            = 'optional';
                $tags['customer_profile_url'] = 'required';
                break;

            case 'sender_id_notification':
                $tags['app_name']      = 'optional';
                $tags['sender_id']     = 'required';
                $tags['sender_id_url'] = 'required';
                break;

            case 'subscription_notification':
                $tags['app_name']    = 'optional';
                $tags['invoice_url'] = 'required';
                break;

            case 'keyword_purchase_notification':
                $tags['app_name']    = 'optional';
                $tags['keyword_url'] = 'required';
                break;

            case 'number_purchase_notification':
                $tags['app_name']   = 'optional';
                $tags['number_url'] = 'required';
                break;

//            case 'ticket_customer':
//                $tags['app_name']       = 'optional';
//                $tags['first_name']     = 'optional';
//                $tags['last_name']      = 'optional';
//                $tags['ticket_url']     = 'required';
//                $tags['ticket_id']      = 'optional';
//                $tags['ticket_subject'] = 'optional';
//                $tags['message']        = 'optional';
//                $tags['create_by']      = 'optional';
//                break;
//
//            case 'reply_ticket':
//                $tags['app_name']       = 'optional';
//                $tags['first_name']     = 'optional';
//                $tags['last_name']      = 'optional';
//                $tags['ticket_url']     = 'required';
//                $tags['ticket_id']      = 'required';
//                $tags['ticket_subject'] = 'optional';
//                $tags['message']        = 'optional';
//                $tags['reply_by']       = 'optional';
//                break;
//
//            case 'ticket_admin':
//                $tags['app_name']        = 'optional';
//                $tags['department_name'] = 'optional';
//                $tags['ticket_url']      = 'required';
//                $tags['ticket_id']       = 'required';
//                $tags['ticket_subject']  = 'optional';
//                $tags['message']         = 'optional';
//                $tags['create_by']       = 'optional';
//                break;
        }

        return $tags;
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
