<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(string $string, string $uid)
 * @method static create(array $array)
 * @method static select(string $string, string $string1, string $string2, string $string3, string $string4, string $string5)
 * @method static whereIn(string $string, mixed $ids)
 * @method static cursor()
 * @method static currentMonth()
 * @method static count()
 * @method static offset(mixed $start)
 * @method static whereLike(string[] $array, mixed $search)
 * @method static insert(array $data)
 */
class Reports extends Model
{
    protected $fillable = [
            'user_id',
            'campaign_id',
            'from',
            'to',
            'message',
            'media_url',
            'sms_type',
            'status',
            'send_by',
            'cost',
            'api_key',
            'sending_server_id',
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
     * get sending server
     *
     * @return BelongsTo
     *
     */
    public function sendingServer(): BelongsTo
    {
        return $this->belongsTo(SendingServer::class);
    }

    /**
     * get campaign
     *
     * @return HasMany
     *
     */
    public function campaign(): HasMany
    {
        return $this->hasMany(Campaigns::class);
    }

    public function scopeCurrentMonth($query){
        return $query->where('created_at', ">=", Carbon::now()->firstOfMonth());
    }

    /**
     * get sms type
     *
     * @return string
     */
    public function getSMSType(): string
    {
        $sms_type = $this->sms_type;

        if ($sms_type == 'plain') {
            return '<span class="badge badge-light-primary text-uppercase mr-1 mb-1">'.__('locale.labels.plain').'</span>';
        }
        if ($sms_type == 'unicode') {
            return '<span class="badge badge-light-primary text-uppercase mr-1 mb-1">'.__('locale.labels.unicode').'</span>';
        }

        if ($sms_type == 'voice') {
            return '<span class="badge badge-light-success text-uppercase mr-1 mb-1">'.__('locale.labels.voice').'</span>';
        }

        if ($sms_type == 'mms') {
            return '<span class="badge badge-light-info text-uppercase mr-1 mb-1">'.__('locale.labels.mms').'</span>';
        }

        if ($sms_type == 'whatsapp') {
            return '<span class="badge badge-light-warning text-uppercase mb-1">'.__('locale.labels.whatsapp').'</span>';
        }

        return '<span class="badge badge-light-danger text-uppercase mb-1">'.__('locale.labels.invalid').'</span>';
    }

    /**
     * get sms direction
     *
     * @return string
     */
    public function getSendBy(): string
    {
        $sms_type = $this->send_by;

        if ($sms_type == 'from') {
            return '<span class="badge badge-light-primary text-uppercase mr-1 mb-1">'.__('locale.labels.outgoing').'</span>';
        }

        if ($sms_type == 'to') {
            return '<span class="badge badge-light-success text-uppercase mr-1 mb-1">'.__('locale.labels.incoming').'</span>';
        }

        if ($sms_type == 'api') {
            return '<span class="badge badge-light-info text-uppercase mr-1 mb-1">'.__('locale.labels.api').'</span>';
        }

        return '<span class="badge badge-light-danger text-uppercase mb-1">'.__('locale.labels.invalid').'</span>';
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
