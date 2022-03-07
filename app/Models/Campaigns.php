<?php

namespace App\Models;

use App\Library\RouletteWheel;
use App\Library\Tool;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(string $string, string $uid)
 * @method static create(array $array)
 * @method static find($campaign_id)
 * @method static cursor()
 * @method static whereIn(string $string, mixed $ids)
 * @method static count()
 */
class Campaigns extends SendCampaignSMS
{

    /**
     * Campaign status
     */
    const STATUS_NEW = 'new';
    const STATUS_QUEUED = 'queued';
    const STATUS_SENDING = 'sending';
    const STATUS_FAILED = 'failed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_PROCESSING = 'processing';


    /*
     * Campaign type
     */
    const TYPE_ONETIME = 'onetime';
    const TYPE_RECURRING = 'recurring';

    public static $serverPools = [];
    protected $sendingSevers = null;
    protected $senderIds = null;
    protected $currentSubscription;

    protected $fillable = [
            'user_id',
            'campaign_name',
            'message',
            'media_url',
            'language',
            'gender',
            'sms_type',
            'upload_type',
            'status',
            'reason',
            'api_key',
            'cache',
            'timezone',
            'schedule_time',
            'schedule_type',
            'frequency_cycle',
            'frequency_amount',
            'frequency_unit',
            'recurring_end',
            'run_at',
            'delivery_at',
            'batch_id',
    ];

