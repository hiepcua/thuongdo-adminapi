<?php


namespace App\Services;

use App\Constants\TimeConstant;
use App\Models\ReportLevel;
use App\Models\ReportRevenue;
use Illuminate\Support\Carbon;

class ReportService implements Service
{
    /**
     * @param  string  $model
     * @param  string|null  $column
     * @return array
     */
    public function reports(string $model, ?string $column = 'created_at'): array
    {
        $query = (new $model)::query();
        return [
            'today' => (clone $query)->whereDate($column, date(TimeConstant::DATE))->count(),
            'week' => (clone $query)->whereBetween(
                $column,
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
            )->count(),
            'month' => (clone $query)->whereMonth($column, date('m'))->whereYear($column, date('Y'))->count()
        ];
    }

    /**
     * @param  int  $level
     * @param  int|null  $oldLevel
     */
    public function reportLevel(int $level, ?int $oldLevel = null): void
    {
        $query = ReportLevel::query()->where(
            [
                'organization_id' => request()->input('organization_id') ?? (new OrganizationService(
                    ))->getOrganizationDefault()
            ]
        );
        if (is_numeric($oldLevel)) {
            (clone $query)->where(['level' => $oldLevel])->where('quantity', '>', 0)->decrement('quantity');
        }
        (clone $query)->where(['level' => $level])->increment('quantity');
    }

    public function incrementByOrganization(string $model, string $column, ?int $number = 1, ?array $condition = [])
    {
        optional((new $model)::query()->where($condition)->first())->increment($column, $number);
    }

    public function decrementByOrganization(string $model, string $column, ?int $number = 1, ?array $condition = [])
    {
        optional((new $model)::query()->where($condition)->where($column, '>', 0)->first())->decrement(
            $column,
            $number
        );
    }

    public function inDecrementByOrganization(
        string $model,
        string $inColumn,
        string $deColumn,
        ?int $number = 1,
        ?array $condition = []
    ) {
        $this->incrementByOrganization($model, $inColumn, $number, $condition);
        $this->decrementByOrganization($model, $deColumn, $number, $condition);
    }

    /**
     * @param  float  $amount
     * @param  string  $key
     */
    public function incrementByReportRevenue(float $amount, string $key)
    {
        $this->storeReportRevenue($key);
        $this->incrementByOrganization(ReportRevenue::class, 'value', $amount, ['key' => $key]);
    }

    /**
     * @param  float  $amount
     * @param  string  $key
     */
    public function decrementByReportRevenue(float $amount, string $key)
    {
        $this->storeReportRevenue($key);
        $this->decrementByOrganization(ReportRevenue::class, 'value', $amount, ['key' => $key]);
    }

    /**
     * @param  string  $key
     */
    public function storeReportRevenue(string $key)
    {
        ReportRevenue::query()->firstOrCreate(
            ['key' => $key, 'organization_id' => getOrganization(), 'time' => date('Y')]
        );
    }
}
