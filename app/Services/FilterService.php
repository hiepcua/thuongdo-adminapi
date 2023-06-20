<?php


namespace App\Services;


use App\Constants\TimeConstant;

class FilterService
{
    /**
     * @param $query
     * @param  string  $value
     * @param  string  $column
     * @return mixed
     */
    public function rangeDateFilter($query, string $value, string $column)
    {
        $dates = explode(',', $value);
        if (!$dates) {
            return $query;
        }
        if (($count = count($dates)) >= 2) {
            $dates = array_slice($dates, 0, 2);
        }
        if ($count == 1) {
            array_push($dates, TimeConstant::DATE_MAX);
        }
        return $query->whereDate($column, '>=', $dates[0])->whereDate($column, '<=', $dates[1]);
    }
}