<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaigns\QuickSendRequest;
use App\Models\Campaigns;
use App\Models\Reports;
use App\Models\Traits\ApiResponser;
use App\Repositories\Contracts\CampaignRepository;
use Illuminate\Http\JsonResponse;

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
     * @param  QuickSendRequest  $request
     *
     * @return JsonResponse
     */
    public function smsSend(Campaigns $campaign, QuickSendRequest $request): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $input             = $request->all();
        $input['sms_type'] = 'plain';
        $input['api_key']  = request()->user()->api_token;


        $data = $this->campaigns->quickSend($campaign, $input);

        if (isset($data)) {

            if ($data->getData()->status == 'success') {
                $reports = Reports::select('uid', 'to', 'from', 'message', 'status', 'cost')->find($data->getData()->data->id);

                return $this->success($reports, $data->getData()->message);
            }

            return $this->error($data->getData()->message, 404);

        }

        return $this->error(__('locale.exceptions.something_went_wrong'), 404);
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
