<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Laravel\Sanctum\HasApiTokens;


/**
 * @method static where(string $string, bool $true)
 * @method getProvider($provider)
 * @method providers()
 * @method truncate()
 * @method create(array $array)
 * @method static find($end_by)
 * @property mixed is_admin
 * @property mixed first_name
 * @property mixed last_name
 * @property mixed id
 * @property mixed roles
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'first_name',
            'last_name',
            'api_token',
            'password',
            'image',
            'email',
            'status',
            'is_admin',
            'is_customer',
            'active_portal',
            'two_factor',
            'two_factor_code',
            'two_factor_expires_at',
            'locale',
            'sms_unit',
            'timezone',
            'provider',
            'provider_id',
            'email_verified_at',
            'two_factor_backup_code',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
            'last_access_at',
            'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
            'password',
            'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
            'is_admin'    => 'boolean',
            'is_customer' => 'boolean',
            'status'      => 'boolean',
            'two_factor'  => 'boolean',
    ];

    /**
     * Find item by uid.
     *
     * @param $uid
     *
     * @return object
     */
    public static function findByUid($uid): object
    {
        return self::where('uid', $uid)->first();
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function systemJobs(): HasMany
    {
        return $this->hasMany(SystemJob::class)->orderBy('created_at', 'desc');
    }

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

            if (config('app.two_factor')) {
                $item->two_factor_backup_code = self::generateTwoFactorBackUpCode();
            }

        });
    }

    /**
     * Check if user has admin account.
     */
    public function isAdmin(): bool
    {
        return 1 == $this->is_admin;
    }

    /**
     * Check if user has admin account.
     */
    public function isCustomer(): bool
    {
        return 1 == $this->is_customer;
    }

    /*
     *  Display User Name
     */
    public function displayName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * generate two factor code
     */
    public function generateTwoFactorCode()
    {
        $this->timestamps            = false;
        $this->two_factor_code       = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    /**
     * Reset two factor code
     */
    public function resetTwoFactorCode()
    {
        $this->timestamps            = false;
        $this->two_factor_code       = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    /**
     * generate two factor backup code
     *
     * @return false|string
     */
    public static function generateTwoFactorBackUpCode()
    {
        $backUpCode = [];
        for ($i = 0; $i < 8; $i++) {
            $backUpCode[] = rand(100000, 999999);
        }

        return json_encode($backUpCode);
    }

    /**
     * Upload and resize avatar.
     *
     * @return string
     * @var void
     */
    public function uploadImage($file): string
    {
        $path        = 'app/profile/';
        $upload_path = storage_path($path);

        if ( ! file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $filename = 'avatar-'.$this->id.'.'.$file->getClientOriginalExtension();

        // save to server
        $file->move($upload_path, $filename);

        // create thumbnails
        $img = Image::make($upload_path.$filename);

        $img->fit(120, 120, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        })->save($upload_path.$filename.'.thumb.jpg');

        return $path.$filename;
    }


    /**
     * Get image thumb path.
     *
     * @return string
     * @var string
     */
    public function imagePath(): string
    {
        if ( ! empty($this->image) && ! empty($this->id)) {
            return storage_path($this->image).'.thumb.jpg';
        } else {
            return '';
        }
    }

    /**
     * Get image thumb path.
     *
     * @var string
     */
    public function removeImage()
    {
        if ( ! empty($this->image) && ! empty($this->id)) {
            $path = storage_path($this->image);
            if (is_file($path)) {
                unlink($path);
            }
            if (is_file($path.'.thumb.jpg')) {
                unlink($path.'.thumb.jpg');
            }
        }
    }


    public function getCanEditAttribute(): bool
    {
        return 1 === auth()->id();
    }

    public function getCanDeleteAttribute(): bool
    {
        return $this->id !== auth()->id() && (Gate::check('delete customer'));
    }


    public function getIsSuperAdminAttribute(): bool
    {
        return 1 === $this->id;
    }

    /**
     * Many-to-Many relations with Role.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasRole($name): bool
    {
        return $this->roles->contains('name', $name);
    }


    /**
     * @return Collection
     */

    public function getPermissions(): Collection
    {
        $permissions = [];

        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                if ( ! in_array($permission, $permissions, true)) {
                    $permissions[] = $permission;
                }
            }
        }

        return collect($permissions);
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

}
