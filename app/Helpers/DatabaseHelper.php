<?php


namespace App\Helpers;


use App\Models\Organization;

class DatabaseHelper
{
    /**
     * @param  array  $data
     * @param  bool|null  $isOrganization
     * @return array
     */
    public static function getData(array $data, ?bool $isOrganization = false): array
    {
        $result = [];
        foreach ($data as $item) {
            $tmp = [
                'id' => getUuid(),
                'name' => $item,
                'organization_id' => Organization::query()->first()->id
            ];
            if (!$isOrganization) {
                unset($tmp['organization_id']);
            }
            $result[] = $tmp;
        }
        return $result;
    }
}