<?php

namespace App\Repositories\Contracts;

use App\Models\User;

/**
 * Interface CustomerRepository.
 */
interface CustomerRepository extends BaseRepository
{

    /**
     * @param  array  $input
     * @param  bool  $confirmed
     *
     * @return mixed
     */
    public function store(array $input, $confirmed = false);

    /**
     * @param  User  $customer
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(User $customer, array $input);

    /**
     * @param  User  $customer
     * @param  array  $input
     *
     * @return mixed
     */
    public function updateInformation(User $customer, array $input);

    /**
     * @param  User  $customer
     * @param  array  $input
     *
     * @return mixed
     */
    public function permissions(User $customer, array $input);

    /**
     * @param  User  $customer
     *
     * @return mixed
     */
    public function destroy(User $customer);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchEnable(array $ids);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchDisable(array $ids);

}
