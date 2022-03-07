<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Role.
 *
 * @property int $id
 * @property string $name
 * @property array $display_name
 * @property array $description
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property mixed $can_delete
 * @property mixed $can_edit
 * @property Collection|Permission[] $permissions
 *
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereDescription($value)
 * @method static Builder|Role whereDisplayName($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereOrder($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @method static whereLike(string[] $array, mixed $search)
 * @mixin Eloquent
 */
class Role extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'name',
            'status',
    ];

    /**
     * The relationship that are eager loaded.
     *
     * @var array
     */
    protected $with = [
            'permissions',
    ];

    protected $appends = ['can_edit', 'can_delete'];

    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($item) {
            // Create new uid
            $uid = uniqid();
            while (self::where('uid', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
    }


    /**
     * show role name
     *
     * @return string
     */
    public function display_name(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getCanEditAttribute(): bool
    {
        return true;
    }

    public function getCanDeleteAttribute(): bool
    {
        return Gate::check('delete roles');
    }

    /**
     * Many-to-Many relations with Role.
     *
     * @return HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    /**
     * get roles admin
     *
     * @return HasMany
     */
    public function admins(): HasMany
    {
        return $this->hasMany(RoleUser::class);
    }

    /**
     * get permissions attribute
     *
     * @return mixed
     */
    public function getPermissionsAttribute()
    {
        return $this->permissions()->getResults()->pluck('name')->toArray();
    }

    /**
     * get route key by uid
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uid';
    }


    /**
     * display route
     *
     * @return array|string
     */

    public function __toString()
    {
        return $this->display_name ?: $this->name;
    }

}
