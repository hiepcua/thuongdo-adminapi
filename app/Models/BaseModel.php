<?php

namespace App\Models;

use App\Scopes\Traits\HasOrganization;
use App\Scopes\Traits\HasSortDescUuid;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use Uuid, HasFactory, HasOrganization, HasSortDescUuid;
    
    protected $guarded = ['id'];

    protected $keyType = 'string';

    protected string $_colorLog = '#2B75CC';

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

    public function getColorLog(): string
    {
        return $this->_colorLog;
    }
}
