<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;

use App\Models\User;
use App\Repositories\Contracts\CustomerRepository;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;


/**
 * Class EloquentCustomerRepository.
 */
class EloquentCustomerRepository extends EloquentBaseRepository implements CustomerRepository
{


    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * EloquentCustomerRepository constructor.
     *
     * @param  User  $user
     * @param  Repository  $config
     */
    public function __construct(User $user, Repository $config)
    {
        parent::__construct($user);
        $this->config = $config;
    }

    /**
     * @param  array  $input
     * @param  bool  $confirmed
     *
     * @return User
     * @throws GeneralException
     *
     */
    public function store(array $input, $confirmed = false): User
    {

        /** @var User $user */
        $user = $this->make(Arr::only($input, ['first_name', 'last_name', 'email', 'status', 'timezone', 'locale']));

        if (empty($user->locale)) {
            $user->locale = $this->config->get('app.locale');
        }

        if (empty($user->timezone)) {
            $user->timezone = $this->config->get('app.timezone');
        }

        $user->email_verified_at = now();
        $user->is_admin          = false;
        $user->is_customer       = true;

        if ( ! $this->save($user, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        Customer::create([
                'user_id'       => $user->id,
                'phone'         => $input['phone'],
                'notifications' => json_encode([
                        'login'        => 'no',
                        'sender_id'    => 'yes',
                        'keyword'      => 'yes',
                        'subscription' => 'yes',
                        'promotion'    => 'yes',
                        'profile'      => 'yes',
                ]),
        ]);

        return $user;
    }


    /**
     * @param  User  $customer
     * @param  array  $input
     *
     * @return User|mixed
     * @throws GeneralException
     *
     */
    public function update(User $customer, array $input): User
    {

        $customer->fill(Arr::except($input, 'password'));

        if ( ! $this->save($customer, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $customer;
    }

    /**
     * @param  User  $user
     * @param  array  $input
     *
     * @return bool
     */
    private function save(User $user, array $input): bool
    {
        if (isset($input['password']) && ! empty($input['password'])) {
            $user->password = Hash::make($input['password']);
        }

        if ( ! $user->save()) {
            return false;
        }

        return true;
    }

    /**
     * update user information
     *
     * @param  User  $user
     * @param  array  $input
     *
     * @return User
     * @throws GeneralException
     */
    public function updateInformation(User $user, array $input): User
    {
        $customer = Customer::where('user_id', $user->id)->first();

        if ( ! $customer) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        if (isset($input['notifications']) && count($input['notifications']) > 0) {

            $defaultNotifications = [
                    'login'        => 'no',
                    'sender_id'    => 'no',
                    'keyword'      => 'no',
                    'subscription' => 'no',
                    'promotion'    => 'no',
                    'profile'      => 'no',
            ];

            $notifications          = array_merge($defaultNotifications, $input['notifications']);
            $input['notifications'] = json_encode($notifications);
        }

        $data = $customer->update($input);

        if ( ! $data) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $user;
    }


    /**
     * update permissions
     *
     * @param  User  $user
     * @param  array  $input
     *
     * @return User|mixed
     * @throws GeneralException
     */
    public function permissions(User $user, array $input): User
    {
        $data = array_values($input['permissions']);

        $status = $user->customer()->update([
                'permissions' => json_encode($data),
        ]);

        if ( ! $status) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $user;
    }


    /**
     * @param  User  $customer
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(User $customer)
    {
        if ( ! $customer->can_delete) {
            throw new GeneralException(__('exceptions.backend.users.first_user_cannot_be_destroyed'));
        }

        if ( ! $customer->delete()) {
            throw new GeneralException(__('exceptions.backend.users.delete'));
        }

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

            throw new GeneralException(__('exceptions.backend.users.update'));
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

            throw new GeneralException(__('exceptions.backend.users.update'));
        });

        return true;
    }

}
