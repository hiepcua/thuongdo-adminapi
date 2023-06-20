<?php


namespace App\Services;


use App\Models\ReportDashboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class ReportDashboardService implements Service
{

    /**
     * @param  Carbon  $startDate
     * @param  Carbon  $endDate
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(Carbon $startDate, Carbon $endDate): JsonResponse
    {
        if ($startDate->greaterThan($endDate)) {
            throw new \Exception(trans('dashboard.start_must_less_than_end_date'));
        }
        $data = ReportDashboard::query()->selectRaw($this->generateQuery())->first();
        return resSuccessWithinData($data);
    }

    /**
     * @return string
     */
    private function generateQuery(): string
    {
        $query = '';
        $columns = [
            'customers_numbers',
            'customers_ordered_numbers',
            'customers_has_some_orders_numbers',
            'orders_numbers',
            'orders_done_numbers',
            'orders_complain_numbers'
        ];
        foreach ($columns as $column) {
            $query .= "CAST(IFNULL(SUM({$column}), 0) AS UNSIGNED) as {$column},";
        }
        return rtrim($query, ',');
    }
}