<?php


namespace App\Services;


use App\Http\Resources\ListResource;
use App\Http\Resources\PaginateJsonResource;
use App\Http\Resources\ReportStatusResource;
use App\Http\Resources\Resource;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BaseService implements Service
{
    public ?Model $_model;
    protected string $_paginateResource = PaginateJsonResource::class;
    protected string $_listResource = ListResource::class;
    protected string $_resource = Resource::class;
    protected array $_relations = [];


    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Danh sách bản ghi giới hạn 1000 bản ghi khi trả về
     * @param int|null $limit
     * @param mixed ...$params
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        [$limit] = func_get_args();
        return resSuccessWithinData(
            new $this->_listResource(
                $this->_model->newQuery()->with($this->_relations)->limit($limit)->get(),
                $this->_resource
            )
        );
    }

    /**
     * Phân trang
     * @param int $perPage
     * @return JsonResponse
     * @throws Exception
     */
    public function pagination(int $perPage): JsonResponse
    {
        $this->throwModel();
        $data = $this->_model->newQuery()->paginate($perPage);
        return resSuccessWithinData(new $this->_paginateResource($data, $this->_resource));
    }

    /**
     * Tạo mới bản ghi
     * @param array $data
     * @return Builder|Model|JsonResponse
     * @throws Exception
     */
    public function store(array $data)
    {
        $this->throwModel();
        return $this->_model->newQuery()->create($data);
    }

    /**
     * Cập nhật bản ghi theo ID
     * @param  string  $id
     * @param  array  $data
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     * @throws Exception
     */
    public function update(string $id, array $data)
    {
        $this->throwModel();
        $record = $this->_model->newQuery()->findOrFail($id);
        $record->update($data);
        return $record;
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $this->_model->newQuery()->findOrFail($id)->delete();
        return resSuccess(trans('system.deleted'));
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function detail(string $id): JsonResponse
    {
        return resSuccessWithinData(new $this->_resource($this->_model->newQuery()->findOrFail($id)));
    }

    /**
     * Khởi tạo model
     */
    private function setModel()
    {
        $namespaceModel = 'App\Models\\' . str_replace('Service', '', class_basename($this));
        $this->_model = class_exists($namespaceModel) ? new $namespaceModel() : null;
    }

    /**
     * Kiểm tra lại model
     * @throws Exception
     */
    protected function throwModel()
    {
        if (!$this->_model) {
            throw new Exception(trans('system.model_not_init'));
        }
    }

    /**
     * @param  string  $table
     * @param  string  $class
     * @return array
     */
    public function getReportsHasQuantity(string $table, string $class): array
    {
        $reports = (array)DB::table($table)->select(
            array_keys($class::STATUSES)
        )->first();
        $data = [];
        foreach ($reports as $key => $report) {
            $data[] = new ReportStatusResource($key, $class::STATUSES, $class::STATUSES_COLOR, $report);
        }
        return $data;
    }

    /**
     * @param  string  $table
     * @param  string  $class
     * @param  string  $status
     * @param  string|null  $customerId
     * @return ReportStatusResource|array
     */
    public function getReportHasQuantity(string $table, string $class, string $status, ?string $customerId)
    {
        $reports = DB::table($table);
        if($customerId) $reports = $reports->where('customer_id', $customerId);
        $reports = (array)$reports->select($status)->first();
        $data = [];
        foreach ($reports as $key => $report) {
            $data = new ReportStatusResource($key, $class::STATUSES, $class::STATUSES_COLOR, $report);
        }
        return $data;
    }
}
