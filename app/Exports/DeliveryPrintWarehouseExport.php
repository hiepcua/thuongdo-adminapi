<?php

namespace App\Exports;

use App\Models\Delivery;
use App\Models\DeliveryOrder;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Services\OrderPackageService;

class DeliveryPrintWarehouseExport implements FromView, WithEvents, WithDrawings
{
    use Exportable;

    private Delivery $_delivery;
    private int $_count;


    public function __construct(Delivery $delivery)
    {
        $this->_delivery = $delivery;
        $this->_count = 40;
    }

    public function view(): View
    {
        $receiver = $this->_delivery->customerDelivery;
        $warehouse = optional($this->_delivery->warehouse);
        $deliveryOrders = DeliveryOrder::query()->where('delivery_id', $this->_delivery->id)->groupBy('order_id')->get();
        $type = optional($deliveryOrders->first())->order_type == Order::class ? 'Order' : 'Ký gửi';
        $orders = [];
        $this->_count += $this->_delivery->orderPackages->count();
        foreach ($deliveryOrders as $deliveryOrder) {
            $order = $deliveryOrder->order;
            $order->isLatest = (new OrderPackageService())->checkPackageIsLatestByOrder($order->id, $this->_delivery->orderPackages->pluck('id')->all());
        
            $orders[] = $order;
            $this->_count++;
        }
        return view(
            'exports.delivery.warehouse',
            [
                'packages' => $this->_delivery->orderPackages,
                'receiver' => $receiver,
                'type' => $type,
                'warehouse' => $warehouse,
                'orders' => $orders,
                'organization' => optional($this->_delivery)->organization,
                'no' => $this->_delivery->no
            ]
        );
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('A1:N'.$this->_count)
                    ->getFont()
                    ->setName('Roboto');
                $event->sheet->getDelegate()->getStyle('A1:N'.$this->_count)
                    ->getFont()
                    ->setName('Roboto')
                    ->setSize(10);
            },
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('ThuongDo');
        $drawing->setDescription('Thuong Do');
        $drawing->setPath(public_path('/storage/organization/thuongdo.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A3');

        return $drawing;
    }
}
