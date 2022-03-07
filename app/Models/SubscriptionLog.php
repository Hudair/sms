<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed data
 * @method static where(string $string, string $uid)
 */
class SubscriptionLog extends Model
{

    const TYPE_SUBSCRIBE = 'subscribe';
    const TYPE_SUBSCRIBED = 'subscribed';
    const TYPE_PAID = 'paid';
    const TYPE_CLAIMED = 'claimed';
    const TYPE_UNCLAIMED = 'unclaimed';
    const TYPE_STARTED = 'started';
    const TYPE_EXPIRED = 'expired';
    const TYPE_RENEWED = 'renewed';
    const TYPE_RENEW = 'renew';
    const TYPE_RENEW_FAILED = 'renew_failed';
    const TYPE_PLAN_CHANGE = 'plan_change';
    const TYPE_PLAN_CHANGED = 'plan_changed';
    const TYPE_PLAN_CHANG_FAILED = 'plan_change_failed';
    const TYPE_CANCELLED = 'cancelled';
    const TYPE_CANCELLED_NOW = 'cancelled_now';
    const TYPE_ADMIN_APPROVED = 'admin_approved';
    const TYPE_ADMIN_REJECTED = 'admin_rejected';
    const TYPE_ADMIN_RENEW_APPROVED = 'admin_renew_approved';
    const TYPE_ADMIN_PLAN_CHANGE_APPROVED = 'admin_plan_change_approved';
    const TYPE_ADMIN_RENEW_REJECTED = 'admin_renew_rejected';
    const TYPE_ADMIN_PLAN_CHANGE_REJECTED = 'admin_plan_change_rejected';
    const TYPE_ADMIN_CANCELLED = 'admin_cancelled';
    const TYPE_ADMIN_CANCELLED_NOW = 'admin_cancelled_now';
    const TYPE_ADMIN_RESUMED = 'admin_resumed';
    const TYPE_ADMIN_PLAN_ASSIGNED = 'admin_plan_assigned';
    const TYPE_RESUMED = 'resumed';
    const TYPE_ERROR = 'error';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'type', 'data',
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
     * subscription Associations
     *
     * @return BelongsTo
     *
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * transaction Associations
     *
     * @return BelongsTo
     *
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(SubscriptionTransaction::class, 'subscription_transactions');
    }

    /**
     * get data
     *
     * @return mixed
     */
    public function getData()
    {
        if (!$this->data) {
            return json_decode('{}', true);
        }

        return json_decode($this->data, true);
    }

    /**
     * update data
     *
     * @param $data
     */
    public function updateData($data)
    {
        $data = (object) array_merge((array) $this->getData(), $data);
        $this->data = json_encode($data);

        $this->save();
    }
}
