<?php


namespace App\Models\Traits;


use App\Helpers\MediaHelper;
use Illuminate\Support\Str;

trait ImageAttribute
{
    /**
     * @param $value
     * @return string
     */
    public function getImageAttribute($value): ?string
    {
        
        if (Str::isUuid($value)) {
            return MediaHelper::getDomain($value);
        }
        return MediaHelper::getFullUrlByValue($value);
    }
}