<?php


namespace App\Http\Controllers\API;

use App\Http\Requests\Contacts\NewContactGroup;
use App\Http\Requests\Contacts\StoreContact;
use App\Http\Requests\Contacts\UpdateContactGroup;
use App\Models\ContactGroups;
use App\Models\Contacts;
use App\Models\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ContactsRepository;
use Illuminate\Http\JsonResponse;

class ContactsController extends Controller
{
    use ApiResponser;

    /**
     * @var ContactsRepository $contactGroups
     */
    protected $contactGroups;

    public function __construct(ContactsRepository $contactGroups)
    {
        $this->contactGroups = $contactGroups;
    }


    /**
     * invalid api endpoint request
     *
     * @return JsonResponse
     */
    public function contacts(): JsonResponse
    {
        return $this->error(__('locale.exceptions.invalid_action'), 403);
    }

    /*
    |--------------------------------------------------------------------------
    | contact module
    |--------------------------------------------------------------------------
    |
    |
    |
    */


    /**
     * store new contact
     *
     * @param  ContactGroups  $group_id
     * @param  StoreContact  $request
     *
     * @return JsonResponse
     */
    public function storeContact(ContactGroups $group_id, StoreContact $request): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $data = $this->contactGroups->storeContact($group_id, $request->only('phone', 'first_name', 'last_name'));

        return $this->success($data->getData()->contact, $data->getData()->message);
    }


    /**
     * view a contact
     *
     * @param  ContactGroups  $group_id
     * @param  Contacts  $uid
     *
     * @return JsonResponse
     */
    public function searchContact(ContactGroups $group_id, Contacts $uid): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if (request()->user()->tokenCan('view_contact')) {

            $data = Contacts::where('group_id', $group_id->id)->select('uid', 'phone', 'first_name', 'last_name')->where('uid', $uid->uid)->first();

            return $this->success($data);
        }

        return $this->error(__('locale.http.403.description'), 403);
    }

    /**
     * update a contact
     *
     * @param  ContactGroups  $group_id
     * @param  Contacts  $uid
     * @param  StoreContact  $request
     *
     * @return JsonResponse
     */
    public function updateContact(ContactGroups $group_id, Contacts $uid, StoreContact $request): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $input               = $request->only('phone', 'first_name', 'last_name');
        $input['contact_id'] = $uid->uid;

        $status = $this->contactGroups->updateContact($group_id, $input);

        if ($status) {
            $data = Contacts::find($uid->id);

            return $this->success($data, __('locale.contacts.contact_successfully_updated'));
        }

        return $this->error(__('locale.http.404.description'), 404);

    }

    /**
     * delete contact
     *
     * @param  ContactGroups  $group_id
     * @param  Contacts  $uid
     *
     * @return JsonResponse
     */
    public function deleteContact(ContactGroups $group_id, Contacts $uid): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if (request()->user()->tokenCan('delete_contact')) {

            $status = $this->contactGroups->contactDestroy($group_id, ['uid' => $uid->uid]);

            if ($status) {
                return $this->success(null, __('locale.contacts.contact_successfully_deleted'));
            }

            return $this->error(__('locale.exceptions.something_went_wrong'), 404);
        }

        return $this->error(__('locale.http.403.description'), 403);
    }


    /**
     * get all contacts from a group
     *
     * @param  ContactGroups  $group_id
     *
     * @return JsonResponse
     */
    public function allContact(ContactGroups $group_id): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }
        if (request()->user()->tokenCan('view_contact')) {
            $data = Contacts::where('group_id', $group_id->id)->select('uid', 'phone', 'first_name', 'last_name')->paginate(25);

            return $this->success($data);
        }

        return $this->error(__('locale.http.403.description'), 403);
    }


    /*
    |--------------------------------------------------------------------------
    | contact group module
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    /**
     * view all contact groups
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if (request()->user()->tokenCan('view_contact_group')) {

            $data = ContactGroups::where('customer_id', request()->user()->id)->select('uid', 'name')->paginate(25);

            return $this->success($data);
        }

        return $this->error(__('locale.http.403.description'), 403);
    }


    /**
     * store contact group
     *
     * @param  NewContactGroup  $request
     *
     * @return JsonResponse
     */

    public function store(NewContactGroup $request): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $group = $this->contactGroups->store($request->input());

        if ($group) {
            return $this->success($group->select('name', 'uid')->find($group->id), __('locale.contacts.contact_group_successfully_added'));
        }

        return $this->error(__('locale.exceptions.something_went_wrong'), 403);

    }


    /**
     * view a group
     *
     * @param  ContactGroups  $group_id
     *
     * @return JsonResponse
     */
    public function show(ContactGroups $group_id): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if (request()->user()->tokenCan('view_contact_group')) {
            $data = ContactGroups::select('uid', 'name')->find($group_id->id);

            return $this->success($data);
        }

        return $this->error(__('locale.http.403.description'), 403);
    }


    /**
     * update contact group
     *
     * @param  ContactGroups  $contact
     * @param  UpdateContactGroup  $request
     *
     * @return JsonResponse
     */

    public function update(ContactGroups $contact, UpdateContactGroup $request): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $group = $this->contactGroups->update($contact, $request->input());

        if ($group) {
            return $this->success($group->select('name', 'uid')->find($contact->id), __('locale.contacts.contact_group_successfully_updated'));
        }

        return $this->error(__('locale.exceptions.something_went_wrong'), 403);

    }

    /**
     * delete contact group
     *
     * @param  ContactGroups  $contact
     *
     * @return JsonResponse
     */
    public function destroy(ContactGroups $contact): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if (request()->user()->tokenCan('delete_contact_group')) {

            $this->contactGroups->destroy($contact);

            return $this->success(null, __('locale.contacts.contact_group_successfully_deleted'));

        }

        return $this->error(__('locale.http.403.description'), 403);
    }

}
