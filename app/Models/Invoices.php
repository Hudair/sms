<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, string $uid)
 * @method static create(array $array)
 * @method static CurrentMonth()
 * @method static whereLike(string[] $array, mixed $search)
 * @method static offset(mixed $start)
 * @method static count()
 * @method static whereIn(string $string, mixed $ids)
 */
class Invoices extends Model
{

    /**
     * Invoice status
     */

    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PENDING = 'pending';

    /**
     * Invoice types
     */

    const TYPE_SENDERID = 'senderid';
    const TYPE_KEYWORD = 'keyword';
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_NUMBERS = 'number';

    /**
     * fillable value
     *
     * @var string[]
     */
    protected $fillable = [
            'uid',
            'user_id',
            'currency_id',
            'payment_method',
            'amount',
            'type',
            'description',
            'transaction_id',
            'status',
            'created_at',
            'updated_at',
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
     * get user
     *
     * @return BelongsTo
     *
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Currency
     *
     * @return BelongsTo
     *
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Payment method
     *
     * @return BelongsTo
     *
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethods::class, 'payment_method', 'id');
    }

    /**
     * get status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return '<div class="badge badge-success text-uppercase mr-1 mb-1"><span>'.__('locale.labels.paid').'</span></div>';
    }

    public function scopeCurrentMonth($query)
    {
        return $query->where('created_at', ">=", Carbon::now()->firstOfMonth());
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
