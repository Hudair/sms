<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Library\SMSCounter;
use App\Library\Tool;
use App\Models\Campaigns;
use App\Models\CampaignsList;
use App\Models\CampaignsRecipients;
use App\Models\CampaignsSenderid;
use App\Models\CampaignsSendingServer;
use App\Models\ContactGroups;
use App\Models\Country;
use App\Models\PhoneNumbers;
use App\Models\PlansCoverageCountries;
use App\Models\Reports;
use App\Models\Senderid;
use App\Models\Templates;
use App\Models\TemplateTags;
use App\Notifications\SendCampaignCopy;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Carbon\Carbon;
use Exception;
use Generator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportsController extends Controller
{

    /**
     * sms reports
     *
     * @param  Request  $request
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function reports(Request $request)
    {
        $this->authorize('view_reports');
        $recipient = $request->recipient;
        if ($recipient) {
            $title = __('locale.contacts.conversion_with', ['recipient' => $recipient]);
            $name  = __('locale.contacts.view_conversion');
        } else {
            $title = __('locale.menu.All Messages');
            $name  = __('locale.menu.All Messages');
        }

        $breadcrumbs = [
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Reports')],
                ['name' => $name],
        ];


        return view('customer.Reports.all_messages', compact('breadcrumbs', 'recipient', 'title'));
    }

    /**
     * get all message reports
     *
     * @param  Request  $request
     *
     * @throws AuthorizationException
     */
    public function searchAllMessages(Request $request)
    {
        $this->authorize('view_reports');

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'created_at',
                4 => 'send_by',
                5 => 'sms_type',
                6 => 'from',
                7 => 'to',
                8 => 'cost',
                9 => 'status',
        ];

        $totalData = Reports::where('user_id', auth()->user()->id)->count();

        $totalFiltered = $totalData;

        $limit     = $request->input('length');
        $start     = $request->input('start');
        $order     = $columns[$request->input('order.0.column')];
        $dir       = $request->input('order.0.dir');
        $recipient = $request->get('recipient');

        if (empty($request->input('search.value')) && empty($recipient)) {

            $sms_reports = Reports::where('user_id', auth()->user()->id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } elseif ($recipient != null) {
            $sms_reports = Reports::where('user_id', auth()->user()->id)
                    ->where('to', $recipient)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $sms_reports = Reports::where('user_id', auth()->user()->id)->whereLike(['uid', 'send_by', 'sms_type', 'from', 'to', 'cost', 'status', 'created_at'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Reports::where('user_id', auth()->user()->id)->whereLike(['uid', 'send_by', 'sms_type', 'from', 'to', 'cost', 'status', 'created_at'], $search)->count();
        }

        $data = [];
        if ( ! empty($sms_reports)) {
            foreach ($sms_reports as $report) {
                if ($report->created_at == null) {
                    $created_at = null;
                } else {
                    $created_at = Tool::customerDateTime($report->created_at);
                }

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $report->uid;
                $nestedData['created_at']    = $created_at;
                $nestedData['send_by']       = $report->getSendBy();
                $nestedData['sms_type']      = $report->getSMSType();
                $nestedData['from']          = $report->from;
                $nestedData['to']            = $report->to;
                $nestedData['cost']          = $report->cost;
                $nestedData['status']        = str_limit($report->status, 20);
                $data[]                      = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();

    }

    /**
     * view single reports
     *
     * @param  Reports  $uid
     *
     * @return JsonResponse
     */
    public function viewReports(Reports $uid): JsonResponse
    {
        return response()->json([
                'status' => 'success',
                'data'   => $uid,
        ]);

    }

    /**
     * @param  Reports  $uid
     *
     * @return JsonResponse
     * @throws Exception
     */

    public function destroy(Reports $uid): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        if ( ! $uid->delete()) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.campaigns.sms_was_successfully_deleted'),
        ]);

    }

    /**
     * bulk sms delete
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function batchAction(Request $request): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $ids = $request->get('ids');

        if (Reports::whereIn('uid', $ids)->where('user_id', auth()->user()->id)->delete()) {
            return response()->json([
                    'status'  => 'success',
                    'message' => __('locale.campaigns.sms_was_successfully_deleted'),
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }


    /**
     * sms received
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function received()
    {
        $this->authorize('view_reports');

        $breadcrumbs = [
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Reports')],
                ['name' => __('locale.menu.Received Messages')],
        ];

        return view('customer.Reports.received_messages', compact('breadcrumbs'));
    }


    /**
     * get all received reports
     *
     * @param  Request  $request
     *
     * @throws AuthorizationException
     */
    public function searchReceivedMessage(Request $request)
    {
        $this->authorize('view_reports');

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'created_at',
                5 => 'sms_type',
                6 => 'from',
                7 => 'to',
                8 => 'cost',
                9 => 'status',
        ];

        $totalData = Reports::where('user_id', auth()->user()->id)->where('send_by', 'to')->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $sms_reports = Reports::where('user_id', auth()->user()->id)->where('send_by', 'to')->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $sms_reports = Reports::where('user_id', auth()->user()->id)->where('send_by', 'to')->whereLike(['uid', 'sms_type', 'from', 'to', 'cost', 'status', 'created_at'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Reports::where('user_id', auth()->user()->id)->where('send_by', 'to')->whereLike(['uid', 'sms_type', 'from', 'to', 'cost', 'status', 'created_at'], $search)->count();
        }

        $data = [];
        if ( ! empty($sms_reports)) {
            foreach ($sms_reports as $report) {
                if ($report->created_at == null) {
                    $created_at = null;
                } else {
                    $created_at = Tool::customerDateTime($report->created_at);
                }

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $report->uid;
                $nestedData['created_at']    = $created_at;
                $nestedData['sms_type']      = $report->getSMSType();
                $nestedData['from']          = $report->from;
                $nestedData['to']            = $report->to;
                $nestedData['cost']          = $report->cost;
                $nestedData['status']        = str_limit($report->status, 20);
                $data[]                      = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();

    }

    /**
     * sms sent
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function sent()
    {
        $this->authorize('view_reports');

        $breadcrumbs = [
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Reports')],
                ['name' => __('locale.menu.Sent Messages')],
        ];

        return view('customer.Reports.sent_messages', compact('breadcrumbs'));
    }


    /**
     * get all sent reports
     *
     * @param  Request  $request
     *
     * @throws AuthorizationException
     */
    public function searchSentMessage(Request $request)
    {
        $this->authorize('view_reports');

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'created_at',
                5 => 'sms_type',
                6 => 'from',
                7 => 'to',
                8 => 'cost',
                9 => 'status',
        ];

        $totalData = Reports::where('user_id', auth()->user()->id)->where('send_by', 'from')->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $sms_reports = Reports::where('user_id', auth()->user()->id)->where('send_by', 'from')->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $sms_reports = Reports::where('user_id', auth()->user()->id)->where('send_by', 'from')->whereLike(['uid', 'sms_type', 'from', 'to', 'cost', 'status', 'created_at'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Reports::where('user_id', auth()->user()->id)->where('send_by', 'from')->whereLike(['uid', 'sms_type', 'from', 'to', 'cost', 'status', 'created_at'], $search)->count();
        }

        $data = [];
        if ( ! empty($sms_reports)) {
            foreach ($sms_reports as $report) {
                if ($report->created_at == null) {
                    $created_at = null;
                } else {
                    $created_at = Tool::customerDateTime($report->created_at);
                }

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $report->uid;
                $nestedData['created_at']    = $created_at;
                $nestedData['sms_type']      = $report->getSMSType();
                $nestedData['from']          = $report->from;
                $nestedData['to']            = $report->to;
                $nestedData['cost']          = $report->cost;
                $nestedData['status']        = str_limit($report->status, 20);
                $data[]                      = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();

    }


    /**
     * get campaign details
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function campaigns()
    {
        $this->authorize('view_reports');

        $breadcrumbs = [
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Reports')],
                ['name' => __('locale.menu.Campaigns')],
        ];

        return view('customer.Reports.campaigns', compact('breadcrumbs'));
    }


    /**
     * search campaign data
     *
     * @param  Request  $request
     *
     * @throws AuthorizationException
     */
    public function searchCampaigns(Request $request)
    {

        $this->authorize('view_reports');

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'campaign_name',
                4 => 'contacts',
                5 => 'sms_type',
                6 => 'schedule_type',
                7 => 'status',
                8 => 'uid',
        ];

        $totalData = Campaigns::where('user_id', auth()->user()->id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $campaigns = Campaigns::where('user_id', auth()->user()->id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $campaigns = Campaigns::where('user_id', auth()->user()->id)->whereLike(['uid', 'campaign_name', 'sms_type', 'schedule_type', 'created_at', 'status'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Campaigns::where('user_id', auth()->user()->id)->whereLike(['uid', 'campaign_name', 'sms_type', 'schedule_type', 'created_at', 'status'], $search)->count();
        }

        $data = [];
        if ( ! empty($campaigns)) {
            foreach ($campaigns as $campaign) {

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $campaign->uid;
                $nestedData['campaign_name'] = "<div>
                                                        <p class='text-bold-600'> $campaign->campaign_name </p>
                                                        <p class='text-muted'>".__('locale.labels.created_at').': '.Tool::formatHumanTime($campaign->created_at)."</p>
                                                   </div>";
                $nestedData['contacts']      = Tool::number_with_delimiter($campaign->contactCount($campaign->cache));
                $nestedData['sms_type']      = $campaign->getSMSType();
                $nestedData['schedule_type'] = $campaign->getCampaignType();
                $nestedData['status']        = $campaign->getStatus();
                $nestedData['camp_status']   = $campaign->status;

                $nestedData['show']       = route('customer.reports.campaign.edit', $campaign->uid);
                $nestedData['show_label'] = __('locale.buttons.edit');

                $nestedData['overview']       = route('customer.reports.campaign.overview', $campaign->uid);
                $nestedData['overview_label'] = __('locale.menu.Overview');

                $nestedData['delete'] = __('locale.buttons.delete');
                $data[]               = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();

    }

    public function editCampaign(Campaigns $campaign)
    {

        if ($campaign->upload_type == 'file') {
            return redirect()->route('customer.reports.campaigns')->with([
                    'status'  => 'info',
                    'message' => __('locale.campaigns.you_are_not_able_to_update_file_import_campaign'),
            ]);
        }

        $breadcrumbs = [
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url("/reports/campaigns"), 'name' => __('locale.menu.Reports')],
                ['name' => __('locale.menu.Campaign Builder')],
        ];


        if (Auth::user()->customer->getOption('sender_id_verification') == 'yes') {
            $sender_ids    = Senderid::where('user_id', auth()->user()->id)->where('status', 'active')->cursor();
            $phone_numbers = PhoneNumbers::where('user_id', auth()->user()->id)->where('status', 'assigned')->cursor();
        } else {
            $sender_ids    = null;
            $phone_numbers = null;
        }
        $template_tags  = TemplateTags::cursor();
        $contact_groups = ContactGroups::where('status', 1)->where('customer_id', auth()->user()->id)->cursor();

        $templates = Templates::where('user_id', auth()->user()->id)->where('status', 1)->cursor();

        $campaign_sender_ids = CampaignsSenderid::where('campaign_id', $campaign->id)->cursor();

        $exist_sender_id     = null;
        $exist_phone_numbers = [];
        $originator          = 'sender_id';

        foreach ($campaign_sender_ids as $sender_id) {
            if ($sender_id->originator == 'sender_id') {
                $exist_sender_id = $sender_id->sender_id;
            } else {
                $originator            = 'phone_number';
                $exist_phone_numbers[] = $sender_id->sender_id;
            }
        }

        $exist_groups     = CampaignsList::where('campaign_id', $campaign->id)->select('contact_list_id')->get()->pluck('contact_list_id')->toArray();
        $exist_recipients = CampaignsRecipients::where('campaign_id', $campaign->id)->select('recipient')->get()->pluck('recipient')->toArray();

        $exist_recipients = implode(',', $exist_recipients);

        $plan_id = Auth::user()->customer->activeSubscription()->plan_id;

        return view('customer.Campaigns.updateCampaignBuilder', compact('breadcrumbs', 'sender_ids', 'phone_numbers', 'template_tags', 'contact_groups', 'templates', 'campaign', 'exist_sender_id', 'originator', 'exist_phone_numbers', 'exist_groups', 'exist_recipients', 'plan_id'));
    }


    /**
     * @param  Campaigns  $campaign
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function postEditCampaign(Campaigns $campaign, Request $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $input    = $request->except('_token');
        $sms_type = $input['sms_type'];

        $sending_servers = $campaign->getSendingServers();

        if (empty($sending_servers)) {

            return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.sending_server_not_available'),
            ]);
        }

        $sender_id = null;
        if (Auth::user()->customer->getOption('sender_id_verification') == 'yes') {
            if (isset($input['originator'])) {
                if ($input['originator'] == 'sender_id') {

                    if ( ! isset($input['sender_id'])) {
                        return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }

                    $sender_id = $input['sender_id'];

                    if (is_array($sender_id) && count($sender_id) > 0) {
                        $invalid   = [];
                        $senderids = Senderid::where('user_id', Auth::user()->id)
                                ->where('status', 'active')
                                ->select('sender_id')
                                ->cursor()
                                ->pluck('sender_id')
                                ->all();

                        foreach ($sender_id as $sender) {
                            if ( ! in_array($sender, $senderids)) {
                                $invalid[] = $sender;
                            }
                        }

                        if (count($invalid)) {

                            return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.sender_id.sender_id_invalid', ['sender_id' => $invalid[0]]),
                            ]);
                        }
                    } else {

                        return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }
                } else {

                    if ( ! isset($input['phone_number'])) {
                        $sender_id = CampaignsSenderid::where('campaign_id', $campaign->id)->pluck('sender_id')->toArray();
                    } else {
                        $sender_id = $input['phone_number'];
                    }


                    if (isset($sender_id) && is_array($sender_id) && count($sender_id) > 0) {
                        $type_supported = [];
                        PhoneNumbers::where('user_id', Auth::user()->id)
                                ->where('status', 'assigned')
                                ->cursor()
                                ->reject(function ($number) use ($sender_id, &$type_supported, &$invalid) {
                                    if (in_array($number->number, $sender_id) && ! str_contains($number->capabilities, 'sms')) {
                                        return $type_supported[] = $number->number;
                                    }

                                    return $sender_id;
                                })->all();

                        if (count($type_supported)) {

                            return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.sender_id.sender_id_sms_capabilities', ['sender_id' => $type_supported[0]]),
                            ]);
                        }
                    } else {

                        return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }
                }
            } else {

                return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.sender_id.sender_id_required'),
                ]);
            }
        } else {
            if (isset($input['originator'])) {
                if ($input['originator'] == 'sender_id') {
                    if ( ! isset($input['sender_id'])) {

                        return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }

                    $sender_id = $input['sender_id'];
                } else {

                    if ( ! isset($input['phone_number'])) {

                        return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.phone_numbers_required'),
                        ]);
                    }

                    $sender_id = $input['phone_number'];
                }

                if ( ! is_array($sender_id) || count($sender_id) <= 0) {

                    return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.sender_id.sender_id_required'),
                    ]);
                }
            }
            if (isset($input['sender_id'])) {
                $sender_id = $input['sender_id'];
            }
        }

        $total           = 0;
        $campaign_groups = [];

        // update contact groups details
        if (isset($input['contact_groups']) && is_array($input['contact_groups']) && count($input['contact_groups']) > 0) {
            $contact_groups = ContactGroups::whereIn('id', $input['contact_groups'])->where('status', true)->where('customer_id', Auth::user()->id)->cursor();
            foreach ($contact_groups as $group) {
                $total             += $group->subscribersCount($group->cache);
                $campaign_groups[] = [
                        'campaign_id'     => $campaign->id,
                        'contact_list_id' => $group->id,
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                ];
            }
        }

        // update manual input numbers
        if (isset($input['recipients'])) {
            switch ($input['delimiter']) {
                case ',':
                    $recipients = explode(',', $input['recipients']);
                    break;

                case ';':
                    $recipients = explode(';', $input['recipients']);
                    break;

                case '|':
                    $recipients = explode('|', $input['recipients']);
                    break;

                case 'tab':
                    $recipients = explode(' ', $input['recipients']);
                    break;

                case 'new_line':
                    $recipients = explode("\n", $input['recipients']);
                    break;

                default:
                    $recipients = [];
                    break;
            }

            $recipients = collect($recipients)->unique();

            $total   += $recipients->count();
            $numbers = [];

            foreach ($recipients->chunk(500) as $chunk) {
                foreach ($chunk as $number) {
                    $numbers[] = [
                            'campaign_id' => $campaign->id,
                            'recipient'   => preg_replace("/\r/", "", $number),
                            'created_at'  => Carbon::now(),
                            'updated_at'  => Carbon::now(),
                    ];
                }
            }

            CampaignsRecipients::where('campaign_id', $campaign->id)->delete();

            CampaignsRecipients::insert($numbers);
        } else {
            CampaignsRecipients::where('campaign_id', $campaign->id)->delete();
        }

        if ($total == 0) {
            return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.contact_not_found'),
            ]);
        }


        if (Auth::user()->sms_unit != '-1') {
            $coverage = PlansCoverageCountries::where('plan_id', $input['plan_id'])->first();

            $priceOption = json_decode($coverage->options, true);

            $sms_count = 1;
            $price     = 0;

            if (isset($input['message'])) {
                $sms_counter  = new SMSCounter();
                $message_data = $sms_counter->count($input['message']);
                $sms_count    = $message_data->messages;
            }


            if ($sms_type == 'plain' || $sms_type == 'unicode') {
                $unit_price = $priceOption['plain_sms'];
                $price      = $total * $unit_price;
            }

            if ($sms_type == 'voice') {
                $unit_price = $priceOption['voice_sms'];
                $price      = $total * $unit_price;
            }

            if ($sms_type == 'mms') {
                $unit_price = $priceOption['mms_sms'];
                $price      = $total * $unit_price;
            }

            if ($sms_type == 'whatsapp') {
                $unit_price = $priceOption['whatsapp_sms'];
                $price      = $total * $unit_price;
            }

            $price *= $sms_count;

            $balance = Auth::user()->sms_unit;

            if ($price > $balance) {
                return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.campaigns.not_enough_balance', [
                                'current_balance' => $balance,
                                'campaign_price'  => $price,
                        ]),
                ]);
            }
        }

        CampaignsSenderid::where('campaign_id', $campaign->id)->delete();

        foreach ($sender_id as $id) {

            $data = [
                    'campaign_id' => $campaign->id,
                    'sender_id'   => $id,
            ];

            if (isset($input['originator'])) {
                $data['originator'] = $input['originator'];
            }

            CampaignsSenderid::create($data);
        }

        CampaignsList::where('campaign_id', $campaign->id)->delete();

        CampaignsList::insert($campaign_groups);


        // if schedule is available then check date, time and timezone
        if (isset($input['schedule']) && $input['schedule'] == "true") {

            $schedule_date = $input['schedule_date'].' '.$input['schedule_time'];
            $schedule_time = Tool::systemTimeFromString($schedule_date, $input['timezone']);

            $campaign->timezone      = $input['timezone'];
            $campaign->status        = Campaigns::STATUS_SCHEDULED;
            $campaign->schedule_time = $schedule_time;


            if ($input['frequency_cycle'] == 'onetime') {
                // working with onetime schedule
                $campaign->schedule_type = Campaigns::TYPE_ONETIME;
            } else {
                // working with recurring schedule
                //if schedule time frequency is not one time then check frequency details
                $recurring_date = $input['recurring_date'].' '.$input['recurring_time'];
                $recurring_end  = Tool::systemTimeFromString($recurring_date, $input['timezone']);

                $campaign->schedule_type = Campaigns::TYPE_RECURRING;
                $campaign->recurring_end = $recurring_end;

                if (isset($input['frequency_cycle'])) {
                    if ($input['frequency_cycle'] != 'custom') {
                        $schedule_cycle             = $campaign::scheduleCycleValues();
                        $limits                     = $schedule_cycle[$input['frequency_cycle']];
                        $campaign->frequency_cycle  = $input['frequency_cycle'];
                        $campaign->frequency_amount = $limits['frequency_amount'];
                        $campaign->frequency_unit   = $limits['frequency_unit'];
                    } else {
                        $campaign->frequency_cycle  = $input['frequency_cycle'];
                        $campaign->frequency_amount = $input['frequency_amount'];
                        $campaign->frequency_unit   = $input['frequency_unit'];
                    }
                }
            }
        } else {
            $campaign->status = Campaigns::STATUS_QUEUED;
        }
        //update cache
        $campaign->cache = json_encode([
                'ContactCount'         => $total,
                'DeliveredCount'       => 0,
                'FailedDeliveredCount' => 0,
                'NotDeliveredCount'    => 0,
        ]);

        $campaign->message = $input['message'];

        if ($sms_type == 'voice') {
            $campaign->language = $input['language'];
            $campaign->gender   = $input['gender'];
        }

        if ($sms_type == 'mms') {
            $campaign->media_url = Tool::uploadImage($input['mms_file']);
        }

        $camp = $campaign->save();

        if ($camp) {
            return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                    'status'  => 'success',
                    'message' => __('locale.campaigns.campaign_successfully_updated'),
            ]);
        }

        return redirect()->route('customer.reports.campaign.edit', $campaign->uid)->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }


    public function campaignOverview(Campaigns $campaign)
    {
        $breadcrumbs = [
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url("/reports/campaigns"), 'name' => __('locale.menu.Reports')],
                ['name' => __('locale.menu.Overview')],
        ];


        return view('customer.Campaigns.overview', compact('campaign', 'breadcrumbs'));
    }

    /**
     * view campaign reports
     *
     * @param  Campaigns  $campaign
     * @param  Request  $request
     *
     * @throws AuthorizationException
     */
    public function campaignReports(Campaigns $campaign, Request $request)
    {

        $this->authorize('view_reports');

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'created_at',
                6 => 'from',
                7 => 'to',
                8 => 'cost',
                9 => 'status',
        ];

        $totalData = Reports::where('user_id', auth()->user()->id)->where('campaign_id', $campaign->id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $sms_reports = Reports::where('user_id', auth()->user()->id)->where('campaign_id', $campaign->id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $sms_reports = Reports::where('user_id', auth()->user()->id)->where('campaign_id', $campaign->id)->whereLike(['uid', 'from', 'to', 'cost', 'status', 'created_at'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Reports::where('user_id', auth()->user()->id)->where('campaign_id', $campaign->id)->whereLike(['uid', 'from', 'to', 'cost', 'status', 'created_at'], $search)->count();
        }

        $data = [];
        if ( ! empty($sms_reports)) {
            foreach ($sms_reports as $report) {
                if ($report->created_at == null) {
                    $created_at = null;
                } else {
                    $created_at = Tool::customerDateTime($report->created_at);
                }

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $report->uid;
                $nestedData['created_at']    = $created_at;
                $nestedData['from']          = $report->from;
                $nestedData['to']            = $report->to;
                $nestedData['cost']          = $report->cost;
                $nestedData['status']        = str_limit($report->status, 20);
                $data[]                      = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();
    }

    /**
     * @param $type
     *
     * @return Generator
     */

    public function reportsGenerator($type): Generator
    {
        if ($type == 'all') {
            foreach (Reports::where('user_id', Auth::user()->id)->cursor() as $report) {
                yield $report;
            }
        } else {
            foreach (Reports::where('user_id', Auth::user()->id)->where('send_by', $type)->cursor() as $report) {
                yield $report;
            }
        }


    }


    /**
     * @param $campaign_id
     *
     * @return Generator
     */

    public function campaignReportsGenerator($campaign_id): Generator
    {
        foreach (Reports::where('user_id', Auth::user()->id)->where('campaign_id', $campaign_id)->cursor() as $report) {
            yield $report;
        }
    }

    /**
     * @param  Request  $request
     *
     * @return RedirectResponse|BinaryFileResponse
     * @throws AuthorizationException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function export(Request $request)
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('customer.reports.all')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view_reports');

        Tool::resetMaxExecutionTime();


        $file_name = (new FastExcel($this->exportData($request)))->export(storage_path('Reports_'.time().'.xlsx'));

        return response()->download($file_name);

    }


    /**
     * @param $request
     *
     * @return Generator
     */
    public function exportData($request): Generator
    {
        $start_date = null;
        $end_date   = null;

        if ($request->start_date && $request->end_date) {
            $start_time = $request->start_date.' '.$request->start_time;
            $start_date = Tool::systemTimeFromString($start_time, Auth::user()->timezone);

            $end_time = $request->end_date.' '.$request->end_time;
            $end_date = Tool::systemTimeFromString($end_time, Auth::user()->timezone);
        }

        $status    = $request->status;
        $direction = $request->direction;
        $type      = $request->type;
        $to        = $request->to;
        $from      = $request->from;

        if ($status == 'delivered') {
            $status = 'Delivered';
        }

        $get_data = Reports::query()->when($status, function ($query) use ($status) {
            $query->whereLike(['status'], $status);
        })->when($from, function ($query) use ($from) {
            $query->whereLike(['from'], $from);
        })->when($to, function ($query) use ($to) {
            $query->whereLike(['to'], $to);
        })->when($direction, function ($query) use ($direction) {
            $query->where('send_by', $direction);
        })->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        })->where('sms_type', $type)->where('user_id', Auth::user()->id)->cursor();

        foreach ($get_data as $report) {
            yield $report;
        }
    }

    /**
     * @return RedirectResponse|BinaryFileResponse
     * @throws AuthorizationException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportSent()
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('customer.reports.all')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view_reports');

        $file_name = (new FastExcel($this->reportsGenerator('from')))->export(storage_path('Reports_'.time().'.xlsx'));

        return response()->download($file_name);
    }

    /**
     * @return RedirectResponse|BinaryFileResponse
     * @throws AuthorizationException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportReceive()
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('customer.reports.all')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view_reports');

        $file_name = (new FastExcel($this->reportsGenerator('to')))->export(storage_path('Reports_'.time().'.xlsx'));

        return response()->download($file_name);
    }

    /**
     * @return RedirectResponse|BinaryFileResponse
     * @throws AuthorizationException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportCampaign(Campaigns $campaign)
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('customer.reports.all')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view_reports');

        $file_name = (new FastExcel($this->campaignReportsGenerator($campaign->id)))->export(storage_path('Reports_'.time().'.xlsx'));

        return response()->download($file_name);
    }

    /**
     * @return Generator
     */

    public function campaignGenerator(): Generator
    {
        foreach (Campaigns::where('user_id', Auth::user()->id)->cursor() as $report) {
            yield $report;
        }
    }

    /**
     * @return RedirectResponse|BinaryFileResponse
     * @throws AuthorizationException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function campaignExport()
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('customer.reports.all')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view_reports');

        $file_name = (new FastExcel($this->campaignGenerator()))->export(storage_path('Campaign_'.time().'.xlsx'));

        return response()->download($file_name);
    }


    /**
     * delete campaign
     *
     * @param  Campaigns  $campaign
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function campaignDelete(Campaigns $campaign): JsonResponse
    {
        if (config('app.env') == 'demo') {

            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if ( ! $campaign->delete()) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.campaigns.campaign_was_successfully_deleted'),
        ]);

    }


    /**
     * bulk campaign delete
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function campaignBatchAction(Request $request): JsonResponse
    {

        if (config('app.env') == 'demo') {

            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $ids = $request->get('ids');

        if (Campaigns::whereIn('uid', $ids)->where('user_id', auth()->user()->id)->delete()) {
            return response()->json([
                    'status'  => 'success',
                    'message' => __('locale.campaigns.campaign_was_successfully_deleted'),
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }


    public function viewCharts()
    {
        $breadcrumbs = [
                ['link' => url("/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['name' => __('locale.menu.View Charts')],
        ];

        $sms_outgoing = Reports::currentMonth()
                ->where('user_id', Auth::user()->id)
                ->selectRaw('Day(created_at) as day, count(send_by) as outgoing,send_by')
                ->where('send_by', "from")
                ->groupBy('day')->pluck('day', 'outgoing')->flip()->sortKeys();

        $sms_incoming = Reports::currentMonth()
                ->where('user_id', Auth::user()->id)
                ->selectRaw('Day(created_at) as day, count(send_by) as incoming,send_by')
                ->where('send_by', "to")
                ->groupBy('day')->pluck('day', 'incoming')->flip()->sortKeys();


        $outgoing = (new LarapexChart)->lineChart()
                ->addData(__('locale.labels.outgoing'), $sms_outgoing->values()->toArray())
                ->setXAxis($sms_outgoing->keys()->toArray());


        $incoming = (new LarapexChart)->lineChart()
                ->addData(__('locale.labels.incoming'), $sms_incoming->values()->toArray())
                ->setXAxis($sms_incoming->keys()->toArray());


        return view('customer.Reports.charts', compact('breadcrumbs', 'sms_incoming', 'sms_outgoing', 'outgoing', 'incoming'));
    }

}