    protected $dates = ['created_at', 'updated_at', 'run_at', 'delivery_at', 'schedule_time', 'recurring_end'];

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
     * get customer
     *
     * @return BelongsTo
     *
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'user_id');
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
     * get reports
     *
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Reports::class, 'campaign_id', 'id');
    }

    /**
     * associate with contact groups
     *
     * @return HasMany
     */
    public function contactList(): HasMany
    {
        return $this->hasMany(CampaignsList::class, 'campaign_id');
    }

    /**
     * associate with recipients
     *
     * @return HasMany
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignsRecipients::class, 'campaign_id');
    }


    /**
     * Get schedule recurs available values.
     *
     * @return array
     */
    public static function scheduleCycleValues(): array
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
     * contact count
     *
     * @param  false  $cache
     *
     * @return mixed|null
     */
    public function contactCount($cache = false)
    {
        if ($cache) {
            return $this->readCache('ContactCount', 0);
        }
        $list_ids   = $this->contactList()->select('contact_list_id')->cursor()->pluck('contact_list_id')->all();
        $list_count = Contacts::whereIn('group_id', $list_ids)->where('status', 'subscribe')->count();

        $recipients_count = $this->recipients()->count();

        return $list_count + $recipients_count;

    }

    /**
     * show delivered count
     *
     * @param  false  $cache
     *
     * @return int|mixed|null
     */
    public function deliveredCount($cache = false): int
    {
        if ($cache) {
            return $this->readCache('DeliveredCount', 0);
        }

        return $this->reports()->where('campaign_id', $this->id)->where('status', 'like', '%Delivered%')->count();
    }

    /**
     * show failed count
     *
     * @param  false  $cache
     *
     * @return int|mixed|null
     */
    public function failedCount($cache = false): int
    {
        if ($cache) {
            return $this->readCache('FailedDeliveredCount', 0);
        }

        return $this->reports()->where('campaign_id', $this->id)->where('status', 'not like', '%Delivered%')->count();
    }

    /**
     * show not delivered count
     *
     * @param  false  $cache
     *
     * @return int|mixed|null
     */
    public function notDeliveredCount($cache = false): int
    {
        if ($cache) {
            return $this->readCache('NotDeliveredCount', 0);
        }

        return $this->reports()->where('campaign_id', $this->id)->where('status', 'like', '%Sent%')->count();
    }

    /**
     * Update Campaign cached data.
     *
     * @param  null  $key
     */
    public function updateCache($key = null)
    {
        // cache indexes
        $index = [
                'DeliveredCount'       => function ($campaign) {
                    return $campaign->deliveredCount();
                },
                'FailedDeliveredCount' => function ($campaign) {
                    return $campaign->failedCount();
                },
                'NotDeliveredCount'    => function ($campaign) {
                    return $campaign->notDeliveredCount();
                },
                'ContactCount'         => function ($campaign) {
                    return $campaign->contactCount(true);
                },
        ];

        // retrieve cached data
        $cache = json_decode($this->cache, true);
        if (is_null($cache)) {
            $cache = [];
        }

        if (is_null($key)) {
            foreach ($index as $key => $callback) {
                $cache[$key] = $callback($this);
            }
        } else {
            $callback    = $index[$key];
            $cache[$key] = $callback($this);
        }

        // write back to the DB
        $this->cache = json_encode($cache);
        $this->save();
    }

    /**
     * Retrieve Campaign cached data.
     *
     * @param $key
     * @param  null  $default
     *
     * @return mixed
     */
    public function readCache($key, $default = null)
    {
        $cache = json_decode($this->cache, true);
        if (is_null($cache)) {
            return $default;
        }
        if (array_key_exists($key, $cache)) {
            if (is_null($cache[$key])) {
                return $default;
            } else {
                return $cache[$key];
            }
        } else {
            return $default;
        }
    }

    /**
     * get active plan sending servers
     *
     * @return mixed
     */
    public function activePlanSendingServers()
    {
        return PlansSendingServer::where('plan_id', $this->user->customer->activeSubscription()->plan_id);
    }

    /**
     * get active customer sending servers
     *
     * @return mixed
     */
    public function activeCustomerSendingServers()
    {
        return SendingServer::where('user_id', $this->user->id)->where('status', true);
    }

    public function getCurrentSubscription()
    {
        if (empty($this->currentSubscription)) {
            $this->currentSubscription = $this->user->customer->activeSubscription();
        }

        return $this->currentSubscription;
    }

    /**
     * get campaign sending servers
     *
     * @param  string  $type
     *
     * @return array
     */
    public function getSendingServers($type = 'plain'): array
    {
        if ( ! is_null($this->sendingSevers)) {
            return $this->sendingSevers;
        }

        if ($type == 'unicode') {
            $type = 'plain';
        }

        $result = [];

        // Check the customer has permissions using sending servers and has his own sending servers
        if ($this->user->customer->getOption('create_sending_server') == 'yes') {

            if ($this->activeCustomerSendingServers()->count()) {
                $result = SendingServer::where('user_id', $this->user->id)->where($type, 1)->where('status', true)->cursor()->map(function ($server) {
                    return [$server->id, '100'];
                });
            } elseif ($this->activePlanSendingServers()->count()) {
                $result = $this->activePlanSendingServers()->get()->map(function ($server) {
                    return [$server->sending_server_id, $server->fitness];
                });
            }
        } else {
            // If customer dont have permission creating sending servers
            $result = $this->activePlanSendingServers()->get()->map(function ($server) {
                return [$server->sending_server_id, $server->fitness];
            });
        }
        $assoc = [];
        foreach ($result as $server) {
            [$key, $fitness] = $server;
            $assoc[(int) $key] = $fitness;
        }

        $this->sendingSevers = $assoc;

        return $this->sendingSevers;
    }

    /**
     * get sender ids
     *
     * @return array|null
     */
    public function getSenderIds(): array
    {

        if ( ! is_null($this->senderIds)) {
            return $this->senderIds;
        }

        $result = CampaignsSenderid::where('campaign_id', $this->id)->cursor()->map(function ($sender_id) {
            return [$sender_id->sender_id, $sender_id->id];
        })->all();

        $assoc = [];
        foreach ($result as $server) {
            [$key, $fitness] = $server;
            $assoc[(int) $key] = $fitness;
        }

        $this->senderIds = $assoc;

        return $this->senderIds;
    }

    /**
     * mark campaign as queued to processing
     */
    public function running()
    {
        $this->status = self::STATUS_PROCESSING;
        $this->run_at = Carbon::now();
        $this->save();
    }

    /**
     * mark campaign as failed
     *
     * @param  null  $reason
     */
    public function failed($reason = null)
    {
        $this->status = self::STATUS_FAILED;
        $this->reason = $reason;
        $this->save();
    }

    /**
     * set campaign warning
     *
     * @param  null  $reason
     */
    public function warning($reason = null)
    {
        $this->reason = $reason;
        $this->save();
    }

    public function preparedDataToSend()
    {

        try {
            // clean up the tracker to prevent the log file from growing very big
            $this->user->customer->cleanupQuotaTracker();

            // set campaign queued to processing
            $this->running();

            // Reset max_execution_time so that command can run for a long time without being terminated
            Tool::resetMaxExecutionTime();

            $this->singleProcess();

        } catch (Exception $exception) {
            $this->failed($exception->getMessage());
        }

    }

    /**
     * @return $this
     */
    public function refreshStatus(): Campaigns
    {
        $campaign     = self::find($this->id);
        $this->status = $campaign->status;
        $this->save();

        return $this;
    }


    /**
     * Mark the campaign as delivered.
     */
    public function delivered()
    {
        $this->status      = self::STATUS_DELIVERED;
        $this->delivery_at = Carbon::now();
        $this->save();
    }

    /**
     * Mark the campaign as delivered.
     */
    public function cancelled()
    {
        $this->status = self::STATUS_CANCELLED;
        $this->save();
    }

    /**
     * Mark the campaign as processing.
     */
    public function processing()
    {
        $this->status = self::STATUS_PROCESSING;
        $this->run_at = Carbon::now();
        $this->save();
    }

    /**
     * render sms with tag
     *
     * @param $msg
     * @param $data
     *
     * @return string|string[]
     */
    public function renderSMS($msg, $data)
    {
        preg_match_all('~{(.*?)}~s', $msg, $datas);

        foreach ($datas[1] as $value) {
            if (array_key_exists($value, $data)) {
                $msg = preg_replace("/\b$value\b/u", $data[$value], $msg);
            } else {
                $msg = str_ireplace($value, '', $msg);
            }
        }

        return str_ireplace(["{", "}"], '', $msg);
    }


    /**
     * send campaign
     */
    public function singleProcess()
    {

        $prepareForTemplateTag = [];
        $contactsData          = [];
        $cutting_array         = [];
        $total_list_contacts   = 0;

        $check_list_count = CampaignsList::where('campaign_id', $this->id)->count();
        if ($check_list_count > 0) {

            $list    = CampaignsList::where('campaign_id', $this->id)->select('contact_list_id')->cursor();
            $list_id = $list->pluck('contact_list_id')->all();

            Contacts::whereIn('group_id', $list_id)->where('status', 'subscribe')->chunk(5000, function ($lines) use (&$contactsData) {
                foreach ($lines as $line) {
                    $data = $line->toArray();
                    foreach ($line->custom_fields as $field) {
                        $data[$field->tag] = $field->value;
                    }
                    $contactsData[] = $data;
                }
            });

            $total_list_contacts = count($contactsData);
        }

        if (CampaignsRecipients::where('campaign_id', $this->id)->count() > 0) {
            CampaignsRecipients::where('campaign_id', $this->id)->select('recipient as phone')->chunk(500, function ($lines) use (&$contactsData, &$total_list_contacts) {
                foreach ($lines as $line) {
                    $data           = $line->toArray();
                    $data['id']     = $total_list_contacts++;
                    $contactsData[] = $data;
                }
            });
        }


        $collection = collect($contactsData);

        $contact_count  = $this->contactCount($this->cache);
        $cutting_system = $this->user->customer->getOption('cutting_system');

        $cost       = 0;
        $total_unit = 0;

        if ($this->sms_type == 'plain' || $this->sms_type == 'unicode') {
            $cost = $this->user->customer->getOption('plain_sms');
        }

        if ($this->sms_type == 'voice') {
            $cost = $this->user->customer->getOption('voice_sms');
        }

        if ($this->sms_type == 'mms') {
            $cost = $this->user->customer->getOption('mms_sms');
        }

        if ($this->sms_type == 'whatsapp') {
            $cost = $this->user->customer->getOption('whatsapp_sms');
        }


        if ($cutting_system == 'yes') {
            $cutting_value = $this->user->customer->getOption('cutting_value');
            $cutting_unit  = $this->user->customer->getOption('cutting_unit');
            $cutting_logic = $this->user->customer->getOption('cutting_logic');

            if ($cutting_unit == 'percentage') {
                $cutting_value = ($cutting_value / 100) * $contact_count;
            }

            $cutting_amount = (int) round($cutting_value);

            if ($cutting_logic == 'random') {
                $cutting_array = $collection->random($cutting_amount)->all();
            }

            if ($cutting_logic == 'start') {
                $cutting_array = $collection->slice(0, $cutting_amount)->all();
            }

            if ($cutting_logic == 'end') {
                $cutting_array = $collection->slice(-$cutting_amount)->all();
            }
        }

        $insertData = Tool::check_diff_multi($collection->all(), $cutting_array);


        collect($cutting_array)->chunk(1000)->each(function ($lines) use (&$prepareForTemplateTag, $cost, &$total_unit) {

            $sending_server = $this->pickSendingServer();
            $sender_id      = $this->pickSenderIds();

            foreach ($lines as $line) {

                $message = $this->renderSMS($this->message, $line);

                $sms_type = $this->sms_type;

                if ($sms_type == 'plain') {
                    if (strlen($message) != strlen(utf8_decode($message))) {
                        $sms_type = 'unicode';
                    }

                    if ($sms_type == 'unicode') {
                        $length_count = mb_strlen(preg_replace('/\s+/', ' ', trim($message)), 'UTF-8');

                        if ($length_count <= 70) {
                            $sms_count = 1;
                        } else {
                            $sms_count = $length_count / 67;
                        }
                    } else {
                        $length_count = strlen(preg_replace('/\s+/', ' ', trim($message)));
                        if ($length_count <= 160) {
                            $sms_count = 1;
                        } else {
                            $sms_count = $length_count / 157;
                        }
                    }
                } elseif ($sms_type == 'unicode') {
                    $length_count = mb_strlen(preg_replace('/\s+/', ' ', trim($message)), 'UTF-8');

                    if ($length_count <= 70) {
                        $sms_count = 1;
                    } else {
                        $sms_count = $length_count / 67;
                    }
                } else {
                    $length_count = strlen(preg_replace('/\s+/', ' ', trim($message)));
                    if ($length_count <= 160) {
                        $sms_count = 1;
                    } else {
                        $sms_count = $length_count / 157;
                    }
                }
                $sms_count = ceil($sms_count);

                $price      = $cost * $sms_count;
                $total_unit += (int) $price;

                $preparedData['id']             = $line['id'];
                $preparedData['user_id']        = $this->user_id;
                $preparedData['phone']          = $line['phone'];
                $preparedData['sender_id']      = $sender_id;
                $preparedData['message']        = $message;
                $preparedData['sms_type']       = $sms_type;
                $preparedData['cost']           = (int) $price;
                $preparedData['status']         = 'Delivered';
                $preparedData['campaign_id']    = $this->id;
                $preparedData['sending_server'] = $sending_server;

                $prepareForTemplateTag[] = $preparedData;
            }
        });


        collect($insertData)->chunk(5000)->each(function ($lines) use (&$prepareForTemplateTag, $cost, &$total_unit) {
            foreach ($lines as $line) {

                $sending_server = $this->pickSendingServer();
                $sender_id      = $this->pickSenderIds();

                $message = $this->renderSMS($this->message, $line);

                $sms_type = $this->sms_type;

                if ($sms_type == 'plain') {
                    if (strlen($message) != strlen(utf8_decode($message))) {
                        $sms_type = 'unicode';
                    }

                    if ($sms_type == 'unicode') {
                        $length_count = mb_strlen(preg_replace('/\s+/', ' ', trim($message)), 'UTF-8');

                        if ($length_count <= 70) {
                            $sms_count = 1;
                        } else {
                            $sms_count = $length_count / 67;
                        }
                    } else {
                        $length_count = strlen(preg_replace('/\s+/', ' ', trim($message)));
                        if ($length_count <= 160) {
                            $sms_count = 1;
                        } else {
                            $sms_count = $length_count / 157;
                        }
                    }
                } elseif ($sms_type == 'unicode') {
                    $length_count = mb_strlen(preg_replace('/\s+/', ' ', trim($message)), 'UTF-8');

                    if ($length_count <= 70) {
                        $sms_count = 1;
                    } else {
                        $sms_count = $length_count / 67;
                    }
                } else {
                    $length_count = strlen(preg_replace('/\s+/', ' ', trim($message)));
                    if ($length_count <= 160) {
                        $sms_count = 1;
                    } else {
                        $sms_count = $length_count / 157;
                    }
                }
                $sms_count = ceil($sms_count);

                $price = $cost * $sms_count;

                $total_unit += (int) $price;

                $preparedData['id']             = $line['id'];
                $preparedData['user_id']        = $this->user_id;
                $preparedData['phone']          = $line['phone'];
                $preparedData['sender_id']      = $sender_id;
                $preparedData['message']        = $message;
                $preparedData['sms_type']       = $sms_type;
                $preparedData['cost']           = (int) $price;
                $preparedData['status']         = null;
                $preparedData['campaign_id']    = $this->id;
                $preparedData['sending_server'] = $sending_server;

                $prepareForTemplateTag[] = $preparedData;
            }
        });

        if ($total_unit > $this->user->sms_unit) {
            $this->failed(sprintf("Campaign `%s` (%s) halted, customer exceeds sms credit", $this->campaign_name, $this->uid));
            sleep(60);
        } else {

            $user = User::find($this->user->id);

            $user->update([
                    'sms_unit' => $user->sms_unit - $total_unit,
            ]);

            try {
                $failed_cost = 0;

                $this->processing();

                collect($prepareForTemplateTag)->sortBy('id')->values()->chunk(3000)->each(function ($sendData) use (&$failed_cost) {
                    foreach ($sendData as $data) {
                        $status = null;
                        if ($this->sms_type == 'plain' || $this->sms_type == 'unicode') {
                            $status = $this->sendPlainSMS($data);
                        }

                        if ($this->sms_type == 'voice') {

                            $data['language'] = $this->language;
                            $data['gender']   = $this->gender;

                            $status = $this->sendVoiceSMS($data);
                        }

                        if ($this->sms_type == 'mms') {

                            $data['media_url'] = $this->media_url;
                            $status            = $this->sendMMS($data);
                        }

                        if ($this->sms_type == 'whatsapp') {
                            $status = $this->sendWhatsApp($data);
                        }

                        if (substr_count($status, 'Delivered') == 1) {
                            $this->updateCache('DeliveredCount');
                        } else {
                            $failed_cost += $data['cost'];
                            $this->updateCache('FailedDeliveredCount');
                        }
                    }
                });

                unset($user);
                $user = User::find($this->user->id);

                $user->update([
                        'sms_unit' => $user->sms_unit + $failed_cost,
                ]);

                $this->delivered();

            } catch (Exception $exception) {
                $this->failed($exception->getMessage());
            } finally {
                self::resetServerPools();
                $this->updateCache();
            }
        }

    }

    /**
     * reset server pools
     */
    public static function resetServerPools()
    {
        self::$serverPools = [];
    }

    public function pickSendingServer()
    {
        $selection = $this->getSendingServers();

        // do not raise an exception, just wait if sending servers are available but exceeding sending limit
        $blacklisted = [];

        while (true) {
            $id = RouletteWheel::generate($selection);
            if (empty(self::$serverPools[$id])) {
                $server = SendingServer::find($id);
                if ($server->custom) {
                    $server['custom_info'] = $server->customSendingServer;
                }
                $server->cleanupQuotaTracker();
                self::$serverPools[$id] = $server;
            }

            if (self::$serverPools[$id]->overQuota()) {
                // log every 60 seconds
                if ( ! array_key_exists($id, $blacklisted) || time() - $blacklisted[$id] >= 60) {
                    $blacklisted[$id] = time();
                    $this->warning(sprintf('Sending server `%s` exceeds sending limit, skipped', self::$serverPools[$id]->name));
                }

                // if all sending servers are blacklisted
                if (sizeof($blacklisted) == sizeof($selection)) {
                    $this->warning(__('locale.campaigns.sending_server_exceed_sending_limit'));
                    sleep(30);
                }

                continue;
            }

            return self::$serverPools[$id];
        }
    }

    /**
     * pick sender id
     *
     * @return int|mixed|string
     */
    public function pickSenderIds()
    {
        $sender_id = $this->getSenderIds();

        $id = RouletteWheel::generate($sender_id);

        if ($id == 0) {
            $id = CampaignsSenderid::find($sender_id[0])->sender_id;
        }
        if (empty(self::$serverPools[$id])) {
            self::$serverPools[$id] = $id;
        }

        return self::$serverPools[$id];

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
            return '<div class="badge badge-primary text-uppercase mr-1 mb-1"><span>'.__('locale.labels.plain').'</span></div>';
        }
        if ($sms_type == 'unicode') {
            return '<div class="badge badge-primary text-uppercase mr-1 mb-1"><span>'.__('locale.labels.unicode').'</span></div>';
        }

        if ($sms_type == 'voice') {
            return '<div class="badge badge-success text-uppercase mr-1 mb-1"><span>'.__('locale.labels.voice').'</span></div>';
        }

        if ($sms_type == 'mms') {
            return '<div class="badge badge-info text-uppercase mr-1 mb-1"><span>'.__('locale.labels.mms').'</span></div>';
        }

        if ($sms_type == 'whatsapp') {
            return '<div class="badge badge-warning text-uppercase mb-1"><span>'.__('locale.labels.whatsapp').'</span></div>';
        }

        return '<div class="badge badge-danger text-uppercase mb-1"><span>'.__('locale.labels.invalid').'</span></div>';
    }

    /**
     * get sms type
     *
     * @return string
     */
    public function getCampaignType(): string
    {
        $sms_type = $this->schedule_type;

        if ($sms_type == 'onetime') {
            return '<div>
                        <div class="badge badge-info text-uppercase mr-1 mb-1"><span>'.__('locale.labels.scheduled').'</span></div>
                        <p class="text-muted">'.Tool::customerDateTime($this->schedule_time).'</p>
                    </div>';
        }
        if ($sms_type == 'recurring') {
            return '<div>
                        <div class="badge badge-success text-uppercase mr-1 mb-1"><span>'.__('locale.labels.recurring').'</span></div>
                        <p class="text-muted">'.__('locale.labels.every').' '.$this->displayFrequencyTime().'</p>
                        <p class="text-muted">'.__('locale.labels.end_time').' '.Tool::formatDate($this->recurring_end).'</p>
                    </div>';
        }

        return '<div class="badge badge-primary text-uppercase mr-1 mb-1"><span>'.__('locale.labels.normal').'</span></div>';
    }

    /**
     * Display frequency time
     *
     * @return string
     */
    public function displayFrequencyTime(): string
    {
        return $this->frequency_amount.' '.Tool::getPluralParse($this->frequency_unit, $this->frequency_amount);
    }


    /**
     * get campaign status
     *
     * @return string
     */
    public function getStatus(): string
    {
        $status = $this->status;

        if ($status == self::STATUS_FAILED || $status == self::STATUS_CANCELLED) {
            return '<div>
                        <div class="badge badge-danger text-uppercase mr-1 mb-1"><span>'.__('locale.labels.'.$status).'</span></div>
                        <p class="text-muted" data-toggle="tooltip" data-placement="top" title="'.$this->reason.'">'.str_limit($this->reason, 40).'</p>
                    </div>';
        }
        if ($status == self::STATUS_SENDING || $status == self::STATUS_PROCESSING) {
            return '<div>
                        <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>'.__('locale.labels.'.$status).'</span></div>
                        <p class="text-muted">'.__('locale.labels.run_at').': '.Tool::customerDateTime($this->run_at).'</p>
                    </div>';
        }

        if ($status == self::STATUS_SCHEDULED) {
            return '<div>
                        <div class="badge badge-info text-uppercase mr-1 mb-1"><span>'.__('locale.labels.scheduled').'</span></div>
                    </div>';
        }
        if ($status == self::STATUS_NEW || $status == self::STATUS_QUEUED) {
            return '<div>
                        <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>'.__('locale.labels.'.$status).'</span></div>
                    </div>';
        }


        return '<div>
                        <div class="badge badge-success text-uppercase mr-1 mb-1"><span>'.__('locale.labels.delivered').'</span></div>
                        <p class="text-muted">'.__('locale.labels.delivered_at').': '.Tool::customerDateTime($this->delivery_at).'</p>
                    </div>';
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
