<?php


namespace App\Helpers;


use App\Constants\StatusConstant;
use App\Constants\TimeConstant;
use Illuminate\Support\Str;

class StatusHelper
{
    /**
     * @param  string  $status
     * @param  string|null  $class
     * @return array
     */
    public static function getInfo(string $status, ?string $class = StatusConstant::class): array
    {
        $index = static::getIndexKey($status);
        return [
            'value' => $status,
            'name' => $class::STATUSES[$status],
            'color' => array_values($class::STATUSES_COLOR)[$index]
        ];
    }

    /**
     * @param  string  $id
     * @param  string  $model
     * @param  string  $status
     * @return array
     */
    public static function getTime(string $id, string $model, string $status): array
    {
        $columnId = Str::snake(getModelNameByClass($model)).'_id';
        $statusTime = StatusConstant::TABLE_STATUS_TIMES[$columnId];
        return [
            'time' => optional(
                optional(
                    (new $statusTime)->where(
                        [$columnId => $id, 'key' => $status]
                    )->first()
                )->created_at
            )->format(TimeConstant::DATETIME_VI)
        ];
    }

    /**
     * @param  string  $id
     * @param  string  $model
     * @param  string  $const
     * @param  array  $statuses
     * @return array
     */
    public static function getStatuses(string $id, string $model, string $const, array $statuses): array
    {
        $data = [];
        foreach ($statuses as $status) {
            $data[] = [
                    'name' => $const::STATUSES[$status],
                ] + StatusHelper::getTime($id, $model, $status);
        }
        return $data;
    }

    /**
     * @param  string  $key
     * @return int
     */
    public static function getIndexKey(string $key): int
    {
        $array = explode('_', $key);
        return (int)array_pop($array);
    }
}