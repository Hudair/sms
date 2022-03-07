<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $id)
 */
class RoleUser extends Model
{
    use HasFactory;

    protected $table = 'role_user';
}
