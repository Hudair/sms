<?php

namespace App\Repositories\Eloquent;

use Exception;
use App\Models\Role;
use App\Exceptions\GeneralException;
use App\Repositories\Contracts\RoleRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class EloquentRoleRepository.
 */
class EloquentRoleRepository extends EloquentBaseRepository implements RoleRepository
{
    /**
     * EloquentRoleRepository constructor.
     *
     * @param  Role  $role
     */
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    /**
     * @param  array  $input
     *
     * @return Role
     * @throws Exception|Throwable
     *
     */
    public function store(array $input): Role
    {
        /** @var Role $role */
        $role = $this->make($input);

        if ( ! $this->save($role, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $role;
    }

    /**
     * @param  Role  $role
     * @param  array  $input
     *
     * @return Role
     * @throws Exception|Throwable
     *
     * @throws Exception
     */
    public function update(Role $role, array $input): Role
    {
        $role->fill($input);

        if ( ! $this->save($role, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $role;
    }

    /**
     * @param  Role  $role
     * @param  array  $input
     *
     * @return bool
     *
     */
    private function save(Role $role, array $input): bool
    {
        if ( ! $role->save($input)) {
            return false;
        }

        $role->permissions()->delete();

        $permissions = $input['permissions'] ?? [];

        foreach ($permissions as $name) {
            $role->permissions()->create(['name' => $name]);
        }

        return true;
    }

    /**
     * @param  Role  $role
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(Role $role)
    {
        $role->permissions()->delete();

        if ( ! $role->delete()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;
    }

    /**
     * Get only roles than current can attribute to the others.
     */
    public function getAllowedRoles()
    {
        $authenticatedUser = auth()->user();

        if ( ! $authenticatedUser) {
            return [];
        }

        $roles = $this->query()->with('permissions')->get();

        if ($authenticatedUser->is_super_admin) {
            return $roles;
        }

        /** @var Collection $permissions */
        $permissions = $authenticatedUser->getPermissions();

        $roles = $roles->filter(function (Role $role) use ($permissions) {
            foreach ($role->permissions as $permission) {
                if (false === $permissions->search($permission, true)) {
                    return false;
                }
            }

            return true;
        });

        return $roles;
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
            if ($this->query()->whereIn('uid', $ids)->delete()) {
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
    public function batchActive(array $ids): bool
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
