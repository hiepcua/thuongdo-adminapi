<?php


namespace App\Services;


use App\Constants\ComplainConstant;
use App\Constants\CustomerConstant;
use App\Constants\DeliveryConstant;
use App\Constants\FineConstant;
use App\Constants\LocateConstant;
use App\Constants\OrderConstant;
use App\Constants\PackageConstant;
use App\Constants\RoleConstant;
use App\Constants\ViaConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\PaginateHelper;
use App\Http\Resources\Customer\InfoDeliveryResource;
use App\Http\Resources\CustomerDelivery\CustomerDeliveryResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\Note\NoteResource;
use App\Http\Resources\OnlyIdNameResource;
use App\Http\Resources\OnlyValueKeyResource;
use App\Http\Resources\TransporterResource;
use App\Http\Resources\Warehouse\WarehouseListResource;
use App\Models\Category;
use App\Models\ComplainType;
use App\Models\Customer;
use App\Models\CustomerReasonInactive;
use App\Models\Delivery;
use App\Models\DeliveryNote;
use App\Models\Fine;
use App\Models\Label;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\ReportLevel;
use App\Models\Solution;
use App\Models\Supplier;
use App\Models\Transporter;
use App\Models\TransporterDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class CommonService extends BaseService
{
    private StaffService $_staffService;

    public function __construct()
    {
        $this->_staffService = new StaffService();
        parent::__construct();
    }

    /**
     * @return array
     */
    public function getCategoriesCustomer(): array
    {
        $data['service'] = new OnlyValueKeyResource(CustomerConstant::CUSTOMER_SERVICE);
        $data['level'] = ReportLevel::query()->where('organization_id', request()->input('organization_id'))->select(
            'level',
            'name',
            'quantity'
        )->orderBy('level')->get();
        $data['label'] = Label::query()->limit(PaginateHelper::getLimit())->select('id', 'name')->get()->toArray();
        $data['provinces'] = (new LocateService())->getProvincesByCountry(LocateConstant::COUNTRY_VI);
        $data['reasons'] = CustomerReasonInactive::query()->select('id', 'name')->get();
        $this->processData($data);
        return $data;
    }

    /**
     * @return array
     */
    public function getCategoriesOrder(): array
    {
        $data['report'] = [
            'all' => $this->getOrderReportAll(),
            'time' => (new ReportService())->reports(Order::class),
            'amount' => $amount = AccountingHelper::getCosts(Order::query()->where('status', OrderConstant::KEY_STATUS_DEPOSITED)->sum('deposit_cost')),
            'amount_cny' => AccountingHelper::getCosts($amount / (new ConfigService())->getExchangeRate())
        ];
        $levels = CustomerConstant::CUSTOMER_LEVEL;
        $levels = new OnlyValueKeyResource($levels);
        $levels->offsetUnset(0);
        $data['level'] = $levels;
        $data['ecommerce'] = new OnlyValueKeyResource(OrderConstant::ECOMMERCE);
        $data['sorts'] = [
            ['name' => 'Thời gian', 'value' => 'created_at_sort', 'data' => new OnlyValueKeyResource(OrderConstant::SORT_TIME)],
            ['name' => 'Giá trị', 'value' => 'order_cost_sort', 'data' => new OnlyValueKeyResource(OrderConstant::SORT_COST)],
        ];
        $data['is_website'] = new OnlyValueKeyResource(OrderConstant::TOOL);
        $data['is_purchase'] = new OnlyValueKeyResource(OrderConstant::ORDER_TYPES);
        $data['is_tax'] = new OnlyValueKeyResource(OrderConstant::TAX);
        $data['statuses'] = new OnlyValueKeyResource(OrderConstant::STATUSES);
        $this->processData($data);
        return $data;
    }

    private function processData(array &$data)
    {
        $data['via'] = new OnlyValueKeyResource(ViaConstant::STATUSES);
        $data['warehouses'] = $this->getWarehouseByCountry();
        $data['business_type'] = new OnlyValueKeyResource(CustomerConstant::CUSTOMER_BUSINESS_TYPE);
        $data['staffs'] = $this->getStaffs();
    }

    /**
     * @param  ?string  $country
     * @return WarehouseListResource
     */
    private function getWarehouseByCountry(?string $country = LocateConstant::COUNTRY_VI): WarehouseListResource
    {
        return new WarehouseListResource(
            (new WarehouseService())->getWarehousesCountry($country)
        );
    }

    public function getListCategoriesFine(): array
    {
        $data['reports'] = $this->addReportAll($this->getReportsHasQuantity('report_fines', FineConstant::class));
        $data['types'] = new OnlyValueKeyResource(FineConstant::TYPES);
        $data['users'] = new ListResource($this->getUsers('user_id'), OnlyIdNameResource::class);
        $data['causes'] = new ListResource($this->getUsers('cause_id'), OnlyIdNameResource::class);
        return $data;
    }

    public function getCategoriesPackages(): array
    {
        $data['categories'] = new ListResource(Category::query()->get(), OnlyIdNameResource::class);
        $data['suppliers'] = new ListResource(Supplier::query()->get(), OnlyIdNameResource::class);
        $data['transporters'] = new ListResource(
            Transporter::query()->where('country', LocateConstant::COUNTRY_CN)->get(), OnlyIdNameResource::class
        );
        $data['types'] = new OnlyValueKeyResource(PackageConstant::TYPES);
        $data['order'] = [
            'statuses' => new OnlyValueKeyResource(OrderConstant::STATUSES),
            'delivery_types' => new OnlyValueKeyResource(OrderConstant::DELIVERIES_TEXT),
            'ecommerce' => new OnlyValueKeyResource(OrderConstant::ECOMMERCE),
            'categories' => new ListResource(Category::query()->get(), OnlyIdNameResource::class)
        ];
        $data['reports'] = [
            'orders' => $this->getOrderReportAll(),
            'time' => (new ReportService())->reports(OrderPackage::class),
            'debt_cost' => OrderPackage::query()->where('status', '!=', PackageConstant::STATUS_RECEIVED_GOODS)->where('status', '!=', PackageConstant::STATUS_CANCEL)->get()->sum('amount')
        ];
        $data['warehouses'] = [ 'vi' => $this->getWarehouseByCountry() , 'cn' => $this->getWarehouseByCountry(LocateConstant::COUNTRY_CN)];
        $data['modifies'] = new OnlyValueKeyResource(PackageConstant::MODIFIES);
        $data['requests'] = new OnlyValueKeyResource(PackageConstant::REQUESTS);
        $data['status'] = new OnlyValueKeyResource(PackageConstant::STATUSES_FILTER);
        $data['statuses'] =  new OnlyValueKeyResource(PackageConstant::STATUSES);
        $data['staffs'] = $this->getStaffs();
        $data['units'] = new OnlyValueKeyResource(PackageConstant::UNITS);
        return $data;
    }

    private function getOrderReportAll()
    {
        return $this->addReportAll($this->getReportsHasQuantity('report_organization_orders', OrderConstant::class));
    }

    private function getUsers(string $column)
    {
        $relation = $column == 'user_id' ? 'staff' : 'cause';
        return Fine::query()->groupBy($column)->with("$relation:id,name")->get()->map(
            function ($fine) use ($relation) {
                return optional($fine->{$relation});
            }
        )->filter(fn($item) => !!$item->id);
    }

    private function addReportAll($array)
    {
        array_unshift(
            $array,
            [
                'value' => '',
                'quantity' => array_sum(Arr::pluck($array, '_quantity')),
                'name' => 'Tất cả',
                'color' => '#45B4CE'
            ]
        );
        return $array;
    }

    /**
     * @return array
     */
    private function getStaffs(): array
    {
        return [
            'care' => $this->_staffService->getStaffHasPermission(RoleConstant::PERMISSION_TAKE_CARE_CUSTOMER),
            'counselor' => $this->_staffService->getStaffHasPermission(RoleConstant::PERMISSION_CUSTOMER_CONSULTING),
            'quote' => $this->_staffService->getStaffHasPermission(RoleConstant::PERMISSION_QUOTE_CUSTOMER),
            'order' => $this->_staffService->getStaffHasPermission(RoleConstant::PERMISSION_ORDER_STAFF),
            'complain' => $this->_staffService->getStaffHasPermission(RoleConstant::PERMISSION_COMPLAIN_STAFF)
        ];
    }

    /**
     * @param  string  $customerId
     * @param  string|null  $deliveryId
     * @return array
     */
    public function getCategoriesDelivery(?string $customerId, ?string $deliveryId = null): array
    {
        return [
            'reports' => [
                'all' => $this->addReportAll(
                    $this->getReportsHasQuantity('report_deliveries', DeliveryConstant::class)
                ),
                'time' => (new ReportService())->reports(Delivery::class),
            ],
            'customer' => $customerId ? new InfoDeliveryResource(Customer::query()->findOrFail($customerId)) : [],
            'payments' => new OnlyValueKeyResource(DeliveryConstant::PAYMENTS),
            'transporters' => new ListResource(Transporter::query()->where('country', LocateConstant::COUNTRY_VI)->get(), TransporterResource::class),
            'transporters_detail' => TransporterDetail::query()->select('id', 'name', 'phone_number')->get(),
            'customer_deliveries' => $customerId ? new ListResource(
                (new CustomerDeliveryService())->getListByCustomerId($customerId), CustomerDeliveryResource::class
            ) : [],
            'notes' => $deliveryId ? [
                'data' => $data = new ListResource(
                    DeliveryNote::query()->where('delivery_id', $deliveryId)->get(),
                    NoteResource::class
                ),
                'quantity' => $data->count()
            ] : [],
            'status_package' => new OnlyValueKeyResource(PackageConstant::DELIVERIES),
            'status_payment' => new OnlyValueKeyResource(DeliveryConstant::STATUSES_PAYMENT),
            'warehouses' => $this->getWarehouseByCountry(),
            'statuses' => new OnlyValueKeyResource(DeliveryConstant::STATUSES)
        ];
    }

    /**
     * @return OnlyValueKeyResource[]
     */
    public function getCategoriesComplain(): array
    {
        return [
            'statuses' => new OnlyValueKeyResource(ComplainConstant::STATUSES),
            'solution' => Solution::query()->select('id', 'name')->get(),
            'complain_type' => ComplainType::query()->select('id', 'name')->get(),
            'staffs' => $this->getStaffs(),
            'note_types' => new OnlyValueKeyResource(ComplainConstant::NOTE_TYPES),
            'report' =>[
                'all' => $this->addReportAll(
                    $this->getReportsHasQuantity('report_organization_complain', ComplainConstant::class)
                ),
            ]
        ];
    }
}