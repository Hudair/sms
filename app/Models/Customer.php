<?php

namespace App\Models;

use App\Library\QuotaTrackerFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

/**
 * @method static where(string $string, string $uid)
 * @method static create(array $array)
 * @method truncate()
 * @method static count()
 * @method static thisYear()
 * @property mixed user
 * @property mixed subscription
 * @property mixed notifications
 */
class Customer extends Model
{
    use Notifiable;

    protected $quotaTracker;

    protected $table = 'customers';
    /**
     * @var mixed
     */
    private $can_edit;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
            'user_id',
            'contact_id',
            'parent',
            'company',
            'website',
            'address',
            'city',
            'postcode',
            'financial_address',
            'financial_city',
            'financial_postcode',
            'tax_number',
            'state',
            'country',
            'phone',
            'notifications',
            'permissions',
            'created_at',
            'updated_at',
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
            if (config('app.env') != 'demo') {
                $item->permissions = self::customerPermissions();
            }
        });
    }

    /**
     * Customer email.
     *
     * @return string
     */
    public function email(): string
    {
        return is_object($this->user) ? $this->user->email : '';
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
     * Associations
     *
     * @return HasOne
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'user_id', 'user_id')->orderBy('created_at', 'desc');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contacts::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function lists(): HasMany
    {
        return $this->hasMany(ContactGroups::class, 'customer_id', 'user_id')->orderBy('created_at', 'desc');
    }


    /**
     * Get active subscription.
     */
    public function activeSubscription()
    {
        return (is_object($this->subscription) && $this->subscription->active()) ? $this->subscription : null;
    }

    /**
     * Get customer options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        if (is_object($this->activeSubscription())) {
            // Find plan
            return $this->activeSubscription()->plan->getOptions();
        } else {
            return [];
        }
    }

    /**
     * Get customer option.
     *
     * @param $name
     *
     * @return string
     */
    public function getOption($name): ?string
    {
        $options = $this->getOptions();

        return isset($options[$name]) ? $options[$name] : null;
    }

    /**
     * Get max list quota.
     *
     * @return string
     */
    public function maxLists(): ?string
    {
        $count = $this->getOption('list_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Count customer lists.
     *
     * @return number
     */
    public function listsCount()
    {
        return $this->lists()->count();
    }

    /**
     * Calculate list usage.
     *
     * @return int
     */
    public function listsUsage(): int
    {
        $max   = $this->maxLists();
        $count = $this->listsCount();

        if ($max == '∞') {
            return 0;
        }

        if ($max == 0) {
            return 0;
        }

        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Calculate list usage.
     *
     * @return int
     */
    public function subscriberUsage(): int
    {
        $max   = $this->maxSubscribers();
        $count = $this->subscriberCounts();

        if ($max == '∞') {
            return 0;
        }

        if ($max == 0) {
            return 0;
        }

        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Display calculate list usage.
     *
     * @return array|Application|Translator|string|null
     */
    public function displayListsUsage()
    {
        if ($this->maxLists() == '∞') {
            return '∞';
        }

        return $this->listsUsage().'%';
    }

    /**
     * get total subscriber
     *
     * @return int
     */
    public function subscriberCounts(): int
    {
        return $this->hasMany(Contacts::class, 'customer_id', 'user_id')->count();
    }

    /**
     * get blacklist count
     *
     * @return int
     */
    public function blacklistCounts(): int
    {
        return $this->hasMany(Blacklists::class, 'user_id', 'user_id')->count();
    }

    /**
     * get sms template count
     *
     * @return int
     */
    public function smsTemplateCounts(): int
    {
        return $this->hasMany(Templates::class, 'user_id', 'user_id')->count();
    }

    /**
     * Get subscriber quota.
     *
     * @return string|null
     */
    public function maxSubscribers(): ?string
    {
        $count = $this->getOption('subscriber_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }


    public function scopeThisYear($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->firstOfYear());
    }

    /**
     * Display calculate list usage.
     *
     * @return array|Application|Translator|string|null
     */
    public function displaySubscribersUsage()
    {
        if ($this->maxSubscribers() == '∞') {
            return '∞';
        }

        return $this->subscriberUsage().'%';
    }


    public function currentPlanName()
    {
        return is_object($this->activeSubscription()) ? $this->activeSubscription()->plan->name : __('locale.subscription.no_active_subscription');
    }


    /**
     * Get notifications.
     *
     * @return mixed
     */
    public function getNotifications()
    {
        if ( ! $this->notifications) {
            return json_decode('{}', true);
        }

        return json_decode($this->notifications, true);
    }

    /**
     * default customer permission
     *
     * @return false|string|string[]
     */
    public static function customerPermissions()
    {
        $categories = collect(config('customer-permissions'))->map(function ($value, $key) {
            $value['name'] = $key;

            return $value;
        })->groupBy('default')->first->toArray();

        $permissions = collect($categories)->map(function ($item) {
            return $item['name'];
        })->toArray();

        return json_encode($permissions);

    }


    /**
     * @return bool
     * @throws Exception
     */
    public function overQuota(): bool
    {
        return ! $this->getQuotaTracker()->check();
    }


    /**
     * @param  int  $unit_value
     *
     * @return mixed
     * @throws Exception
     */
    public function countUsage($unit_value = 0)
    {
        return $this->getQuotaTracker()->add(null, $unit_value);
    }

    /**
     * Get customer's sending quota rate.
     *
     * @return string
     * @throws Exception
     */
    public function displaySendingQuotaUsage(): string
    {
        if ($this->getSendingQuota() == -1) {
            return __('locale.labels.unlimited');
        }

        // @todo use percentage helper here
        return $this->getSendingQuotaUsagePercentage().'%';
    }


    /**
     * @throws Exception
     */
    public function cleanupQuotaTracker()
    {
        $this->getQuotaTracker()->cleanupSeries();
    }

    /**
     * get sms quota
     *
     * @return int
     */
    public function getSendingQuota()
    {
        // -1 indicate unlimited
        return $this->getOption('sms_max');
    }


    /**
     * @return string
     * @throws Exception
     */
    public function getSendingQuotaUsage(): string
    {
        $tracker = $this->getQuotaTracker();

        return $tracker->getUsage();
    }


    /**
     * Get customer's sending quota rate.
     *
     * @return string
     * @throws Exception
     */
    public function getSendingQuotaUsagePercentage()
    {
        $max   = $this->getSendingQuota();
        $count = $this->getSendingQuotaUsage();

        if ($max == -1) {
            return 0;
        }
        if ($max == 0) {
            return 0;
        }
        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Get the quota/limit object.
     *
     * @return array
     */
    public function getQuotaHash(): array
    {
        $current = $this->getCurrentSubscription();

        return [
                'start' => (isset($current->created_at) ? $current->created_at->timestamp : null),
                'max'   => $this->getOption('sms_max'),
        ];
    }

    /**
     * get sending limit
     *
     * @return array|null[]|string[]
     */
    public function getSendingLimits(): array
    {
        $timeValue = $this->getOption('sending_quota_time');
        if ($timeValue == -1) {
            return []; // no limit
        }
        $timeUnit = $this->getOption('sending_quota_time_unit');
        $limit    = $this->getOption('sending_quota');

        return ["{$timeValue} {$timeUnit}" => $limit];
    }

    /**
     * Get sending quota lock file.
     *
     * @return string file path
     */
    public function getSendingQuotaLockFile(): string
    {
        return storage_path("app/customer/quota/{$this->uid}");
    }


    /**
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

    public function getCurrentSubscription()
    {
        return $this->subscription;
    }


    /**
     * get initial quota tracker
     *
     * @throws Exception
     */
    public function initQuotaTracker()
    {
        $this->quotaTracker = new QuotaTrackerFile($this->getSendingQuotaLockFile(), $this->getQuotaHash(), $this->getSendingLimits());
        $this->quotaTracker->cleanupSeries();
        // @note: in case of multi-process, the following command must be issued manually
        //     $this->renewQuotaTracker();
    }

}
