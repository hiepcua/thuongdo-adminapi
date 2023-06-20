<?php


namespace App\Helpers;


class MediaHelper
{
    /**
     * @param  string|null  $value
     * @return string|null
     */
    public static function getFullUrlByValue(?string $value): ?string
    {
        return $value ? (config('app.media_url').'/storage'.$value) : $value;
    }

    /**
     * @param  string|null  $value
     * @return string
     */
    public static function getDomain(?string $value = null): ?string
    {
        if (!$value) {
            return $value;
        }
        return config('app.media_url')."/api/file/$value";
    }

    /**
     * @param $value
     * @return string
     */
    public static function getUrl(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }
        if (!is_string($value) || (preg_match(
                    '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
                    $value
                ) !== 1)) {
            return static::getFullUrlByValue($value);
        }
        return static::getDomain($value);
    }
}