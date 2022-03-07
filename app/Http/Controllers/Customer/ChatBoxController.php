<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatBox\SentRequest;
use App\Models\Campaigns;
use App\Models\ChatBox;
use App\Models\ChatBoxMessage;
use App\Models\PhoneNumbers;
use App\Repositories\Contracts\CampaignRepository;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatBoxController extends Controller
{

    protected $campaigns;

    /**
     * ChatBoxController constructor.
     *
     * @param  CampaignRepository  $campaigns
     */
    public function __construct(CampaignRepository $campaigns)
    {
        $this->campaigns = $campaigns;
    }

    /**
     * get all chat box
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('chat_box');

        $pageConfigs = [
                'pageHeader'    => false,
                'contentLayout' => "content-left-sidebar",
                'bodyClass'     => 'chat-application',
        ];

        $chat_box = ChatBox::where('user_id', Auth::user()->id)->orderBy('updated_at', 'desc')->cursor();

        return view('customer.ChatBox.index', [
                'pageConfigs' => $pageConfigs,
                'chat_box'    => $chat_box,
        ]);
    }

    /**
     * start new conversion
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function new()
    {
        $this->authorize('chat_box');

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('chat-box'), 'name' => __('locale.menu.Chat Box')],
                ['name' => __('locale.labels.new_conversion')],
        ];

        $phone_numbers = PhoneNumbers::where('user_id', Auth::user()->id)->where('status', 'assigned')->cursor();

        return view('customer.ChatBox.new', compact('breadcrumbs', 'phone_numbers'));
    }


    /**
     * start new conversion
     *
     * @param  Campaigns  $campaign
     * @param  SentRequest  $request
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function sent(Campaigns $campaign, SentRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('customer.chatbox.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('chat_box');

        $data = $this->campaigns->quickSend($campaign, $request->except('_token'));

        if (isset($data)) {
            if ($data->getData()->status == 'success') {

                $chatbox = ChatBox::where('user_id', Auth::user()->id)->where('from', $request->sender_id)->where('to', $request->recipient)->first();

                if ( ! $chatbox) {

                    $chatbox = ChatBox::create([
                            'user_id'      => Auth::user()->id,
                            'from'         => $request->sender_id,
                            'to'           => $request->recipient,
                            'notification' => 0,
                    ]);
                }


                if ($chatbox) {
                    ChatBoxMessage::create([
                            'box_id'  => $chatbox->id,
                            'message' => $request->message,
                            'send_by' => 'from',
                    ]);

                    $chatbox->touch();

                    return redirect()->route('customer.chatbox.index')->with([
                            'status'  => $data->getData()->status,
                            'message' => $data->getData()->message,
                    ]);
                }

                return redirect()->route('customer.chatbox.index')->with([
                        'status'  => $data->getData()->status,
                        'message' => $data->getData()->message,
                ]);
            }

            return redirect()->route('customer.chatbox.index')->with([
                    'status'  => $data->getData()->status,
                    'message' => $data->getData()->message,
            ]);
        }

        return redirect()->route('customer.chatbox.index')->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }

    /**
     * get chat messages
     *
     * @param  ChatBox  $box
     *
     * @return JsonResponse
     */
    public function messages(ChatBox $box): JsonResponse
    {
        $box->update([
                'notification' => 0,
        ]);

        $data = ChatBoxMessage::where('box_id', $box->id)->select('message', 'send_by')->cursor()->toJson();

        return response()->json([
                'status' => 'success',
                'data'   => $data,
        ]);

    }

    /**
     * reply message
     *
     * @param  ChatBox  $box
     * @param  Campaigns  $campaign
     * @param  Request  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function reply(ChatBox $box, Campaigns $campaign, Request $request): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('chat_box');

        if (empty($request->message)) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.insert_your_message'),
            ]);
        }

        $input = [
                'sender_id' => $box->from,
                'recipient' => $box->to,
                'sms_type'  => 'plain',
                'message'   => $request->message,
        ];

        $data = $this->campaigns->quickSend($campaign, $input);

        if (isset($data)) {
            if ($data->getData()->status == 'success') {

                ChatBoxMessage::create([
                        'box_id'  => $box->id,
                        'message' => $request->message,
                        'send_by' => 'from',
                ]);

                $box->touch();

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.campaigns.message_successfully_delivered'),
                ]);
            }

            return response()->json([
                    'status'  => $data->getData()->status,
                    'message' => $data->getData()->message,
            ]);

        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }
}
