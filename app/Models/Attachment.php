<?php

namespace App\Models;

use App\Helpers\MediaHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Attachment
 * @package App\Models
 *
 * @property $id
 */
class Attachment extends BaseModel
{
    use HasFactory;

    protected $appends = [
        'url', 'type'
    ];

    public function getUrlAttribute(): string
    {
        return MediaHelper::getUrl($this->id);
    }

    public function getTypeAttribute(): string
    {
        if (strpos($this->mime_type, 'video') !== false) {
            return 'video';
        }
        if (strpos($this->mime_type, 'image') !== false) {
            return 'image';
        }
        return 'file';
    }
}
