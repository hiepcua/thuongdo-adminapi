<?php


namespace App\Models\Traits;


use App\Helpers\MediaHelper;

trait AvatarAttribute
{
    /**
     * @param $value
     * @return string
     */
    public function getAvatarAttribute($value): ?string
    {
        return MediaHelper::getUrl($value);
    }
}