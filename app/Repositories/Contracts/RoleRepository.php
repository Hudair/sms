<?php

namespace App\Repositories\Contracts;

use App\Models\Role;
use Illuminate\Support\Collection;

/**
 * Interface RoleRepository.
 */
interface RoleRepository extends BaseRepository
{
    /**
     * @param array $input
     *
     * @return mixed|Role
     */
    public function store(array $input);

    /**
     * @param Role  $role
     * @param array $input
     *
     * @return mixed|Role
     */
    public function update(Role $role, array $input);

    /**
     * @param Role $role
     *
     * @return mixed
     */
    public function destroy(Role $role);

    /**
     * @return Collection
     */
    public function getAllowedRoles();


    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function batchDestroy(array $ids);

    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function batchActive(array $ids);

    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function batchDisable(array $ids);


}
