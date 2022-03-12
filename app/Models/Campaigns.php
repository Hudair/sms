<?php

namespace App\Models;

use App\Library\RouletteWheel;
use App\Library\SMSCounter;
use App\Library\Tool;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

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
    public static $senderIdPools = [];
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

    public function nextScheduleDate($startDate, $interval, $intervalCount)
    {

        switch ($interval) {

            case 'month':
                $scheduleDate = $startDate->addMonthsNoOverflow($intervalCount);
                break;

            case 'day':
                $scheduleDate = $startDate->addDay($intervalCount);
                break;

            case 'week':
                $scheduleDate = $startDate->addWeek($intervalCount);
                break;

            case 'year':
                $scheduleDate = $startDate->addYearsNoOverflow($intervalCount);
                break;

            default:
                $scheduleDate = null;
                break;
        }

        return $scheduleDate;
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

    public function getSendingServers()
    {
        if ( ! is_null($this->sendingSevers)) {
            return $this->sendingSevers;
        }

        $sending_server_id   = CampaignsSendingServer::where('campaign_id', $this->id)->first()->sending_server_id;
        $this->sendingSevers = SendingServer::find($sending_server_id);

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
            $assoc[$key] = $fitness;
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
     * get coverage
     *
     * @return array
     */
    public function getCoverage(): array
    {
        $data          = [];
        $plan_coverage = PlansCoverageCountries::where('plan_id', $this->user->customer->activeSubscription()->plan->id)->cursor();
        foreach ($plan_coverage as $coverage) {
            $data[$coverage->country->country_code] = json_decode($coverage->options, true);
        }

        return $data;

    }

    /**
     * send campaign
     *
     * @throws NumberParseException
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

        if ($cutting_system == 'yes' && $this->user->customer->getOption('cutting_value') != 0) {
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


        $sending_server = $this->getSendingServers();
        $coverage = $this->getCoverage();

        collect($cutting_array)->chunk(1000)->each(function ($lines) use (&$prepareForTemplateTag, $cost, &$total_unit, $sending_server, $coverage) {

            $check_sender_id = $this->getSenderIds();

            if (count($check_sender_id) > 0) {
                $sender_id = $this->pickSenderIds();
            } else {
                $sender_id = null;
            }

            foreach ($lines as $line) {
                $phoneUtil         = PhoneNumberUtil::getInstance();
                $phoneNumberObject = $phoneUtil->parse('+'.$line['phone']);
                $country_code      = $phoneNumberObject->getCountryCode();

                if (is_array($coverage) && array_key_exists($country_code, $coverage)) {

                    if ($this->sms_type == 'plain' || $this->sms_type == 'unicode') {
                        $cost = $coverage[$country_code]['plain_sms'];
                    }

                    if ($this->sms_type == 'voice') {
                        $cost = $coverage[$country_code]['voice_sms'];
                    }

                    if ($this->sms_type == 'mms') {
                        $cost = $coverage[$country_code]['mms_sms'];
                    }

                    if ($this->sms_type == 'whatsapp') {
                        $cost = $coverage[$country_code]['whatsapp_sms'];
                    }

                    $message  = $this->renderSMS($this->message, $line);
                    $sms_type = $this->sms_type;

                    $sms_counter  = new SMSCounter();
                    $message_data = $sms_counter->count($message);
                    $sms_count    = $message_data->messages;

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

                } else {

                    $message = $this->renderSMS($this->message, $line);

                    $sms_type = $this->sms_type;

                    $sms_counter  = new SMSCounter();
                    $message_data = $sms_counter->count($message);
                    $sms_count    = $message_data->messages;

                    $price      = 1 * $sms_count;
                    $total_unit += (int) $price;

                    $preparedData['id']             = $line['id'];
                    $preparedData['user_id']        = $this->user_id;
                    $preparedData['phone']          = $line['phone'];
                    $preparedData['sender_id']      = $sender_id;
                    $preparedData['message']        = $message;
                    $preparedData['sms_type']       = $sms_type;
                    $preparedData['cost']           = (int) $price;
                    $preparedData['status']         = "Permission to send an SMS has not been enabled for the region indicated by the 'To' number: ".$line['phone'];

                }
                $preparedData['campaign_id']    = $this->id;
                $preparedData['sending_server'] = $sending_server;
                $prepareForTemplateTag[]        = $preparedData;
            }
        });


        collect($insertData)->chunk(5000)->each(function ($lines) use (&$prepareForTemplateTag, $cost, &$total_unit, $sending_server, $coverage) {

            $check_sender_id = $this->getSenderIds();

            if (count($check_sender_id) > 0) {
                $sender_id = $this->pickSenderIds();
            } else {
                $sender_id = null;
            }

            foreach ($lines as $line) {

                $phoneUtil         = PhoneNumberUtil::getInstance();
                $phoneNumberObject = $phoneUtil->parse('+'.$line['phone']);
                $country_code      = $phoneNumberObject->getCountryCode();

                if (is_array($coverage) && array_key_exists($country_code, $coverage)) {

                    if ($this->sms_type == 'plain' || $this->sms_type == 'unicode') {
                        $cost = $coverage[$country_code]['plain_sms'];
                    }

                    if ($this->sms_type == 'voice') {
                        $cost = $coverage[$country_code]['voice_sms'];
                    }

                    if ($this->sms_type == 'mms') {
                        $cost = $coverage[$country_code]['mms_sms'];
                    }

                    if ($this->sms_type == 'whatsapp') {
                        $cost = $coverage[$country_code]['whatsapp_sms'];
                    }

                    $message  = $this->renderSMS($this->message, $line);
                    $sms_type = $this->sms_type;

                    $sms_counter  = new SMSCounter();
                    $message_data = $sms_counter->count($message);
                    $sms_count    = $message_data->messages;

                    $price      = $cost * $sms_count;
                    $total_unit += (int) $price;

                    $preparedData['id']             = $line['id'];
                    $preparedData['user_id']        = $this->user_id;
                    $preparedData['phone']          = $line['phone'];
                    $preparedData['sender_id']      = $sender_id;
                    $preparedData['message']        = $message;
                    $preparedData['sms_type']       = $sms_type;
                    $preparedData['cost']           = (int) $price;
                    $preparedData['status']         = null;

                } else {

                    $message = $this->renderSMS($this->message, $line);

                    $sms_type = $this->sms_type;

                    $sms_counter  = new SMSCounter();
                    $message_data = $sms_counter->count($message);
                    $sms_count    = $message_data->messages;

                    $price      = 1 * $sms_count;
                    $total_unit += (int) $price;

                    $preparedData['id']             = $line['id'];
                    $preparedData['user_id']        = $this->user_id;
                    $preparedData['phone']          = $line['phone'];
                    $preparedData['sender_id']      = $sender_id;
                    $preparedData['message']        = $message;
                    $preparedData['sms_type']       = $sms_type;
                    $preparedData['cost']           = (int) $price;
                    $preparedData['status']         = "Permission to send an SMS has not been enabled for the region indicated by the 'To' number: ".$line['phone'];

                }
                $preparedData['campaign_id']    = $this->id;
                $preparedData['sending_server'] = $sending_server;
                $prepareForTemplateTag[]        = $preparedData;
            }
        });

        if ($this->user->sms_unit != '-1' && $total_unit > $this->user->sms_unit) {
            $this->failed(sprintf("Campaign `%s` (%s) halted, customer exceeds sms credit", $this->campaign_name, $this->uid));
            sleep(60);
        } else {

            $user = User::find($this->user->id);
            if ($user->sms_unit != '-1') {

                $user->update([
                        'sms_unit' => $user->sms_unit - $total_unit,
                ]);
            }

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
                            if (isset($this->media_url)){
                                $data['media_url'] = $this->media_url;
                            }
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

                if ($user->sms_unit != '-1') {
                    $user->update([
                            'sms_unit' => $user->sms_unit + $failed_cost,
                    ]);
                }

                $this->delivered();

            } catch (Exception $exception) {
                $this->failed($exception->getMessage());
            } finally {
                self::resetServerPools();
                $this->updateCache();
                $this->delivered();
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

        if (empty(self::$senderIdPools[$id])) {
            self::$senderIdPools[$id] = $id;
        }

        return self::$senderIdPools[$id];

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
            return '<span class="badge bg-primary text-uppercase me-1 mb-1">'.__('locale.labels.plain').'</span>';
        }
        if ($sms_type == 'unicode') {
            return '<span class="badge bg-primary text-uppercase me-1 mb-1">'.__('locale.labels.unicode').'</span>';
        }

        if ($sms_type == 'voice') {
            return '<span class="badge bg-success text-uppercase me-1 mb-1">'.__('locale.labels.voice').'</span>';
        }

        if ($sms_type == 'mms') {
            return '<span class="badge bg-info text-uppercase me-1 mb-1">'.__('locale.labels.mms').'</span>';
        }

        if ($sms_type == 'whatsapp') {
            return '<span class="badge bg-warning text-uppercase mb-1">'.__('locale.labels.whatsapp').'</span>';
        }

        return '<span class="badge bg-danger text-uppercase mb-1">'.__('locale.labels.invalid').'</span>';
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
                        <span class="badge badge-light-info text-uppercase me-1 mb-1">'.__('locale.labels.scheduled').'</span>
                        <p class="text-muted">'.Tool::customerDateTime($this->schedule_time).'</p>
                    </div>';
        }
        if ($sms_type == 'recurring') {
            return '<div>
                        <span class="badge badge-light-success text-uppercase me-1 mb-1">'.__('locale.labels.recurring').'</span>
                        <p class="text-muted">'.__('locale.labels.every').' '.$this->displayFrequencyTime().'</p>
                        <p class="text-muted">'.__('locale.labels.next_schedule_time').': '.Tool::formatDateTime($this->schedule_time).'</p>
                        <p class="text-muted">'.__('locale.labels.end_time').': '.Tool::formatDateTime($this->recurring_end).'</p>
                    </div>';
        }

        return '<span class="badge badge-light-primary text-uppercase me-1 mb-1">'.__('locale.labels.normal').'</span>';
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
                        <span class="badge bg-danger text-uppercase me-1 mb-1">'.__('locale.labels.'.$status).'</span>
                        <p class="text-muted" data-toggle="tooltip" data-placement="top" title="'.$this->reason.'">'.str_limit($this->reason, 40).'</p>
                    </div>';
        }
        if ($status == self::STATUS_SENDING || $status == self::STATUS_PROCESSING) {
            return '<div>
                        <span class="badge bg-primary text-uppercase mr-1 mb-1">'.__('locale.labels.'.$status).'</span>
                        <p class="text-muted">'.__('locale.labels.run_at').': '.Tool::customerDateTime($this->run_at).'</p>
                    </div>';
        }

        if ($status == self::STATUS_SCHEDULED) {
            return '<span class="badge bg-info text-uppercase mr-1 mb-1">'.__('locale.labels.scheduled').'</span>';
        }
        if ($status == self::STATUS_NEW || $status == self::STATUS_QUEUED) {
            return '<span class="badge bg-primary text-uppercase mr-1 mb-1">'.__('locale.labels.'.$status).'</span>';
        }


        return '<div>
                        <span class="badge bg-success text-uppercase mr-1 mb-1">'.__('locale.labels.delivered').'</span>
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
