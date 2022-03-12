<?php

namespace App\Models;

use App\Library\QuotaTrackerFile;
use App\Library\Tool;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


/**
 * @method static status(bool $true)
 * @method static where(string $string, string $uid)
 * @method static cursor()
 * @method static whereIn(string $string, array $sending_servers_ids)
 * @method static find(int|mixed|string $id)
 * @method static create(array $server)
 * @property mixed quota_value
 * @property mixed quota_base
 * @property mixed quota_unit
 * @property mixed quotaTracker
 * @property mixed created_at
 * @property mixed uid
 * @property mixed name
 */
class SendingServer extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * @note important! consider updating the $fillable variable, it will affect some other methods
     */
    protected $fillable = [
            'name',
            'user_id',
            'settings',
            'api_link',
            'port',
            'username',
            'password',
            'route',
            'sms_type',
            'account_sid',
            'auth_id',
            'auth_token',
            'access_key',
            'access_token',
            'secret_access',
            'api_key',
            'api_secret',
            'user_token',
            'project_id',
            'api_token',
            'auth_key',
            'device_id',
            'region',
            'application_id',
            'c1',
            'c2',
            'c3',
            'c4',
            'c5',
            'c6',
            'c7',
            'type',
            'sms_per_request',
            'quota_value',
            'quota_base',
            'quota_unit',
            'custom_order',
            'schedule',
            'custom',
            'status',
            'two_way',
            'plain',
            'mms',
            'voice',
            'whatsapp',
            'source_addr_ton',
            'source_addr_npi',
            'dest_addr_ton',
            'dest_addr_npi',
            'success_keyword',
    ];

    /**
     *  The attributes that should be cast.
     *
     * @var string[]
     */
    protected $casts = [
            'schedule'        => 'boolean',
            'custom'          => 'boolean',
            'status'          => 'boolean',
            'two_way'         => 'boolean',
            'plain'           => 'boolean',
            'mms'             => 'boolean',
            'voice'           => 'boolean',
            'whatsapp'        => 'boolean',
            'quota_value'     => 'integer',
            'quota_base'      => 'integer',
            'sms_per_request' => 'integer',
    ];


    /**
     * Plans
     *
     * @return BelongsToMany
     *
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plans_sending_servers');
    }

    /**
     * Customer
     *
     * @return BelongsTo
     */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Admin
     *
     * @return BelongsTo
     *
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * get custom sending server
     *
     * @return BelongsTo
     *
     */
    public function customSendingServer(): BelongsTo
    {
        return $this->belongsTo(CustomSendingServer::class, 'id', 'server_id');
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
     * Active status scope
     *
     * @param        $query
     * @param  bool  $status
     *
     * @return mixed
     */
    public function scopeStatus($query, bool $status)
    {
        return $query->where('status', $status);
    }


    /**
     * Get sending limit types.
     *
     * @return array
     */
    public static function sendingLimitValues(): array
    {
        return [
                'unlimited'      => [
                        'quota_value' => -1,
                        'quota_base'  => -1,
                        'quota_unit'  => 'day',
                ],
                '100_per_minute' => [
                        'quota_value' => 100,
                        'quota_base'  => 1,
                        'quota_unit'  => 'minute',
                ],
                '1000_per_hour'  => [
                        'quota_value' => 1000,
                        'quota_base'  => 1,
                        'quota_unit'  => 'hour',
                ],
                '10000_per_day'  => [
                        'quota_value' => 10000,
                        'quota_base'  => 1,
                        'quota_unit'  => 'day',
                ],
        ];
    }


    /**
     * Get sending server's quota.
     *
     * @return string
     */
    public function getSendingQuota(): string
    {
        return $this->quota_value;
    }

    /**
     * Get sending server's sending quota.
     *
     * @return string
     * @throws Exception
     */
    public function getSendingQuotaUsage(): string
    {
        $tracker = $this->getQuotaTracker();

        return $tracker->getUsage();
    }


    /**
     * Quota display.
     *
     * @return string
     */
    public function displayQuota(): string
    {
        if ($this->quota_value == -1) {
            return __('locale.plans.unlimited');
        }

        return $this->quota_value.'/'.$this->quota_base.' '.__('locale.labels.'.Tool::getPluralParse($this->quota_unit, $this->quota_base));
    }

    /**
     * Quota display.
     *
     * @return string
     */
    public function displayQuotaHtml(): string
    {
        if ($this->quota_value == -1) {
            return __('locale.plans.unlimited');
        }

        return '<code>'.$this->quota_value.'</code>/<code>'.$this->quota_base.' '.__('locale.labels.'.Tool::getPluralParse($this->quota_unit, $this->quota_base)).'</code>';
    }


    /**
     * Get sending server's QuotaTracker.
     *
     * @return mixed
     * @throws Exception
     */
    public function getQuotaTracker()
    {
        if ( ! $this->quotaTracker) {
            $this->initQuotaTracker();
        }

        return $this->quotaTracker;
    }

    /**
     * Initialize the quota tracker.
     *
     * @throws Exception
     */
    public function initQuotaTracker()
    {
        $this->quotaTracker = new QuotaTrackerFile($this->getSendingQuotaLockFile(), ['start' => $this->created_at->timestamp, 'max' => -1], [$this->getQuotaIntervalString() => $this->getSendingQuota()]);
        $this->quotaTracker->cleanupSeries();
        // @note: in case of multi-process, the following command must be issued manually
        //     $this->renewQuotaTracker();
    }


    /**
     * Clean up the quota tracking files to prevent it from growing too large.
     *
     * @throws Exception
     */
    public function cleanupQuotaTracker()
    {
        // @todo: hard-coded for 1 month
        $this->getQuotaTracker()->cleanupSeries(null, '1 month');
    }

    /**
     * Get sending quota lock file.
     *
     * @return string file path
     */
    public function getSendingQuotaLockFile(): string
    {
        return storage_path("app/server/quota/{$this->uid}");
    }

    /**
     * Get quota starting time.
     *
     * @return string
     */
    public function getQuotaIntervalString(): string
    {
        return "{$this->quota_base} {$this->quota_unit}";
    }

    /**
     * Get quota starting time.
     *
     * @return string
     */
    public function getQuotaStartingTime(): string
    {
        return "{$this->getQuotaIntervalString()} ago";
    }


    /**
     * Increment quota usage.
     *
     * @param  Carbon|null  $timePoint
     *
     * @return mixed
     * @throws Exception
     */
    public function countUsage(Carbon $timePoint = null)
    {
        return $this->getQuotaTracker($timePoint)->add();
    }

    /**
     * Check if user has used up all quota allocated.
     *
     * @return string
     * @throws Exception
     */
    public function overQuota()
    {
        return ! $this->getQuotaTracker()->check();
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
