<?php


namespace App\Services;


use App\Constants\CustomerConstant;
use App\Constants\TimeConstant;
use App\Constants\ViaConstant;
use App\Helpers\RandomHelper;
use App\Helpers\StatusHelper;
use App\Http\Resources\Customer\CustomerListResource;
use App\Http\Resources\Customer\CustomerPaginateResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Models\Customer;
use App\Models\CustomerOffer;
use App\Models\ReportCustomer;
use App\Models\ReportUserCounselingCustomer;
use App\Models\ReportUserOrderedCustomer;
use App\Models\ReportUserQuotationCustomer;
use App\Models\ReportUserTakeCareCustomer;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerService extends BaseService
{
    protected string $_paginateResource = CustomerPaginateResource::class;
    protected string $_listResource = CustomerListResource::class;
    protected string $_resource = CustomerResource::class;

    /**
     *  Lấy số lượng khách hàng theo tháng đã đăng ký
     * @return int
     */
    public function getCustomerNumberByYearAndMonth(): int
    {
        $code = optional(Customer::query()->whereMonth('created_at', date('m'))->whereYear(
            'created_at',
            date('Y')
        )->orderByDesc('code')->first())->code;
        return (int) ($code ? substr(filter_var($code, FILTER_SANITIZE_NUMBER_INT), 6) : 1);
    }

    public function updateCode(Customer $customer)
    {
        if ($customer->code) {
            return;
        }
        $customer->code = RandomHelper::customerCode();
        $customer->save();
    }

    /**
     * @param string $email
     * @return Builder|Model
     */
    public function getCustomerByEmail(string $email)
    {
        return Customer::query()->withoutGlobalScope(OrganizationScope::class)->where('email', $email)->select(
            'id',
            'code',
            'name',
            'email',
            'password',
            'organization_id',
            'status'
        )->firstOrFail();
    }

    /**
     * @return Builder|Model
     */
    public function getCustomerTest()
    {
        return $this->getCustomerByEmail(CustomerConstant::CUSTOMER_TEST);
    }

    /**
     * @return array
     */
    public function getVia(): array
    {
        $via = ViaConstant::STATUSES;
        $data = [];
        foreach (ViaConstant::STATUSES as $key => $via) {
            $data[] = StatusHelper::getInfo($key, ViaConstant::class);
        }
        return $data;
    }

    /**
     * @return array
     */
    public function reports(): array
    {
        $column = 'created_at';
        $query = Customer::query();
        return [
            'today' => (clone $query)->whereDate($column, date(TimeConstant::DATE))->count(),
            'week' => (clone $query)->whereBetween(
                $column,
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
            )->count(),
            'month' => Customer::query()->whereMonth($column, date('m'))->whereYear($column, date('Y'))->count()
        ];
    }

    public function store(array $data): JsonResponse
    {
        $record = DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $data['staff_counselor_id'] = $data['staff_counselor_id'] ?? $this->getStaff(
                    ReportUserCounselingCustomer::class
                );
            $customer = Customer::query()->create($data);
            if (isset($data['customer_delivery']) && count($data['customer_delivery']) > 0) {
                (new CustomerDeliveryService())
                    ->storeMultiRecord(optional($customer)->id, $data['customer_delivery']);
            }
            if (isset($data['customer_offer'])) {
                $data['customer_offer']['customer_id'] = optional($customer)->id;
                CustomerOffer::query()->create($data['customer_offer']);
            }
            ReportCustomer::query()->create(['customer_id' => optional($customer)->id]);
            return $customer;
        });
        return resSuccessWithinData(new $this->_resource($record));
    }

    public function update($id, array $data)
    {
        $data = (new UserService())->updatePassword($data);
        return parent::update($id, $data);
    }

    /**
     * Lấy số tiền trong ví
     * @param  string|null  $customerId
     * @return float
     */
    public function getBalanceAmount(?string $customerId = null): float
    {
        return optional(
                (new ReportCustomerService())->getReportCustomerCurrent($customerId)
            )->balance_amount ?? 0;
    }

    public function getStaff(string $model): ?string
    {
        $report = (new $model)::query()->where('status', 1)->orderBy('quantity')->first();
        if(!$report) return null;
        $report->increment('quantity');
        return $report->user_id;
    }

    /**
     * @param  string  $id
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function getCustomerById(string $id)
    {
        return Customer::query()->find($id);
    }

    /**
     * @param  string  $id
     * @return int
     */
    public function getCustomerLevelById(string $id): int
    {
        return optional($this->getCustomerById($id))->level ?? 0;
    }
}
