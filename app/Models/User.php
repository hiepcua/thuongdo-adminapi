<?php

namespace App\Models;

use App\Models\Traits\AvatarAttribute;
use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App\Models
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $status
 * @property Carbon $blocked_at
 * @property int $login_failed
 * @property string $verify_code
 */
class User extends Authenticate
{
    use HasApiTokens, HasFactory, Notifiable, HasOrganization, HasRoles, AvatarAttribute;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guard_name = 'api';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'blocked_at' => 'datetime',
    ];

    protected string $_tableNameFriendly = 'Người dùng';
}
