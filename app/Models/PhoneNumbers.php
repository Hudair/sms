<?php

namespace App\Models;

use App\Library\Tool;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static whereLike(string[] $array, $search)
 * @method static where(string $string, string $uid)
 * @method static select(string $string)
 * @method static count()
 * @method static offset(mixed $start)
 * @method static cursor()
 * @method static create(array $number)
 * @property mixed name
 */
class PhoneNumbers extends Model
{

    /**
     * The attributes for assign table
     *
     * @var string
     */

    protected $table = 'phone_numbers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
            'user_id',
            'number',
            'status',
            'capabilities',
            'price',
            'billing_cycle',
            'frequency_amount',
            'frequency_unit',
            'validity_date',
            'currency_id',
            'transaction_id',
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
     * @var string[]
     */
    protected $casts = [
            'capabilities'  => 'object',
            'validity_date' => 'date',
    ];

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
     * get all plans
     *
     * @return mixed
     */

    public static function getAll()
    {
        return self::select('*');
    }

    /**
     * get numbers capabilities
     *
     * @return string
     */
    public function getCapabilities(): string
    {
        $return_data  = '';
        $capabilities = json_decode($this->capabilities, true);
        foreach ($capabilities as $capability) {
            if ($capability == 'sms') {
                $return_data .= '<div class="badge badge-primary text-uppercase mr-1 mb-1"><span>'.__('locale.labels.sms').'</span></div>';
            }
            if ($capability == 'voice') {
                $return_data .= '<div class="badge badge-success text-uppercase mr-1 mb-1"><span>'.__('locale.labels.voice').'</span></div>';
            }
            if ($capability == 'mms') {
                $return_data .= '<div class="badge badge-info text-uppercase mr-1 mb-1"><span>'.__('locale.labels.mms').'</span></div>';
            }
            if ($capability == 'whatsapp') {
                $return_data .= '<div class="badge badge-warning text-uppercase mb-1"><span>'.__('locale.labels.whatsapp').'</span></div>';
            }
        }

        return $return_data;
    }

    /**
     * Frequency time unit options.
     *
     * @return array
     */
    public static function timeUnitOptions(): array
    {
        return [
                ['value' => 'day', 'text' => 'day'],
                ['value' => 'week', 'text' => 'week'],
                ['value' => 'month', 'text' => 'month'],
                ['value' => 'year', 'text' => 'year'],
        ];
    }

    /**
     * Check if phone number validity time is unlimited.
     *
     * @return bool
     */
    public function isTimeUnlimited(): bool
    {
        return $this->frequency_unit == 'unlimited';
    }


    /**
     * Get billing recurs available values.
     *
     * @return array
     */
    public static function billingCycleValues(): array
    {
        return [
                'daily'   => [
                        'frequency_amount' => 1,
                        'frequency_unit'   => 'day',
                ],
                'monthly' => [
                        'frequency_amount' => 1,
                        'frequency_unit'   => 'month',
                ],
                'yearly'  => [
                        'frequency_amount' => 1,
                        'frequency_unit'   => 'year',
                ],
        ];
    }

    /**
     * Display frequency time
     *
     * @return array|Application|Translator|string|null
     */
    public function displayFrequencyTime()
    {
        // unlimited
        if ($this->isTimeUnlimited()) {
            return __('locale.labels.unlimited');
        }

        return $this->frequency_amount.' '.Tool::getPluralParse($this->frequency_unit, $this->frequency_amount);
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
