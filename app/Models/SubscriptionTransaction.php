<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed        subscription_id
 * @property mixed|string status
 * @method static where(string $string, string $uid)
 */
class SubscriptionTransaction extends Model
{
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';

    const TYPE_SUBSCRIBE = 'subscribe';
    const TYPE_RENEW = 'renew';
    const TYPE_PLAN_CHANGE = 'plan_change';
    const TYPE_AUTO_CHARGE = 'auto_charge';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'title',
            'type',
            'status',
            'amount',
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
     *  subscriptions associations
     *
     * @return BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }


    /**
     * Change status to success.
     *
     * @var void
     */
    public function setSuccess()
    {
        $this->status = SubscriptionTransaction::STATUS_SUCCESS;
        $this->save();
    }

    /**
     * Change status to failed.
     *
     * @var void
     */
    public function setFailed()
    {
        $this->status = SubscriptionTransaction::STATUS_FAILED;
        $this->save();
    }


    /**
     * change status to pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status == subscriptionTransaction::STATUS_PENDING;
    }

    /**
     * Check if transaction is failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status == subscriptionTransaction::STATUS_FAILED;
    }

}
