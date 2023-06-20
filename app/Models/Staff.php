<?php

namespace App\Models;

use App\Models\Traits\AvatarAttribute;
use App\Models\Traits\ImageAttribute;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasSortDescByCreated;
use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasPermissions;
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
class Staff extends BaseModel
{
    use Filterable, HasFactory, Notifiable, HasOrganization, HasRoles, HasSortDescByCreated, AvatarAttribute, HasPermissions, AvatarAttribute;

    protected $table = 'users';

    protected string $_prefixRoute = 'user';

    protected $guard_name = 'api';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'blocked_at' => 'datetime',
    ];

    protected string $_tableNameFriendly = 'Nhân viên';

    public function scopeName($query)
    {
        return $query->where("name", 'like', '%'.request()->query('name').'%');
    }

    public function scopeDepartmentId($query)
    {
        return $query->where("department_id", request()->query('department_id'));
    }

    public function scopeRoleName($query) {
        return $query->whereHas('roles', function($q) {
            $q->where('name', request()->query('role_name'));
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
