<?php

namespace App\Models;

use App\Scopes\Traits\Filterable;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as PermissionContract;
use Spatie\Permission\Traits\HasRoles;

class Permission extends PermissionContract
{
    use HasFactory, Filterable, HasRoles, Uuid;

    protected $guarded = ['id'];

    public function module()
    {
        return $this->hasOne(Module::class, 'id', 'module_id');
    }

    public function scopeExceptModuleName($query)
    {
        return $query->whereHas(
            'module',
            function ($q) {
                $q->where('name', '!=', request()->query('except_module_name'));
            }
        );
    }

    /**
     * @var string
     */
    protected string $_tableNameFriendly = 'Bản ghi';

    /**
     * Lấy tên bảng dạng thân thiện để hiển thị message
     * @return string
     */
    public function getTableFriendly(): string
    {
        return $this->_tableNameFriendly;
    }

    /**
     * Get prefix route thay the cho model
     * @return ?string
     */
    public function getPrefixRoute(): ?string
    {
        return $this->_prefixRoute ?? null;
    }
}
