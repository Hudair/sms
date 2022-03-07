<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Helpers\Helper;
use App\Models\Customer;
use App\Models\RoleUser;
use App\Models\User;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Throwable;


class EloquentUserRepository extends EloquentBaseRepository implements UserRepository
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var RoleRepository
     */
    protected $roles;

    /**
     * EloquentUserRepository constructor.
     *
     * @param  User  $user
     * @param  RoleRepository  $roles
     * @param  Repository  $config
     */
    public function __construct(
            User $user,
            RoleRepository $roles,
            Repository $config
    ) {
        parent::__construct($user);
        $this->roles  = $roles;
        $this->config = $config;
    }

    /**
     * @param  array  $input
     * @param  bool  $confirmed
     *
     * @return User
     * @throws GeneralException
     * @throws Exception
     *
     */
    public function store(array $input, $confirmed = false): User
    {
        /** @var User $user */
        $user = $this->make(Arr::only($input, ['first_name', 'last_name', 'email', 'status', 'phone']));

        if (empty($user->locale)) {
            $user->locale = $this->config->get('app.locale');
        }

        if (empty($user->timezone)) {
            $user->timezone = $this->config->get('app.timezone');
        }

        if (isset($input['is_customer'])) {
            $user->is_customer   = true;
            $user->active_portal = 'customer';
        }

        if (isset($input['is_admin'])) {
            $user->is_admin          = true;
            $user->active_portal     = 'admin';
            $user->email_verified_at = Carbon::now();
        } else {
            $user->is_admin = false;

            if ( ! config('account.verify_account')) {
                $user->email_verified_at = Carbon::now();
            }
        }


        if ( ! $this->save($user, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        if (isset($input['is_customer'])) {
            $customer = Customer::create([
                    'user_id'       => $user->id,
                    'phone'         => $input['phone'],
                    'notifications' => json_encode([
                            'login'        => 'no',
                            'tickets'      => 'yes',
                            'sender_id'    => 'yes',
                            'keyword'      => 'yes',
                            'subscription' => 'yes',
                            'promotion'    => 'yes',
                            'profile'      => 'yes',
                    ]),
            ]);

            if ($customer) {
                $permissions     = json_decode($user->customer->permissions, true);
                $user->api_token = $user->createToken($input['email'], $permissions)->plainTextToken;
                $user->save();

                return $user;
            }
            $user->delete();
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));

        }

        //  event(new UserCreated($user));

        return $user;
    }

    /**
     * @param  User  $user
     * @param  array  $input
     *
     * @return User
     * @throws Exception|Throwable
     *
     * @throws Exception
     */
    public function update(User $user, array $input): User
    {
        if ( ! $user->can_edit) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        $user->fill(Arr::except($input, 'password'));

        if ($user->is_super_admin && ! $user->active) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        if ( ! $this->save($user, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        //  event(new UserUpdated($user));

        return $user;
    }

    /**
     * @param  User  $user
     * @param  array  $input
     *
     * @return bool
     * @throws GeneralException
     *
     */
    private function save(User $user, array $input): bool
    {
        if (isset($input['password']) && ! empty($input['password'])) {
            $user->password = Hash::make($input['password']);
        }

        if ( ! $user->save()) {
            return false;
        }

        $roles = $input['roles'] ?? [];

        if ( ! empty($roles)) {
            $allowedRoles = $this->roles->getAllowedRoles()->keyBy('id');

            foreach ($roles as $id) {
                if ( ! $allowedRoles->has($id)) {
                    throw new GeneralException(__('locale.exceptions.something_went_wrong'));
                }
            }
        }

        $user->roles()->sync($roles);

        return true;
    }

    /**
     * @param  User  $user
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(User $user)
    {
        if ( ! $user->can_delete) {
            throw new GeneralException(__('locale.exceptions.unauthorized'));
        }

        if ( ! $user->delete()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

//        event(new UserDeleted($user));

        return true;
    }

    /**
     * @param  User  $user
     *
     * @return RedirectResponse
     * @throws Exception
     *
     */
    public function impersonate(User $user): RedirectResponse
    {
        if ($user->is_super_admin) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        $authenticatedUser = auth()->user();

        if ($authenticatedUser->id === $user->id
                || Session::get('admin_user_id') === $user->id
        ) {
            return redirect()->route('admin.home');
        }

        if ( ! Session::get('admin_user_id')) {
            session(['admin_user_id' => $authenticatedUser->id]);
            session(['admin_user_name' => $authenticatedUser->name]);
            session(['temp_user_id' => $user->id]);
        }

        //Login user
        auth()->loginUsingId($user->id);

        return redirect(Helper::home_route());
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchDestroy(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            // This wont call eloquent events, change to destroy if needed
            foreach ($this->query()->whereIn('uid', $ids)->cursor() as $administrator) {
                RoleUser::where('user_id', $administrator->id)->delete();
                Customer::where('user_id', $administrator->id)->delete();
                $administrator->delete();
            }
        });

        return true;
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchEnable(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => true])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchDisable(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => false])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

}
