<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends BaseModel
{
    use HasFactory;

    protected $table = 'role_has_permissions';

    protected $primaryKey = 'permission_id';

    public $timestamps = false;

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'id', 'permission_id');
    }


}
