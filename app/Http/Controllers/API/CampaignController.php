<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaigns\SendAPISMS;
use App\Models\Campaigns;
use App\Models\Reports;
use App\Models\Traits\ApiResponser;
use App\Repositories\Contracts\CampaignRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class CampaignController extends Controller
{
    use ApiResponser;

    protected $campaigns;

    /**
     * CampaignController constructor.
     *
     * @param  CampaignRepository  $campaigns
     */
    public function __construct(CampaignRepository $campaigns)
    {
        $this->campaigns = $campaigns;
    }

    /**
     * sms sending
     *
     * @param  Campaigns  $campaign
     * @param  SendAPISMS  $request
     *
     * @return JsonResponse
     * @throws NumberParseException
     */
    public function smsSend(Campaigns $campaign, SendAPISMS $request): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $input = $request->all();

        if ( ! isset($request->type)) {
            $sms_type = 'plain';
        } else {
            $sms_type = $request->type;
        }

        if ($sms_type == 'plain' || $sms_type == 'unicode' || $sms_type == 'voice' || $sms_type == 'mms' || $sms_type == 'whatsapp') {

            if ($sms_type == 'voice' && $request->gender == null && $request->language == null) {
                return response()->json([
                        'status'  => 'error',
                        'message' => 'Language and gender parameters are required',
                ]);
            }


            if ($sms_type == 'mms' && $request->media_url == null) {
                return response()->json([
                        'status'  => 'error',
                        'message' => 'media_url parameter is required',
                ]);
            }

            if ($sms_type == 'mms' && filter_var($request->media_url, FILTER_VALIDATE_URL) === false) {
                return response()->json([
                        'status'  => 'error',
                        'message' => 'Valid media url is required.',
                ]);
            }


            $input['sms_type']       = $sms_type;
            $input['api_key']        = request()->user()->api_token;
            $input['sending_server'] = request()->user()->api_sending_server;

            if (isset($request->schedule_time)) {
                $input['name']       = 'API_'.time();
                $input['originator'] = 'sender_id';
                $input['sender_id']  = [
                        $request->sender_id,
                ];

                $input['recipients']      = $request->recipient;
                $input['delimiter']       = ',';
                $input['schedule']        = true;
                $input['schedule_date']   = Carbon::parse($request->schedule_time)->toDateString();
                $input['schedule_time']   = Carbon::parse($request->schedule_time)->setSeconds(0)->format('H:i');
                $input['timezone']        = request()->user()->timezone;
                $input['frequency_cycle'] = 'onetime';

                $data = $this->campaigns->sendApi($campaign, $input);

                if (isset($data->getData()->status)) {

                    if ($data->getData()->status == 'success') {
                        return $this->success(null, $data->getData()->message);
                    }

                    return $this->error($data->getData()->message, 404);

                }

            } else {

                if (substr_count($request->recipient, ',') == 1) {

                    $input['name']       = 'API_'.time();
                    $input['originator'] = 'sender_id';
                    $input['sender_id']  = [
                            $request->sender_id,
                    ];

                    $input['recipients'] = $request->recipient;
                    $input['delimiter']  = ',';

                    $data = $this->campaigns->sendApi($campaign, $input);

                    if (isset($data->getData()->status)) {

                        if ($data->getData()->status == 'success') {
                            return $this->success(null, $data->getData()->message);
                        }

                        return $this->error($data->getData()->message, 404);

                    }

                } else {

                    $phoneUtil             = PhoneNumberUtil::getInstance();
                    $phoneNumberObject     = $phoneUtil->parse('+'.$input['recipient']);
                    $input['country_code'] = $phoneNumberObject->getCountryCode();

                    $data = $this->campaigns->quickSend($campaign, $input);
                    if (isset($data->getData()->status)) {

                        if ($data->getData()->status == 'success') {
                            $reports = Reports::select('uid', 'to', 'from', 'message', 'status', 'cost')->find($data->getData()->data->id);

                            return $this->success($reports, $data->getData()->message);
                        }

                        return $this->error($data->getData()->message, 404);

                    }
                }

            }

            return $this->error(__('locale.exceptions.something_went_wrong'), 404);
        }

        return $this->error(__('locale.exceptions.invalid_sms_type'), 404);

    }

    /**
     * view single sms reports
     *
     * @param  Reports  $uid
     *
     * @return JsonResponse
     */
    public function viewSMS(Reports $uid): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if (request()->user()->tokenCan('view_reports')) {
            $reports = Reports::select('uid', 'to', 'from', 'message', 'status', 'cost')->where('api_key', request()->user()->api_token)->find($uid->id);
            if ($reports) {
                return $this->success($reports);
            }

            return $this->error('SMS Info not found', 404);
        }

        return $this->error(__('locale.http.403.description'), 403);
    }


    /**
     * get all messages
     *
     * @return JsonResponse
     */
    public function viewAllSMS(): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if (request()->user()->tokenCan('view_reports')) {
            $reports = Reports::select('uid', 'to', 'from', 'message', 'status', 'cost')->where('api_key', request()->user()->api_token)->paginate(25);
            if ($reports) {
                return $this->success($reports);
            }

            return $this->error('SMS Info not found', 404);
        }

        return $this->error(__('locale.http.403.description'), 403);
    }
}
