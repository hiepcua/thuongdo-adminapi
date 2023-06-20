<?php


namespace App\Helpers;


class QueryAppend
{
    /**
     * @param $query
     * @param $column
     * @param $value
     * @param  string  $condition
     * @param  string  $logic
     */
    private static function prepareFilter(&$query, $column, $value, $condition = 'where', $logic = '=')
    {
        if (is_array($column)) {
            foreach ($column as $key => $item) {
                $query->{$condition}($item, $logic, is_array($value) ? $value[$key] : $value);
            }
            return;
        }
        $query->{$condition}($column, $logic, $value);
    }

    /**
     * @param $query
     * @param $column
     * @param $value
     * @param  string  $logic
     */
    public static function filterWhere(&$query, $column, $value, $logic = '=')
    {
        self::prepareFilter($query, $column, $value, 'where', $logic);
    }

    /**
     * @param $query
     * @param $column
     * @param $value
     * @param  string  $logic
     */
    public static function filterOrWhere(&$query, $column, $value, $logic = '=')
    {
        self::prepareFilter($query, $column, $value, 'orWhere', $logic);
    }
}